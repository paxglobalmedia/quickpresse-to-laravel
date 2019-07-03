<?php namespace Logimonde\Crm\Models;

use Model;

/**
 * Subaccount Model
 */
class Subaccount extends Model
{
    public $connection = 'crm';
    /**
     * @var string The database table used by the model.
     */
    public $table = 'logimonde_clients_subaccounts';

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
        'company' => ['Logimonde\Crm\Models\Company'],
    ];
    public $belongsToMany = [
        'contract' => ['Logimonde\Crm\Models\Contract'],
        'agreement' => ['Logimonde\Crm\Models\Agreement']
    ];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];
}
