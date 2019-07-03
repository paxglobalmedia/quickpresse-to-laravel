<?php namespace Logimonde\Slider\Models;

use Model;

/**
 * Slider Model
 */
class Slider extends Model
{

    /**
     * @var string The database table used by the model.
     */
    public $table = 'logimonde_slider_sliders';

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
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [
        'image' => ['System\Models\File', 'order' => 'sort_order'],
    ];
    public $attachMany = [

    ];

    public function getLanguageOptions()
    {
        return ['fr' => 'Francais', 'en' => 'English'];
    }

    public function getTargetOptions()
    {
        return ['_blank' => 'New page', '_self' => 'Same page'];
    }

    public function scopeShowSliders($query, $lang) {
        $query->where('language',$lang);
        $query->isPublished();
        $query->orderBy('sort', 'asc');

        return $query->get();
    }

    public function scopeIsPublished($query)
    {
        return $query
            ->whereNotNull('published')
            ->where('published', '=', 1);
    }

}