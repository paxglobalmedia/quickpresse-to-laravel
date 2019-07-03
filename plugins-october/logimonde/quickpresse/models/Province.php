<?php namespace Logimonde\Quickpresse\Models;

use Model;

/**
 * Province Model
 */
class Province extends Model
{

    public $connection = 'logimonde';
    /**
     * @var string The database table used by the model.
     */
    public $table = 'province';

    protected $primaryKey = 'idProvince';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = ['idPays'];

    public $belongsTo = [
        'country' => ['Logimonde\Quickpresse\Models\Country', 'key' => 'idPays', 'otherKey' => 'idPays']
    ];

    protected static $nameList = [];

    public static function getNameList($countryId)
    {
        if (isset(self::$nameList[$countryId])) {
            return self::$nameList[$countryId];
        }

        return self::$nameList[$countryId] = self::where('idPays', $countryId)
            ->orderBy('nom_en', 'asc')
            ->lists('nom_en', 'idProvince');
    }

}