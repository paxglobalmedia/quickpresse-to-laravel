<?php namespace Logimonde\Account\Traits;

use Auth;
use ApplicationException;
use Logimonde\QuickPresse\Models\Company;
use RainLab\User\Models\User;
use Logimonde\Crm\Models\Company as CrmCompany;

trait AccountUtils
{

    /**
     * Returns the logged in user, if available
     */
    public function user($id = null)
    {
        if ($id)
            return User::where('id', $id)->first();


        if (!Auth::check())
            return null;

        return Auth::getUser();
    }

    protected function getAccounts($company_id, $page = null)
    {
        $sort = isset($params['sort']) ? $params['sort'] : $this->property('sortOrder');
        $page = !is_null($page) ? $page : $this->property('pageNumber');

        $accounts = CrmCompany::listAccounts([
            'page' => $page,
            'sort' => $sort,
            'perPage' => $this->property('itemsPerPage'),
            'company' => $company_id
        ]);
        return $accounts;
    }

    private function getUsers($company = null)
    {
        $page = $this->pageValidation();

        $search = $this->page['search'] = input('search');
        $sort = isset($params['sort']) ? $params['sort'] : $this->property('sortOrder');
        $page = !is_null($page) ? $page : $this->property('pageNumber');
        $page = $search != '' ? null : $page;

        $users = User::listUsers([
            'page' => $page,
            'sort' => $sort,
            'perPage' => $this->property('itemsPerPage'),
            'search' => $search,
            'company' => $company
        ]);

        $options = '?search=' . $search;
        $options .= '&sort=' . $sort;
        $options .= '&lastPage=' . (input('lastPage') != '' ? input('lastPage') : $users->lastPage());
        $this->page['options'] = $options;

        return $users;
    }

    public function saveCompany()
    {
        $data = post();
        if (is_array($data) && $data['company_id'] != '') {
            $company = Company::whereId($data['company_id'])->first();
        } else {
            $company = new Company;
        }
        $company->name = $data['name'];
        $company->address = $data['address'];
        $company->zip = $data['zip'];
        $company->city_id = $data['city'];
        $company->state_id = $data['state'];
        $company->country_id = $data['country'];
        $company->phone = $data['phone'];
        $company->save();

        \Flash::success(\Lang::get('logimonde.account::lang.company.update'));

    }

    protected function uploadLogo($field, $id, $type)
    {
        $file = \Request::file($field);
        $rules = array($field => 'mimes:jpg,jpeg,png,gif|max:300');
        $valid = $this->validateUpload($field, $file, $rules);
        if (isset($valid['fails'])) {
            \Flash::error($valid['fails']);
            return false;
        }
        $company = $type == 'client' ? $id : $this->getSubAccountCompanyId($id);
        $uploadedFile = $this->processingLogoFile($file, $company, $type);
        $this->resizeImage($uploadedFile, '160', '160');
        $this->updateLogoModel($field, $uploadedFile, $id, $type);
        return true;
    }

    public function updateLogoModel($field, $uploadedFile, $id, $type)
    {
        if ($type == 'client') {
            $model = Company::whereId($id)->first();
        } else {
            $model = Account::whereId($id)->first();
        }
        $model->{$field} = $uploadedFile['filename'];
        $model->save();
    }

    public function onDeleteCompanyLogo()
    {
        if ($id = post('company_id')) {
            $company = Company::whereId($id)->first();
            if (post('logo_lang') == 'en') {
                $company->logo_file_en = '';
            } else if (post('logo_lang') == 'fr') {
                $company->logo_file_fr = '';
            }
            $company->save();
        }
    }

    public function onLoadSubAccountPagination()
    {
        $page = post('page');
        $company = post('company');

        $this->page['accounts'] = $this->getAccounts($company, $page);
    }

    protected function getSubAccountCompanyId($id)
    {
        $account = Account::whereId($id)->first();
        return $account->company_id;
    }

}