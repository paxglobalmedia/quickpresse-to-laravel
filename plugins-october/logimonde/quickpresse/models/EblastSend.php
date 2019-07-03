<?php namespace Logimonde\Quickpresse\Models;

use Model;

/**
 * EblastSend Model
 */
class EblastSend extends Model
{
    /**
     * @var string The database table used by the model.
     */
    public $table = 'logimonde_quickpresse_eblast_sends';

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
        'eblast' => ['Logimonde\Quickpresse\Models\Eblast'],
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    public static $allowedSortingOptions = array(
        'tit asc' => 'Created (ascending)',
        'created_at desc' => 'Created (descending)',
        'updated_at asc' => 'Updated (ascending)',
        'updated_at desc' => 'Updated (descending)',
        'date_required asc' => 'Date required (ascending)',
        'date_required desc' => 'Date required (descending)',
        'random' => 'Random'
    );

    public function scopeIsActive($query)
    {
        return $query
            ->whereNotNull('status')
            ->where('status', false);
    }

    public function scopeIsSend($query)
    {
        return $query
            ->whereNotNull('status')
            ->where('status', true);
    }

    public function scopeIsApproved($query)
    {
        return $query
            ->whereNotNull('approved')
            ->where('approved', true);
    }

    public function scopeIsDisplayed($query)
    {
        return $query
            ->whereNotNull('displayed')
            ->where('displayed', true);
    }

    public function statistics()
    {
        if ($this->approved === '1' && $this->status === '1') {
            $this->increment('views');

            $useragent = $_SERVER['HTTP_USER_AGENT'];
            $isMsoffice = strpos($useragent, 'MSOffice');

            if ($isMsoffice !== false) {
                $this->increment('outlook');
            }
            $this->save();
        }
    }

    public function shared()
    {
        if ($this->approved === '1' && $this->status === '1') {
            $this->increment('shared');

            $this->save();
        }
    }

}
