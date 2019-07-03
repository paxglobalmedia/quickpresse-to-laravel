<?php namespace Logimonde\QuickPresse\Models;

use Model;

/**
 * bounce Model
 */
class Bounce extends Model
{
    /**
     * @var string The database table used by the model.
     */
    public $table = 'logimonde_quickpresse_bounces';

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
        'code' => 'Logimonde\QuickPresse\Models\Code',
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    public static $allowedSortingOptions = array(
        'email asc' => 'Email(ascending)',
        'email desc' => 'Email (descending)',
        'error_count asc' => 'Error Count (ascending)',
        'error_count desc' => 'Error Count (descending)',
        'code_id asc' => 'Code (ascending)',
        'code_id desc' => 'Code (descending)',
        'created_at asc' => 'Created (ascending)',
        'created_at desc' => 'Created (descending)',
        'bounce_date asc' => 'Bounce Date (ascending)',
        'bounce_date desc' => 'Bounce Date (descending)',
    );

    public function scopeListBounces($query, $options)
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
            'screen' => true,
        ], $options));

        $searchableFields = ['email', 'code_error', 'status'];

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
}
