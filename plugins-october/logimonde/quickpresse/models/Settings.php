<?php namespace Logimonde\Quickpresse\Models;

use Model;

/**
 * Settings Model
 */
class Settings extends Model
{
    public $implement = ['System.Behaviors.SettingsModel'];

    // A unique code
    public $settingsCode = 'logimonde_quickpresse_settings';

    // Reference to field configuration
    public $settingsFields = 'fields.yaml';
}
