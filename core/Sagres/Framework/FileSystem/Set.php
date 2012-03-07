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
            $this->addPath($subfolder);
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


        usort($ret,function ($a, $b) {return strlen($b)-strlen($a);});
        return $ret;
    }

    /**
     * returs the lowes common folder is the set
     * @return String
     */
    public function getLowesCommonFolder()
    {
        $folders = $this->getAllFoldersInSet();
        $lowest = null;

        foreach($folders as $folder) {
            if(is_null($lowest)) {
                $lowest = $folder;
            } else {
                $lowest = $this->get_longest_common_subsequence($lowest, $folder);
            }
        }

        return $lowest;
    }

    /**
     * gets the longest common substring
     *
     *
     * @param array $words
     * @return mixed
     */
    private function get_longest_common_subsequence($string_1, $string_2)
{
        $string_1_length = strlen($string_1);
        $string_2_length = strlen($string_2);
        $return          = array();

        if ($string_1_length === 0 || $string_2_length === 0)
        {
                // No similarities
                return $return;
        }

        $longest_common_subsequence = array();

        // Initialize the CSL array to assume there are no similarities
        for ($i = 0; $i < $string_1_length; $i++)
        {
                $longest_common_subsequence[$i] = array();
                for ($j = 0; $j < $string_2_length; $j++)
                {
                        $longest_common_subsequence[$i][$j] = 0;
                }
        }

        $largest_size = 0;

        for ($i = 0; $i < $string_1_length; $i++)
        {
                for ($j = 0; $j < $string_2_length; $j++)
                {
                        // Check every combination of characters
                        if ($string_1[$i] === $string_2[$j])
                        {
                                // These are the same in both strings
                                if ($i === 0 || $j === 0)
                                {
                                        // It's the first character, so it's clearly only 1 character long
                                        $longest_common_subsequence[$i][$j] = 1;
                                }
                                else
                                {
                                        // It's one character longer than the string from the previous character
                                        $longest_common_subsequence[$i][$j] = $longest_common_subsequence[$i - 1][$j - 1] + 1;
                                }

                                if ($longest_common_subsequence[$i][$j] > $largest_size)
                                {
                                        // Remember this as the largest
                                        $largest_size = $longest_common_subsequence[$i][$j];
                                        // Wipe any previous results
                                        $return       = array();
                                        // And then fall through to remember this new value
                                }

                                if ($longest_common_subsequence[$i][$j] === $largest_size)
                                {
                                        // Remember the largest string(s)
                                        $return[] = substr($string_1, $i - $largest_size + 1, $largest_size);
                                }
                        }
                        // Else, $CSL should be set to 0, which it was already initialized to
                }
        }

        // Return the list of matches
        return $return;
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
