<?php
namespace Sagres\Framework\File;

use Sagres\Framework\BaseFrameworkAction;
use Sagres\Framework\File\Exception\NotFound;
use Sagres\Framework\File\Exception\InvalidPermissions;
use Sagres\Framework\File\Exception\IOException;

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
     * @return Sagres\Framework\File\Set
     */

    public function getFileSet()
    {
        return $this->fileSet;
    }

    /**
     * Sets the fileSet to perform operations on
     * @param Sagres\Framework\File\Set $fileSet
     */
    public function setFileSet(Set $fileSet)
    {
        $this->fileSet = $fileSet;
    }


    /**
     * copy all files in the fileset to the given destination folder
     *
     * if $overwrite is false function will fail if the file already exists in $folder
     *
     * @param String $folder
     * @param Boolean $overwrite - should we overwrite files ?
     */
    public function copyToFolder($folder, $overwrite = false)
    {
        // checks

        if (!is_dir($folder)) {
            throw new NotFound($folder . ' does not exists');
        }

        if (!is_writable($folder)) {
            throw new InvalidPermissions($folder . ' is not writtable');
        }

        $files = $this->getFileSet()->toArray();
        $logger = $this->getLogger();
        $this->log("Copy files to" . $folder);

        foreach ($files as $file) {
            $this->log("\t" . $file);

            $filename = basename($file);
            $newFile = $folder . DIRECTORY_SEPARATOR . $filename;

            if (file_exists($newFile) && !$overwrite) {
                $message = $filename . ' will be overwritten in ' . $folder . ' bailing out';
                $this->log($message, 'error');
                throw new IOException($message);
            }


            if (! copy($file, $newFile)) {

                $message = $filename . ' unable to copy to ' . $folder . ' bailing out';
                $this->log($message, 'error');
                throw new IOException($message);
            }
        }
    }

}
