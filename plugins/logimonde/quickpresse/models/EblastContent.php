<?php namespace Logimonde\Quickpresse\Models;

use Model;

/**
 * EblastContent Model
 */
class EblastContent extends Model
{
    /**
     * @var string The database table used by the model.
     */
    public $table = 'logimonde_quickpresse_eblast_contents';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

    protected $jsonable = ['style_block'];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $belongsTo = [
        'eblast' => ['Logimonde\Quickpresse\Models\Eblast'],
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

}
