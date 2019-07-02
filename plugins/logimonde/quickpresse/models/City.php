<?php namespace Logimonde\Quickpresse\Models;

use Model;

/**
 * City Model
 */
class City extends Model
{
    public $connection = 'logimonde';
    /**
     * @var string The database table used by the model.
     */
    public $table = 'city';

    protected $primaryKey = 'idVille';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

    public $belongsTo = [
        'state' => ['Logimonde\Quickpresse\Models\Province']
    ];

    protected static $nameList = [];

    public static function getNameList($stateId)
    {
        if (isset(self::$nameList[$stateId])) {
            return self::$nameList[$stateId];
        }

        return self::$nameList[$stateId] = self::where('idProvince', $stateId)
            ->orderBy('nom_en', 'asc')
            ->lists('nom_en', 'idVille');
    }

}