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
     * ensures the file or files are readable
     * @param Array|String $files
     * @throws InvalidPermissions
     */
    private function ensureReadable($files)
    {
        if(! is_array($files)) {
            $files = array($files);
        }

        foreach($files as $file) {
            if(! is_readable($file)) {
                $message = $file . ' is not readable';
                $this->log($message, 'error');
                throw new InvalidPermissions($message);
            }
        }
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

        $this->ensureReadable($originalFile);

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
            // @codeCoverageIgnoreStart
            $message = $originalFile . ' unable to copy to ' . $newFile . ' bailing out';
            $this->log($message, 'error');
            throw new IOException($message);
            // @codeCoverageIgnoreEnd
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
            // @codeCoverageIgnoreStart
            $message = $folder . ' - can not create :' . $e->getMessage();
            $this->log($message, 'error');
            throw new IOException($message);
            // @codeCoverageIgnoreEnd
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

    /**
     * Deletes the specified file
     *
     * @param String $filename
     * @throws IOException
     */
    public function deleteFile($filename)
    {
        try {
            unlink($filename);
        } catch (\Exception $e) {
            $message = "Can not delete file $filename : " . $e->getMessage();
            $this->log($message, 'error');
            throw new IOException($message);
        }
    }

    /**
     * deletes all files in the file set
     *
     * if $set is passes it will delete files in the passed set otherwise will delete
     * files specified with the setFileSet()
     *
     * this will just delete files, folders are not deleted
     *
     * @param Set $set
     * @see Sagres\Framework\FileSystem\Action::deleteFile
     */
    public function delete(Set $set = null)
    {
        if (is_null($set)) {
            $set = $this->getFileSet();
        }
        foreach($set as $filemane) {
            // not doing this check, deleteFile should fail if trying to delete a folder
//             if (is_dir($filemane)) {
//                 $this->log("$filename is a folder, skipping");
//                 continue;
//             }

            $this->deleteFile($filename);
        }
    }

    /**
     * Deletes a folder, the folder must be empty and you need to have the
     * correct permissions to delete it
     *
     * @param String $folder
     * @throws IOException
     */
    public function deleteFolder($folder)
    {
        try {
            rmdir($folder);
        } catch (\Exception $e) {
            $message = "Can not delete folder $folder : " . $e->getMessage();
            $this->log($message, 'error');
            throw new IOException($message);
        }
    }

    /**
     * Deletes the folder dpecified including all files and folders inside it
     * You must have the correct permissions on the folder and its contents in
     * order to delete them
     *
     * @param String $folder
     * @throws IOException
     * @see Sagres\Framework\FileSystem\Action::deleteFolder
     * @see Sagres\Framework\FileSystem\Action::deleteFile
     */
    public function deleteFolderRecursive($folder)
    {
        $set = new Set();
        $set->addSet($folder);
        $folders = $set->getAllFoldersInSet();
        $this->delete($set);
        foreach($folders as $subFolder) {
            $this->deleteFolder($subFolder);
        }
        $this->deleteFolder(folder);
    }
}
