<?php


namespace App\scriptPHPautomation;


/**
 * Class toMoveFileClass
 * @package App\scriptPHPautomation
 */
class toMoveFileClass
{
   public function toMovePhPclass($file, $to){

       $source_file = $file;
       $destination_path = $to.'/';
       return rename($source_file, $destination_path . pathinfo($source_file, PATHINFO_BASENAME));
   }
}