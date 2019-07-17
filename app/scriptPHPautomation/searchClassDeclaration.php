<?php


namespace App\scriptPHPautomation;


/**
 * Class searchClassDeclaration
 * @package App\scriptPHPautomation
 * searching for a class declaration on a site with hundreds of PHP files.
 */
class searchClassDeclaration
{
    public function searchClass($fileName, $keyWordToSearch){

         // the following line prevents the browser from parsing this as HTML.
         header('Content-Type: text/plain');

         // get the file contents, assuming the file to be readable (and exist)
         $contents = file_get_contents($fileName);

         // escape special characters in the query
         $pattern = preg_quote($keyWordToSearch, '/');

         // finalise the regular expression, matching the whole line
         $pattern = "/^.*$pattern.*\$/m";

         // search, and store all matching occurences in $matches
         if(preg_match_all($pattern, $contents, $matches)){
             echo "Found matches:\n";
             echo implode("\n", $matches[0]);
         }
         else{
             echo "No matches found";
         }
     }
}