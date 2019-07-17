<?php


namespace App\scriptPHPautomation;


/**
 * Class CheckIfFolderExist
 * Check if folder exist
 * @package App\scriptPHPautomation
 */
class CheckIfFolderExist
{
    /**
     * @param $path
     * $path = path/to/directory
     */
    public function IfFolderExist($path){

        $this->path = $path;
        if (!file_exists($this->path)) {
            mkdir($path, 0777, true);
        }

    }
}