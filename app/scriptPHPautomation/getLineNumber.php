<?php

namespace App\scriptPHPautomation;


class getLineNumber
{
    public function obtainLineNumber($fileName) {

        $file = new SplFileObject( $fileName);
        foreach ($file as $line) {
            echo $file->key() . ". " . $line;
        }

    }
}