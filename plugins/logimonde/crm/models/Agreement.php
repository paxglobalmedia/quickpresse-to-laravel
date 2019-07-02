<?php namespace Logimonde\Crm\Models;

use Model;

/**
 * Agreement Model
 */
class Agreement extends Model
{
    public $connection = 'crm';
    /**
     * @var string The database table used by the model.
     */
    public $table = 'logimonde_crm_agreements';

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
        'items' => [
            'Logimonde\Crm\Models\ItemAgreement',
            'scope' => 'isQuickpresse'
        ],
    ];
    public $belongsTo = [
        'company' => ['Logimonde\Crm\Models\Company'],
    ];
    public $belongsToMany = [
        'subaccounts' => [
            'Logimonde\Crm\Models\Subaccount',
            'table' => 'logimonde_crm_agreement_subaccount',
            'order' => 'name'],
    ];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    public function scopeIsPublished($query)
    {
        return $query
            ->whereNotNull('published')
            ->where('published', true);
    }
}
