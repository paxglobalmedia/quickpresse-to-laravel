<?php


namespace App\scriptPHPautomation;


class detectIfileExist
{
    public function detectFile(){

        $file_pointer = '/user01/work/gfg.txt';

        if (file_exists($file_pointer)) {
            echo "The file $file_pointer exists";
        }else {
            echo "The file $file_pointer does not exists";
        }
    }
}