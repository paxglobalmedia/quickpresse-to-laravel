<?php namespace Logimonde\QuickPresse\Models;

use Model;

/**
 * Circulationstate Model
 */
class State extends Model
{

    /**
     * @var string The database table used by the model.
     */
    public $table = 'states';

    protected $primaryKey = 'idProvince';

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
    public $belongsTo = [];
    public $belongsToMany = [
        'plans' => ['Logimonde\Quickpresse\Models\Plan',
            'table' => 'logimonde_quickpresse_plan_states',
        ]
    ];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];
}
