<?php namespace Logimonde\Crm\Models;

use Model;

/**
 * ItemAgreement Model
 */
class ItemAgreement extends Model
{
    public $connection = 'crm';
    /**
     * @var string The database table used by the model.
     */
    public $table = 'logimonde_crm_item_agreements';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

    protected $jsonable = ['publication_dates', 'qp_destinations'];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [
        'agreement' => ['Logimonde\Crm\Models\Agreement'],
        'product' => ['Logimonde\Crm\Models\Product'],
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    public function scopeIsQuickpresse($query)
    {
        return $query->where('qp_app', '1');
    }
}
