<?php
namespace Sagres\Framework\FileSystem;

use Sagres\Framework\BaseFrameworkAction;
use Sagres\Framework\FileSystem\Exception\NotFound;
use Sagres\Framework\FileSystem\Exception\InvalidPermissions;
use Sagres\Framework\FileSystem\Exception\IOException;

class Action extends BaseFrameworkAction
{
    private $fileSet = null;


    public function __construct(Set $fileSet = null)
    {
        if (!is_null($fileSet)) {
            $this->setFileSet($fileSet);
        }
    }

    /**
     * returns the fileSet to perform operations on
     * @return Sagres\Framework\FileSystem\Set
     */

    public function getFileSet()
    {
        return $this->fileSet;
    }

    /**
     * Sets the fileSet to perform operations on
     * @param Sagres\Framework\FileSystem\Set $fileSet
     */
    public function setFileSet(Set $fileSet)
    {
        $this->fileSet = $fileSet;
    }


    /**
     * Copy a file
     * @param String $originalFile - the file to copy
     * @param String $newFile - the new file location and name
     * @param Boolean $overwrite - should we overwrite
     * @param null|ocatal $mode - the new file permissions, if null it will keep the permitions of the original file
     *
     * @throws Sagres\Framework\FileSystem\Exception\IOException
     * @throws Sagres\Framework\FileSystem\Exception\InvalidPermissions
     * @throws Sagres\Framework\FileSystem\Exception\NotFound
     */
    public function copy($originalFile, $newFile, $overwrite = false, $mode = null)
    {
        if (is_dir($originalFile)) {
            $message = $originalFile . ' is a folder, can not copy bailing out';
            $this->log($message, 'error');
            throw new IOException($message);
        }

        if(! is_file($originalFile)) {
            $message = $originalFile . ' not found';
            $this->log($message, 'error');
            throw new NotFound($message);
        }

        if(! is_readable($originalFile)) {
            $message = $originalFile . ' is not readable';
            $this->log($message, 'error');
            throw new InvalidPermissions($message);
        }

        $destinationFolder = dirname($newFile);
        if (!file_exists($destinationFolder)) {
            $message = $destinationFolder . ' does not exists, can not copy, bailing out';
            $this->log($message, 'error');
            throw new NotFound($message);
        }

        if (!is_writable($destinationFolder)) {
            $message = $destinationFolder . ' is not writable, bailing out';
            $this->log($message, 'error');
            throw new InvalidPermissions($message);
        }

        if(file_exists($newFile) && ! $overwrite) {
            $message = $newFile . ' will be overwritten in bailing out';
            $this->log($message, 'error');
            throw new IOException($message);
        }

        if(is_null($mode)) {
            $mode = $this->getPermissions($originalFile);
        }

        $this->log("[copy] $originalFile -> $newFile", 'debug');
        try{
            copy($originalFile, $newFile);
        } catch (\Exception $e) {
            $message = $originalFile . ' unable to copy to ' . $newFile . ' bailing out';
            $this->log($message, 'error');
            throw new IOException($message);
        }
    }

    /**
     * get the octal representation of the resource permissions
     * @param String $path
     * @return string
     */
    public function getPermissions($path)
    {
        return substr(sprintf('%o', fileperms($path)), -4);
    }
    /**
     * copy all files in the fileset to the given destination folder
     *
     * Destination folder will be created if needed
     *
     * if $overwrite is false function will fail if the file already exists in $folder
     *
     * @param String $folder
     * @param Boolean $overwrite - should we overwrite files ?
     */
    public function copyToFolder($folder, $overwrite = false, $mode=null)
    {
        $logger = $this->getLogger();
        $this->log("Copy files to" . $folder);

        $set = $this->getFileSet();
        $files = $set->toArray();
        $sourceFolder = $set->getLowestCommonFolder();


        if(!$sourceFolder || '/' == $sourceFolder) {
            $message = 'Can not determine source folder for the file set, make';
            $message .= ' sure all paths in file set have a common base folder';
            $message .= ' having /folder1 and /folder2 in the file set will invalidate the copy';
            $this->log($message, 'error');
            throw new \LogicException($message);
        }

        foreach ($files as $file) {

            if (!file_exists($file)) {
                $message = $file . ' not found, skipping';
                echo "\n$message\n";
                $this->log($message, 'warning');
                continue;
            }

            $newFile = $this->getDestination($file, $sourceFolder, $folder);
            if (is_dir($file)) {
                // just so we do not overwrite the mode
                $nmode = $mode;
                if( is_null($mode)) {
                    $nmode = 0755;
                }
                $this->mkdir($newFile, $nmode);
            } else {
                $this->copy($file, $newFile, $overwrite, $mode);
            }
        }
    }


    /**
     * creates a folder structure, it is recursive
     *
     * @param String $folder
     * @param String|octal $mode
     * @throws IOException
     */
    public function mkdir($folder, $mode = '0755')
    {
        if (is_dir($folder)) {
            return;
        }

        try {
            $this->log("[mkdir] $folder", 'debug');
            mkdir($folder, $mode, true);
        } catch (\Exception $e) {
            $message = $folder . ' - can not create :' . $e->getMessage();
            $this->log($message, 'error');
            throw new IOException($message);
        }
    }

    /**
     * computes the destination path
     *
     * @param String $originalFile - the file to be copied
     * @param String $baseSourceFolder - the source folder where the copy was initiated
     * @param String $destinationFolder - the folder where to copy files
     */
    private function getDestination($originalFile, $baseSourceFolder, $destinationFolder)
    {

         if(DIRECTORY_SEPARATOR == substr($baseSourceFolder, -1)) {
             $baseSourceFolder = substr($baseSourceFolder, 0, -1);
         }

         if(DIRECTORY_SEPARATOR == substr($destinationFolder, -1)) {
             $destinationFolder = substr($destinationFolder, 0, -1);
         }


         $ret = str_replace($baseSourceFolder, $destinationFolder, $originalFile);

         return $ret;
    }
}
