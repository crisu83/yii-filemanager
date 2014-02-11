<?php

class FileManagerTest extends \YiiTest
{
   /**
    * @var \CodeGuy
    */
    protected $codeGuy;

    protected function _before()
    {
        $this->mockApplication(
            array(
                'aliases' => array(
                    'project' => realpath(__DIR__ . '/../..'),
                    'vendor' => 'project.vendor',
                ),
                'components' => array(
                    'fileManager' => array(
                        'class' => 'project.components.FileManager',
                        'baseUrl' => '//test',
                    ),
                ),
                'preload' => array(
                    'fileManager',
                ),
            )
        );
    }

    public function testInit()
    {
        $this->assertNotEquals(false, Yii::getPathOfAlias('fileManager'));
    }

    public function testCreateModel()
    {
        // todo: implement
    }

    public function testSaveModel()
    {
        // todo: implement
    }

    public function testLoadModel()
    {
        // todo: implement
    }

    public function testDeleteModel()
    {
        // todo: implement
    }

    public function testSendFile()
    {
        // todo: implement
    }

    public function testXSendFile()
    {
        // todo: implement
    }

    public function testGetBasePath()
    {
        $manager = $this->getManager();
        $this->assertEquals('files', $manager->getBasePath());
        $this->assertNotEquals(false, strpos($manager->getBasePath(true), 'yii-filemanager/tests/files'));
    }

    public function testGetBaseUrl()
    {
        $manager = $this->getManager();
        $this->assertEquals('files', $manager->getBaseUrl());
        $this->assertEquals('//test/files', $manager->getBaseUrl(true));
    }

    public function testCreateDirectory()
    {
        // todo: implement
    }

    public function testDeleteDirectory()
    {
        // todo: implement
    }

    public function testNormalizeFilename()
    {
        $manager = $this->getManager();
        $this->assertEquals('test.txt', $manager->normalizeFilename('test/\?%*:|"<>.txt'));
        $this->assertEquals('test-1.png', $manager->normalizeFilename('test 1.png'));
    }

    /**
     * @return FileManager
     */
    protected function getManager()
    {
        return Yii::app()->getComponent('fileManager');
    }
}