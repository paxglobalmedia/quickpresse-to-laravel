<?php namespace Logimonde\Crm\Models;

use Model;

/**
 * Contract Model
 */
class Contract extends Model
{
    public $connection = 'crm';
    /**
     * @var string The database table used by the model.
     */
    public $table = 'logimonde_crm_contracts';

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
            'Logimonde\Crm\Models\ItemContract',
            'scope' => 'isQuickpresse'
        ],
    ];
    public $belongsTo = [
        'company' => ['Logimonde\Crm\Models\Company'],
    ];
    public $belongsToMany = [
        'subaccounts' => [
            'Logimonde\Crm\Models\Subaccount',
            'table' => 'logimonde_crm_contract_subaccount',
            'order' => 'name'],
    ];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    public function getTotalItemsAttribute()
    {
        return $this->items()->where('qp_app', '1')->sum('quantity');
    }

    public function scopeIsPublished($query)
    {
        return $query
            ->whereNotNull('published')
            ->where('published', true);
    }

    public function scopeIsActive($query)
    {
        return $query->where('status', '1');
    }

    public function scopeIsQuickPresse($query)
    {
        return $query->whereHas('items', function ($query) {
            $query->where('qp_app', '1');
        });
    }

    public function scopeHasBalance($query)
    {
        return $query->where('balance', '>', '0');
    }

    public function scopeIsApproved($query)
    {
        return $query
            ->whereNotNull('approved')
            ->where('approved', true);
    }
}
