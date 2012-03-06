<?php
namespace Sagres\Framework\FileSystem;
/**
 * Represents a Path set, that is a set of files / folders in your local hard drive
 * @author nuno
 *
 */
class Set
{
    private $set = array();
    private $stringSeparator = ' ';

    /**
     * return the string used to separate files when returning the class as string
     * @return String
     */
    public function getStringSeparator()
    {
        return $this->stringSeparator;
    }

    /**
     * sets the string used to separate files when returning the class as string
     * @param String $stringSeparator
     */

    public function setStringSeparator($stringSeparator)
    {
        $this->stringSeparator = $stringSeparator;
    }

    /**
     * adds a single Path to the set
     * @param String $file
     */

    public function addPath($file)
    {
        $this->set[] = $file;
    }


    /**
     * adds all flies inside the $folder folder that match the selection criteria
     * to the path set
     *
     * @param String $folder - the folder to search for files
     * @param String $selector - the selection criteria for files
     */

    public function addSet($folder, $selector = '*.*')
    {
        $path = $folder . DIRECTORY_SEPARATOR . $selector;
        $found = glob($path);
        $array = $this->set;
        array_splice($array, count($array), 0, $found);
        $this->set = $array;
    }


    public function addSetRecursive($folder, $selector="*.*")
    {
        $this->addSet($folder, $selector);

        $found = glob($folder . DIRECTORY_SEPARATOR . '/*', GLOB_ONLYDIR);
        foreach($found as $folder) {
            $this->addSet($folder, $selector);
        }


    }



    /**
     * returns all folders present in the set (if files where added the folder
     * of that file will be returned)
     */
    public function getAllFoldersFromSet()
    {
        $set = $this->toArray();
        $ret = array();
        foreach($set as $file) {
            if (is_dir($file)) {
                $ret[] = $file;
            } else {
                $ret[] = dirname($file);
            }
        }

        return array_unique($ret);
    }

    /**
     * return a proper array representation of this class
     * @return array
     */
    public function toArray()
    {
        return $this->set;
    }

    /**
     * return a proper String representation of this class
     * @return String
     */
    public function __toString()
    {
        return implode($this->getStringSeparator(), $this->toArray());
    }



}
