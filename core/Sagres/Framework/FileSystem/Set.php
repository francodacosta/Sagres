<?php
namespace Sagres\Framework\FileSystem;
/**
 * Represents a Path set, that is a set of files / folders in your local hard drive
 * @author nuno
 *
 */
use Sagres\Framework\FileSystem\Exception\IOException;

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
        $this->set[] = $this->formatPath($file);
    }


    /**
     * adds all flies inside the $folder folder that match the selection criteria
     * to the path set
     *
     * @param String $folder - the folder to search for files
     * @param String $selector - the selection criteria for files
     */

    public function addSet($folder, $selector = '/.*/')
    {

        $folder = $this->formatPath($folder);
        $this->addPath($folder);
        if (is_dir($folder)) {
            if ($dh = opendir($folder)) {
                while (($filename = readdir($dh)) !== false) {
                   $file = $folder . $filename;
                       if(!is_dir($file)) {
                           if (preg_match( $selector, $filename) > 0) {
                               $this->addPath($file);
                           }
                       }
                }
                closedir($dh);
            }
        } else {
            throw new IOException($folder . ' not found');
        }
    }


    public function addSetRecursive($folder, $selector="/.*/")
    {
        $this->addSet($folder, $selector);

        $found = glob($folder . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR);
        foreach($found as $subfolder) {
            $this->addSet($subfolder, $selector);
        }
    }

    private function formatPath($path)
    {

        if (is_dir($path)) {
            if(DIRECTORY_SEPARATOR != substr($path, -1)) {
                $path = $path . DIRECTORY_SEPARATOR;
            }
        }
        return $path;
    }


    /**
     *
     * @return array containig all folders in the file set
     */
    public function getAllFoldersInSet()
    {
        $set = $this->toArray();
        $ret = array();

        foreach($set as $file) {
            if(is_dir($file)) {
                $ret[] = $file;
            }
        }


        usort($ret,function ($a, $b) {return strlen($a)-strlen($b);});
        return $ret;
    }


    /**
     * returns the lowest common folder present in the set.
     *
     * @return NULL|String
     */
    public function getLowestCommonFolder()
    {
        $folders = $this->getAllFoldersInSet();
        if (count($folders) == 0 ) {
            return null;
        }

        $lowest = $folders[0];
        $origLowest = false;

        while($lowest != $origLowest) {
            $origLowest = $lowest;
            foreach($folders as $folder) {
                if (false === strpos($folder, $lowest)) {
                    $lowest = dirname($folder);
                    break;
                }
            }
        }

        return $lowest;

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
