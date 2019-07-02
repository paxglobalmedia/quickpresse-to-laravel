<?php namespace Logimonde\QuickPresse\Models;

use Model;

/**
 * Library Model
 */
class Library extends Model
{
    /**
     * @var string The database table used by the model.
     */
    public $table = 'logimonde_quickpresse_libraries';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

    public static $allowedSortingOptions = array(
        'title asc' => 'Title (ascending)',
        'title desc' => 'Title (descending)',
        'created_at asc' => 'Created (ascending)',
        'created_at desc' => 'Created (descending)',
        'updated_at asc' => 'Oldest first',
        'updated_at desc' => 'Newest first',
        'company_id asc' => 'Company (ascending)',
        'size asc' => 'Smallest first',
        'size desc' => 'Largest first',
        'random' => 'Random'
    );

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [
        'company' => ['Logimonde\Crm\Models\Company'],
        'user' => ['RainLab\User\Models\User'],
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    public function scopeListImages($query, $options)
    {
        /*
         * Default options
         */
        extract(array_merge([
            'page' => 1,
            'perPage' => 30,
            'sort' => 'updated_at desc',
            'search' => '',
            'company' => null,
            'active' => true
        ], $options));

        $searchableFields = ['original_name', 'title', 'mime', 'size'];

        if ($active) {
            $query->isPublished();
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

    public function scopeIsPublished($query)
    {
        return $query
            ->whereNotNull('published')
            ->where('published', true);
    }
}
