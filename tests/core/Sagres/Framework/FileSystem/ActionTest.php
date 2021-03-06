<?php
namespace Sagres\Framework\FileSystem;

/**
 * Test class for Action.
 * Generated by PHPUnit on 2012-03-06 at 12:25:35.
 */
class ActionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Action
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Action;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Sagres\Framework\FileSystem\Action::getFileSet
     * @covers Sagres\Framework\FileSystem\Action::setFileSet
     */
    public function testSetGetFileSet()
    {
        $set = new Set();
        $this->object->setFileSet($set);
        $this->assertSame($set, $this->object->getFileSet());
    }

    private function resetFolder($folder) {

        if(is_dir($folder)) {
            if( $dh = opendir($folder)) {
                while (($filename = readdir($dh)) !== false) {
                    if(! in_array($filename, array('.', '..'))) {
                        unlink($folder . DIRECTORY_SEPARATOR . $filename);
                    }
                }
            }
        }

        if(file_exists($folder)) {
            rmdir($folder);
        }
        mkdir($folder, 0777);
    }

    /**
     * @covers Sagres\Framework\FileSystem\Action::copyToFolder
     * @covers Sagres\Framework\FileSystem\Action::copy
     * @covers Sagres\Framework\FileSystem\Action::getPermissions
     * @covers Sagres\Framework\FileSystem\Action::getDestination
     * @covers Sagres\Framework\FileSystem\Action::ensureReadable
     */
    public function testCopyToFolder()
    {
        $fileSet = new Set();
        $fileSet->addSet(__DIR__ . '/../../../../fixtures/folder1/');

        $folder = __DIR__ . '/../../../../fixtures/copy/';

        // make sure the folder is empty
        $this->resetFolder($folder);


        $this->object->setFileSet($fileSet);
        $this->object->copyToFolder($folder);

        $this->assertTrue(file_exists($folder . '/file1.txt'));
        $this->assertTrue(file_exists($folder . '/file2.yml'));
        $this->assertTrue(file_exists($folder . '/file3.txt'));
    }

    /**
     * @covers Sagres\Framework\FileSystem\Action::copyToFolder
     * @covers Sagres\Framework\FileSystem\Action::copy
     * @expectedException \LogicException
     * @depends testCopyToFolder
     */
    public function testCopyToFolder_noCommonBaseSourceFolder()
    {
        $fileSet = new Set();
        $fileSet->addSet(__DIR__ . '/../../../../fixtures/folder1');
        $fileSet->addPath('/var/');
        $folder = __DIR__ . '/../../../../fixtures/' . md5(time());

        $this->object->setFileSet($fileSet);
        $this->object->copyToFolder($folder);

    }
    /**
     * @covers Sagres\Framework\FileSystem\Action::copyToFolder
     * @covers Sagres\Framework\FileSystem\Action::copy
     * @expectedException Sagres\Framework\FileSystem\Exception\IOException
     * @depends testCopyToFolder
     */
    public function testCopyToFolder_BailoutIfOverwitingFiles()
    {
        $fileSet = new Set();
        $fileSet->addSet(__DIR__ . '/../../../../fixtures/folder1');

        $folder = __DIR__ . '/../../../../fixtures/copy';

        $this->object->setFileSet($fileSet);
        $this->object->copyToFolder($folder);

    }

    /**
     * @covers Sagres\Framework\FileSystem\Action::copyToFolder
     * @covers Sagres\Framework\FileSystem\Action::copy
     * @expectedException Sagres\Framework\FileSystem\Exception\InvalidPermissions
     * @depends testCopyToFolder
     */
    public function testCopyToFolder_foldernotWritable()
    {
        $fileSet = new Set();
        $fileSet->addSet(__DIR__ . '/../../../../fixtures/folder1');

        $folder = __DIR__ . '/../../../../fixtures/copy1';

        $this->resetFolder($folder);
        chmod($folder, 0444);

        $this->object->setFileSet($fileSet);
        $this->object->copyToFolder($folder);

    }

    /**
     * @covers Sagres\Framework\FileSystem\Action::copyToFolder
     * @covers Sagres\Framework\FileSystem\Action::copy
     * @depends testCopyToFolder
     */
    public function testCopyToFolder_SourceFileNotFound()
    {
        $fileSet = new Set();
        $fileSet->addSet(__DIR__ . '/../../../../fixtures/folder1');
        $fileSet->addPath(__DIR__ . '/../../../../fixtures/not.found');
        $folder = __DIR__ . '/../../../../fixtures/copy1';

        $this->resetFolder($folder);

        $this->object->setFileSet($fileSet);
        $this->object->copyToFolder($folder);
    }

    /**
     * @covers Sagres\Framework\FileSystem\Action::copy
     * @expectedException Sagres\Framework\FileSystem\Exception\IOException
     */
    public function testCopy_originalFileIsFolder()
    {
        $fileSet = new Set();

        $folder = __DIR__ . '/../../../../fixtures/copy1';

        $this->object->copy($folder, $folder);
    }


    /**
     * @covers Sagres\Framework\FileSystem\Action::copy
     * @covers Sagres\Framework\FileSystem\Action::ensureReadable
     * @expectedException Sagres\Framework\FileSystem\Exception\InvalidPermissions
     */
    public function testCopy_originalFileIsNotReadable()
    {
        $fileSet = new Set();

        $file = __DIR__ . '/../../../../fixtures/not.readable';
        $file1 = __DIR__ . '/../../../../fixtures/copy/not.readable';

        chmod($file, 0111);

        $this->object->copy($file, $file1);
    }

    /**
     * @covers Sagres\Framework\FileSystem\Action::copy
     * @expectedException Sagres\Framework\FileSystem\Exception\NotFound
     */
    public function testCopy_DestinationFolderIsNotFound()
    {
        $fileSet = new Set();

        $file = __DIR__ . '/../../../../fixtures/empty.file';
        $file1 = __DIR__ . '/../../../../fixtures/xcsdafczxcxzcxzc/not.readable';


        $this->object->copy($file, $file1);
    }


    /**
     * @covers Sagres\Framework\FileSystem\Action::__construct
     */
    public function testconstructor()
    {
        $fileSet = new Set();
        $o =new Action($fileSet);

        $this->assertSame($fileSet, $o->getFileSet());
    }

    /**
     * @covers Sagres\Framework\FileSystem\Action::mkdir
     */
    public function testMkdir()
    {
        $folder = __DIR__ . '/../../../../fixtures/foo';
        $this->object->mkdir($folder);

        $this->assertTrue(file_exists($folder));

        rmdir($folder);
    }
    /**
     * @covers Sagres\Framework\FileSystem\Action::mkdir
     */
    public function testMkdir_recursive()
    {
        $folder = __DIR__ . '/../../../../fixtures/foo/1';
        $this->object->mkdir($folder);

        $this->assertTrue(file_exists($folder));

        rmdir($folder);
        rmdir(dirname($folder));
    }
    /**
     * @covers Sagres\Framework\FileSystem\Action::mkdir
     */
    public function testMkdir_folderAlreadyExists()
    {
        $folder = __DIR__ . '/../../../../fixtures/foo';
        $this->object->mkdir($folder);

        $this->assertTrue(file_exists($folder));

        $this->object->mkdir($folder);

        $this->assertTrue(file_exists($folder));
        rmdir($folder);
    }

}
?>
