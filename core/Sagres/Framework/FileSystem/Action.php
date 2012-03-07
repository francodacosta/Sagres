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
     * creates the destination folder structure, if needed
     *
     * @param String $destinationPath the directory path of the file beeing copied
     * @param String $basePath the base where to start creating the structure
     */
    private function createFolderStructure($destinationPath, $mode = 0777)
    {

        if(! is_dir($destinationPath)) {
            mkdir($destinationPath, $mode, true);
        }

    }

    /**
     * computes the destination path of the given folder
     * @param String $sourcePath - the path of the source folder
     * @param String $baseSourceFolder - the base source folder where the copy was initiated
     * @param String $baseDestinationFolder - the base folder where to copy files
     */
    private function computeDestinationPath($sourcePath, $baseSourceFolder, $baseDestinationFolder)
    {
        if (is_null($baseSourceFolder)) {
            $filename = basename($sourcePath);
            $dest = $baseDestinationFolder . DIRECTORY_SEPARATOR . $filename;
            return $dest;
        }

        $path = str_replace($sourcePath, $baseDestinationFolder, $baseSourceFolder);

        return $path;
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

        $files = $this->getFileSet()->toArray();
        $logger = $this->getLogger();
        $this->log("Copy files to" . $folder);

        $baseSourceFolder = $this->getFileSet()->getLowesCommonFolder();

        if(! is_null($baseSourceFolder)) {
            // getLowesCommonFolder() returns the lowest common folder that belongs to the set
        }

        echo "\n baseSourceFolder $baseSourceFolder\n";
        foreach ($files as $file) {
            $this->log("\t" . $file);

            if (!is_dir($folder)) {
                throw new NotFound($folder . ' does not exists');
            }

            if (!is_writable($folder)) {
                throw new InvalidPermissions($folder . ' is not writtable');
            }

            $destinationPath = $this->computeDestinationPath($file, $baseSourceFolder, $folder);

            echo "\n dest path: $destinationPath\n";

            if(is_dir($file)) {
                $this->createFolderStructure($destinationPath);
            } else {

                if (file_exists($destinationPath) && !$overwrite) {
                    $message = $destinationPath . ' will be overwritten bailing out';
                    $this->log($message, 'error');
                    throw new IOException($message);
                }

                try{
                    copy($file, $destinationPath);
                } catch (\Exception $e) {
                    $message = $file . ' unable to copy to ' . $destinationPath . ' bailing out';
                    $this->log($message, 'error');
                    throw new IOException($message);
                }
            }

        }
    }

}
