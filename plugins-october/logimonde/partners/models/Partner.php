<?php namespace Logimonde\Partners\Models;

use Model;

/**
 * Partner Model
 */
class Partner extends Model
{

    /**
     * @var string The database table used by the model.
     */
    public $table = 'logimonde_partners_partners';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    public $translatable = ['name'];

    public static function boot()
    {
        // Call default functionality (required)
        parent::boot();

        // Check the translate plugin is installed
        if (!class_exists('RainLab\Translate\Behaviors\TranslatableModel'))
            return;

        // Extend the constructor of the model
        self::extend(function($model){
            // Implement the translatable behavior
            $model->implement[] = 'RainLab.Translate.Behaviors.TranslatableModel';
        });
    }
}