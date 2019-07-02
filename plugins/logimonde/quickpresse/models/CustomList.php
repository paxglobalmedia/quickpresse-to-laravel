<?php namespace Logimonde\Quickpresse\Models;

use Model;

/**
 * CustomList Model
 */
class CustomList extends Model
{
    /**
     * @var string The database table used by the model.
     */
    public $table = 'logimonde_quickpresse_custom_lists';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

    public static $allowedSortingOptions = array(
        'name asc' => 'Name (ascending)',
        'name desc' => 'Name (descending)',
        'created_at asc' => 'Created (ascending)',
        'created_at desc' => 'Created (descending)',
        'updated_at asc' => 'Updated (ascending)',
        'updated_at desc' => 'Updated (descending)',
    );

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [
        'contacts' => ['Logimonde\Quickpresse\Models\CustomContacts'],
    ];
    public $belongsTo = [
        'company' => ['Logimonde\Crm\Models\Company'],
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    public function getListCountAttribute()
    {
        return $this->contacts()->count();
    }

    public function scopeListCustomLists($query, $options)
    {
        /*
         * Default options
         */
        extract(array_merge([
            'page' => 1,
            'perPage' => 30,
            'sort' => 'created_at',
            'search' => '',
            'company' => null,
            'active' => true,
        ], $options));

        $searchableFields = ['name', 'email'];

        if ($active) {
            $query->isActive();
        }

        if ($company) {
            $query->where('company_id', $company);
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


        return $query->paginate($perPage, $page);

    }

    public function scopeIsActive($query)
    {
        return $query
            ->whereNotNull('active')
            ->where('active', true);
    }

    public function scopeHasContacts($query)
    {
        return $query->has('contacts');

    }

}
