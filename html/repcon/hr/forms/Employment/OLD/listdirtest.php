<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>Untitled Document</title>
?<php

/**
* Return a list of all files within a directory
*
* @param string $directory The directory to search
* @param bool $recursive Go through child directories as well
* @return array
*/
function dirList($directory, $recursive = true) 
{
 // create an array to hold directory list
 $results = array();

 // create a handler for the directory
 $handler = opendir($directory);

 // keep going until all files in directory have been read
 while (false !== ($file = readdir($handler)))
 {
  // if $file isn't this directory or its parent, 
  // add it to the results array
  if ($file != '.' && $file != '..')
  {
   // if the file is a directory
   // add contents of that directory
   if(is_dir($directory.DIRECTORY_SEPARATOR.$file) && $recursive === true)
   {
    $results[] = array($file => dirList($directory.DIRECTORY_SEPARATOR.$file));
   }
   else
   {
    $results[] = $file;
   }
  }
 }

 // tidy up: close the handler
 closedir($handler);

 // done!
 return $results;

}
/**
 * Return an unordered html list of all files within a directory
 *
 * @param string $directory The directory to search
 * @param array $fileTypes Which fileTypes to show. To list all files use htmlDirList("mydir", array("*"))
 * @param bool $recursive Search through child directories as well
 * @param string $listClassName The class name to apply to the <ul>
 * @param function $displayName A function to call on the file to determine how it is displayed (e.g. change _ to space)
 * @return array
 */
function htmlDirList($directory, $fileTypes = null, $recursive = true, $listClassName = null, $displayName = null)
{
 // defaults (if null)
 // restrict by fileTypes
 $fileTypes = (is_null($fileTypes) ? array("doc","rtf","pdf") : $fileTypes);
 // the class name to apply to the <ul>
 $listClassName = (is_null($listClassName) ? "documents" : $listClassName);
 // set a default function for displayName
 $displayName = (is_null($displayName) ? create_function('$fileName,$extension', 'return str_replace("_", " ", $fileName);') : $displayName);
 
 // get list of files / folders
 $list = dirList($directory, $recursive);
 
 // use an array for building up the string
 $results = array();
 // create the list
 $results[] = "<ul class=\"$listClassName\">\n";
 foreach ( $list as $value )
 {
  // is a folder
  if(is_array($value))
  {
   $results[] = "<li class=\"directory\">".$displayName(key($value),"")."\n".htmlDirList($directory.DIRECTORY_SEPARATOR.key($value), $fileTypes, $recursive, $listClassName, $displayName)."</li>\n";
  }
  else
  {
   // the extension is after the last "."
   $extension = strtolower(array_pop(explode(".", $value)));
   // the file name is before the last "."
   $fileName = array_shift(explode(".", $value));
   // continue to next item if not one of the desired file types
   if(!in_array("*", $fileTypes) && !in_array($extension, $fileTypes)) continue;
   // add the list item
   $results[] = "<li class=\"file $extension\"><a href=\"".str_replace("\\", "/", $directory)."/$value\">".$displayName($fileName, $extension)."</a></li>\n";
  }
 }
 $results[] = "\n</ul>";
 
 // return the results as a string
 return implode("", $results);
}
?>
</head>
<body>
<?php echo htmlDirList("documents") ?>
</body>