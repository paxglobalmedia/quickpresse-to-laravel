<?php namespace Logimonde\Crm\Models;

use Model;

/**
 * Customer Model
 */
class Company extends Model
{
    public $connection = 'crm';
    /**
     * @var string The database table used by the model.
     */
    public $table = 'logimonde_clients_companies';

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
    public $hasMany = [
        'subaccounts' => ['Logimonde\Crm\Models\Subaccount', 'order' => 'name asc',],
    ];
    public $belongsTo = [];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    public function scopeIsActive($query)
    {
        return $query
            ->whereNotNull('active')
            ->where('active', true);
    }
}
