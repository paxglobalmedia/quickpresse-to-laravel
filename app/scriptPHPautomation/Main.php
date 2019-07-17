<?php

use App\scriptPHPautomation\CheckIfFolderExist;
use App\scriptPHPautomation\detectIfileExist;
use App\scriptPHPautomation\getCurrentPath;
use App\scriptPHPautomation\getLineNumber
use App\scriptPHPautomation\renameClass ;
use App\scriptPHPautomation\searchClassDeclaration;
use App\scriptPHPautomation\toMoveFileClass;
use App\scriptPHPautomation\toRenameNameSpace;


namespace App\scriptPHPautomation;


/**
 * Class Main to automate move dependecy class use by plugin from OctoberCMS to Laravel
 * @package App\scriptPHPautomation
 */
class Main
{
    /**
     * Main constructor.
     */
    public function __construct($pluginDirectoryName, $pluginName)
    {
        // The developer insert the plugin directory name in popup form
        $this->pluginDirectoryName = $pluginDirectoryName;

        // The developer insert the plugin name in popup form
        $this->pluginName = $pluginName;

        // standard october path;
        $this->octoberpluginPath = '/plugins';

        // The full current plugin to convert to laravel
        $this->pluginFullPath = $this->octoberpluginPath.'/'.$this->pluginDirectoryName.'/'.$this->pluginName;
    }

    /**
     * @param $pluginDirectoryName
     * @param $pluginName
     */
    public function automation(){

        //Create Folder Class in App/$PluginName/
        new CheckIfFolderExist('app/'.$this->pluginName.'/class');

        //Create The Folder of the plugin in Laravel if not exist
       $this->checkFolderExistence($this->pluginFullPath);

        // Search Class Declaration and move it to App/$pluginNAme/Class
        //Get The full dependency class
        $this->findDepencyClass($className);

    }

    /**
     * @param $pluginFullPath
     * @return CheckIfFolderExist
     */
    public function checkFolderExistence($pluginFullPath){

        $checkFolder = new CheckIfFolderExist($pluginFullPath);
        return $checkFolder ;
    }

    /**
     * @param $className
     * @return searchClassDeclaration
     */
    public function findDepencyClass($className) {

        $dependency = new searchClassDeclaration($className);
        return $dependency;
    }

}