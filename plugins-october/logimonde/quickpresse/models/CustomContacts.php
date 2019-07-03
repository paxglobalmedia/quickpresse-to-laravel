<?php namespace Logimonde\Quickpresse\Models;

use Model;

/**
 * CustomContacts Model
 */
class CustomContacts extends Model
{
    /**
     * @var string The database table used by the model.
     */
    public $table = 'logimonde_quickpresse_custom_contacts';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

    public static $allowedSortingOptions = array(
        'email asc' => 'E-mail (ascending)',
        'email desc' => 'E-mail (descending)',
        'active asc' => 'Active (ascending)',
        'active desc' => 'Active (descending)',
        'created_at asc' => 'Created (ascending)',
        'created_at desc' => 'Created (descending)',
        'updated_at asc' => 'Updated (ascending)',
        'updated_at desc' => 'Updated (descending)',
    );

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [
        'custom_list' => ['Logimonde\Quickpresse\Models\CustomList'],
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    public function scopeListCustomContacts($query, $options)
    {
        /*
         * Default options
         */
        extract(array_merge([
            'page' => 1,
            'perPage' => 30,
            'sort' => 'created_at',
            'search' => '',
            'list' => null,
        ], $options));

        $searchableFields = ['first_name', 'last_name', 'email', 'address', 'title'];


        if ($list) {
            $query->where('custom_list_id', $list);
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
}
