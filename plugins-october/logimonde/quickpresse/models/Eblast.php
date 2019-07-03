<?php namespace Logimonde\Quickpresse\Models;

use Model;

/**
 * Eblast Model
 */
class Eblast extends Model
{
    /**
     * @var string The database table used by the model.
     */
    public $table = 'logimonde_quickpresse_eblasts';

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
        'contents' => ['Logimonde\Quickpresse\Models\EblastContent'],
        'sends' => ['Logimonde\Quickpresse\Models\EblastSend', 'order' => 'date_required asc',],
    ];
    public $belongsTo = [
        'contract' => ['Logimonde\Crm\Models\Contract', 'order' => 'title asc'],
        'agreement' => ['Logimonde\Crm\Models\Agreement', 'order' => 'title asc'],
        'subaccount' => ['Logimonde\Crm\Models\Subaccount', 'order' => 'name asc'],
        'template' => ['Logimonde\Quickpresse\Models\Template'],
        'company' => ['Logimonde\Crm\Models\Company', 'order' => 'Name asc'],
        'product' => ['Logimonde\Crm\Models\Product'],
        'custom_list' => ['Logimonde\Quickpresse\Models\CustomList'],
        'user' => ['RainLab\User\Models\User'],
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    public static $allowedSortingOptions = array(
        'created_at asc' => 'Created (ascending)',
        'created_at desc' => 'Created (descending)',
        'updated_at asc' => 'Updated (ascending)',
        'updated_at desc' => 'Updated (descending)',
        'random' => 'Random'
    );

    public function getContentsCountAttribute()
    {
        return $this->contents()->count();
    }

    public function getPageCountAttribute()
    {
        $pages = $this->contents()->where('block', 'like', '%block-flyer%')->get();
        return count($pages);
    }

    public function getTotalMailingAttribute()
    {
        $sends = $this->sends()->get();
        return count($sends);
    }

    public function getClicksTotalAttribute()
    {
        return $this->contents()->sum('clicks');
    }

    public function scopeIsActive($query)
    {
        return $query
            ->whereNotNull('active')
            ->where('active', true);
    }
}
