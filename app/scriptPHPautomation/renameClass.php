<?php


namespace App\scriptPHPautomation;


class renameClass
{
    public function toRenameClass($currentname, $newName){

      return  rename($currentname, $newName);
    }
}