<?php

namespace App\scriptPHPautomation;


/**
 * Class toRenameNameSpace
 * @package App\scriptPHPautomation
 */
class toRenameNameSpace
{

    /**
     * LIre une line de phrase et remplace cette ligne ( TO RENAME namespace)
     */
    public function renameNamespace()
    {
        $contents = file_get_contents($dir);
        $new_contents= "";
        if( strpos($contents, $id) !== false) { // if file contains ID
            $contents_array = preg_split("/\\r\\n|\\r|\\n/", $contents);
            foreach ($contents_array as &$record) {    // for each line
                if (strpos($record, $id) !== false) { // if we have found the correct line
                    pass; // we've found the line to delete - so don't add it to the new contents.
                }else{
                    $new_contents .= $record . "\r"; // not the correct line, so we keep it
                }
            }
            file_put_contents($dir, $new_contents); // save the records to the file
            echo json_encode("Successfully updated record!");
        }
        else{
            echo json_encode("failed - user ID ". $id ." doesn't exist!");
        }

    }
}