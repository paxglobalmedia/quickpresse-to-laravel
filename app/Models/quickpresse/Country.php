<?php namespace Logimonde\Quickpresse\Models;

use Model;

/**
 * Country Model
 */
class Country extends Model
{

    public $connection = 'logimonde';
    /**
     * @var string The database table used by the model.
     */
    public $table = 'country';
    protected $primaryKey = 'idPays';
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
    public $attachOne = [];
    public $attachMany = [];

    protected static $nameList = null;

    public static function getNameList()
    {
        if (self::$nameList) {
            return self::$nameList;
        }

        return self::$nameList = self::orderBy('show_order', 'asc')->lists('nom_en', 'idPays');
    }

}