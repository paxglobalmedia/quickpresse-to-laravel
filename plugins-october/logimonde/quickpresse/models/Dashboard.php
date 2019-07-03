<?php namespace Logimonde\Quickpresse\Models;

use Model;

/**
 * Dashboard Model
 */
class Dashboard extends Model
{
    /**
     * @var string The database table used by the model.
     */
    public $table = 'logimonde_quickpresse_dashboards';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

    /**
     * @var array Relations
     */

    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [
        'eblast' => ['Logimonde\Quickpresse\Models\Eblast'],
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    public static $allowedSortingOptions = array(
        'date_requested asc' => 'Send date(ascending)',
        'date_requested desc' => 'Send date (descending)',
        'company_name asc' => 'Company (ascending)',
        'company_name desc' => 'Company (descending)',
        'title asc' => 'Title (ascending)',
        'title desc' => 'Title (descending)',
        'created_at asc' => 'Created (ascending)',
        'created_at desc' => 'Created (descending)',
        'type_qp asc' => 'Type (ascending)',
        'type_qp desc' => 'Type (descending)',
    );

    public static $allowedCustomerSorting = array(
        'date_required asc' => 'Send date(ascending)',
        'date_required desc' => 'Send date (descending)',
        'title asc' => 'Title (ascending)',
        'title desc' => 'Title (descending)',
        'updated_at asc' => 'Updated (ascending)',
        'updated_at desc' => 'Updated (descending)',
    );

    public static $allowedStatsSorting = array(
        'date_requested asc' => 'Send date(ascending)',
        'date_requested desc' => 'Send date (descending)',
        'title asc' => 'Title (ascending)',
        'title desc' => 'Title (descending)',
    );

    public function scopeListEblasts($query, $options)
    {
        /*
         * Default options
         */
        extract(array_merge([
            'page' => 1,
            'perPage' => 30,
            'sort' => 'created_at',
            'search' => '',
            'filter' => null,
            'status' => false,
            'company' => false,
            'subaccount' => '',
            'startDate' => '',
            'endDate' => '',
            'plan' => null,
            'screen' => true,
        ], $options));

        $searchableFields = ['title', 'company_name', 'date_required',
            'product_name_en', 'product_name_fr', 'list_name'];

        if ($company) {
            $query->whereHas(
                'eblast', function ($q) use ($company) {
                $q->where('company_id', $company);
            });
        }

        if ($subaccount) {
            $query->whereHas(
                'eblast', function ($q) use ($subaccount) {
                $q->where('subaccount_id', $subaccount);
            });
        }

        if ($status) {
            $query->where('status', '1');
        } else {
            $query->where('status', '0');
        }

        if ($startDate != '') {
            $query->where('date_required', '>=', $startDate);
            $query->where('date_required', '<=', $endDate);
        }

        /*
         * Filter
         */
        if (!is_null($filter)) {
            $query->filterOptions($filter);
        }

        /*
         * Sorting
         */
        if (!is_array($sort)) {
            $sort = [$sort];
        }

        foreach ($sort as $_sort) {

            if (in_array($_sort, array_keys(self::$allowedSortingOptions))) {
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

        if ($screen) {
            return $query->paginate($perPage, $page);
        } else {
            return $query->get();
        }
    }

    public function scopeIsActive($query)
    {
        return $query
            ->whereNotNull('active')
            ->where('active', true);
    }

    public function scopeIsDraft($query)
    {
        return $query
            ->whereNotNull('active')
            ->where('active', false);
    }

    public function scopeIsSend($query)
    {
        return $query
            ->whereNotNull('status')
            ->where('status', true);
    }

    public function scopeIsApproved($query)
    {
        return $query
            ->whereNotNull('approved')
            ->where('approved', true);
    }

    public function scopeIsDisplayed($query)
    {
        return $query
            ->whereNotNull('displayed')
            ->where('displayed', true);
    }

    public function scopeFilterOptions($query, $option)
    {
        if ($option == 'today') {
            $query->isToday()->isActive();
        } else if ($option == 'new') {
            $query->isNew()->isActive();
        } else if ($option == 'active') {
            $query->isActive();
        } else if ($option == 'draft') {
            $query->isDraft();
        } else if ($option == 'contract') {
            $query->isContract();
        } else if ($option == 'qp_list') {
            $query->isList();
        } else if ($option == 'custom') {
            $query->isCustom();
        }
    }

    public function scopeIsToday($query)
    {
        return $query
            ->where('date_required', date('Y-m-d'));
    }

    public function scopeIsNew($query)
    {
        return $query
            ->whereNotNull('displayed')
            ->where('displayed', false);
    }

    public function scopeIsContract($query)
    {
        return $query
            ->where('type_qp', 'contract');
    }

    public function scopeIsList($query)
    {
        return $query
            ->where('type_qp', 'qp_list');
    }

    public function scopeIsCustom($query)
    {
        return $query
            ->where('type_qp', 'custom');
    }
}
