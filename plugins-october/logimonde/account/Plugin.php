<?php namespace Logimonde\Account;

use Yaml;
use File;
use Auth;
use Mail;
use Event;
use Backend;
use System\Classes\PluginBase;
use RainLab\User\Controllers\Users as UsersController;
use RainLab\User\Models\User as UserModel;
use Logimonde\Quickpresse\Models\Country;
use Logimonde\Quickpresse\Models\Province;
use Logimonde\Quickpresse\Models\City;

/**
 * Account Plugin Information File
 */
class Plugin extends PluginBase
{

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name' => 'Logimonde Account',
            'description' => 'No description provided yet...',
            'author' => 'Logimonde',
            'icon' => 'icon-user'
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Boot method, called right before the request route.
     *
     * @return array
     */
    public function boot()
    {
        $this->extendUserModel();
        $this->extendUserList();
        $this->extendUserController();
    }

    private function extendUserModel()
    {
        UserModel::extend(function ($model) {
            $model->belongsTo['country'] = [
                'Logimonde\Quickpresse\Models\Country',
            ];
            $model->belongsTo['city'] = [
                'Logimonde\Quickpresse\Models\City',
            ];
            $model->belongsTo['state'] = [
                'Logimonde\Quickpresse\Models\Province',
            ];
            $model->belongsTo['company'] = [
                'Logimonde\Crm\Models\Company',
            ];
            $model->addFillable([
                'company_id',
                'title',
                'phone',
                'mobile',
                'street_addr',
                'city_id',
                'country_id',
                'state_id',
                'language',
                'first_name',
                'last_name',
                'zip',
            ]);
            $model->addDynamicMethod('getCountryOptions', function ($query, $options) {
                return Country::getNameList();
            });
            $model->addDynamicMethod('getStateOptions', function ($query, $options) use ($model) {
                return Province::getNameList($model->country_id);
            });
            $model->addDynamicMethod('getCityOptions', function ($query, $options) use ($model) {
                return City::getNameList($model->state_id);
            });
            $model->addDynamicMethod('getLanguageOptions', function ($query, $options) {
                return ['en' => 'English', 'fr' => 'Français'];
            });

        });
    }

    private function extendUserList()
    {
        UserModel::extend(function ($model) {

            $model->addDynamicMethod('scopeListUsers', function ($query, $options) {
                /*
                 * Default options
                 */
                extract(array_merge([
                    'page' => 1,
                    'perPage' => 30,
                    'sort' => 'created_at',
                    'company' => null,
                    'search' => '',
                    'activated' => true
                ], $options));

                $allowedSortingOptions = [
                    'first_name asc' => 'First Name (ascending)',
                    'first_name desc' => 'First Name (descending)',
                    'last_name asc' => 'Last Name (ascending)',
                    'last_name desc' => 'Last Name (descending)',
                    'created_at asc' => 'Created (ascending)',
                    'created_at desc' => 'Created (descending)',
                    'updated_at asc' => 'Updated (ascending)',
                    'updated_at desc' => 'Updated (descending)',
                    'company_id asc' => 'Company (ascending)',
                    'random' => 'Random'
                ];
                $searchableFields = ['first_name', 'last_name', 'email', 'phone'];


                /*
                 * Sorting
                 */
                if (!is_array($sort)) {
                    $sort = [$sort];
                }

                foreach ($sort as $_sort) {

                    if (in_array($_sort, array_keys($allowedSortingOptions))) {
                        $parts = explode(' ', $_sort);
                        if (count($parts) < 2) {
                            array_push($parts, 'desc');
                        }
                        list($sortField, $sortDirection) = $parts;
                        if ($sortField == 'random') {
                            $sortField = DB::raw('RAND()');
                        }
                        $query->orderBy($sortField, $sortDirection);
                    }
                }

                /*
                 * Search
                 */
                $search = trim($search);
                if (strlen($search)) {
                    $query->searchWhere($search, $searchableFields);
                }

                /*
                 * Company
                 */
                if ($company !== null) {

                    $query->where('company_id', $company);
                }

                return $query->paginate($perPage, $page);

            });

        });
    }

    private function extendUserController()
    {

        UsersController::extendFormFields(function ($widget) {
            // Prevent extending of related form instead of the intended User form
            if (!$widget->model instanceof UserModel) {
                return;
            }

            $configFile = __DIR__ . '/config/profile_fields.yaml';
            $config = Yaml::parse(File::get($configFile));
            $widget->addTabFields($config);
        });

        UsersController::extendListColumns(function ($list, $model) {

            if (!$model instanceof UserModel)
                return;

            $list->addColumns([
                'is_activated' => [
                    'label' => 'Activated',
                    'type' => 'switch',
                ]
            ]);

        });
    }

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents()
    {
        return [
            'Logimonde\Account\Components\UserUpdate' => 'UserUpdate',
            'Logimonde\Account\Components\UserRegister' => 'UserRegister',
            'Logimonde\Account\Components\UserPasswordReset' => 'UserPasswordReset',
            'Logimonde\Account\Components\UserLogin' => 'UserLogin',
            'Logimonde\Account\Components\Users' => 'Users',
            'Logimonde\Account\Components\Companies' => 'Companies',
            'Logimonde\Account\Components\CompanyForm' => 'CompanyForm',
            'Logimonde\Account\Components\SubaccountForm' => 'SubaccountForm',
            'Logimonde\Account\Components\BasicRegister' => 'BasicRegister',
        ];
    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return []; // Remove this line to activate

    }

    /**
     * Registers back-end navigation items for this plugin.
     *
     * @return array
     */
    public function registerNavigation()
    {
        return []; // Remove this line to activate

    }

}
