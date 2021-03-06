<?php
namespace Asset\Model;

require_once 'PHPUnit/Framework.php';
//require_once '../../../bootstrap.php';

/**
 * Test class for Asset.
 * Generated by PHPUnit on 2010-02-23 at 11:51:13.
 */
class AssetTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var Asset
     */
    protected $asset;

    protected $group;
    protected $mimeType;
    protected $type;
    protected $extension;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->group = new Group('default', 'Default');
        $this->type = new Type('image', 'Image');
        $this->mimeType = new MimeType('image/jpg', $this->type);
        $this->extension = new Extension('jpg');
        $this->asset = new Asset('test', 'Test', $this->extension, $this->group, $this->mimeType);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
    }

    public function test__constructor()
    {
        $this->assertEquals('test', $this->asset->getSysname());
        $this->assertEquals('Test', $this->asset->getName());
        $this->assertEquals($this->extension, $this->asset->getExtension());
        $this->assertEquals($this->group, $this->asset->getGroup());
        $this->assertEquals($this->mimeType, $this->asset->getMimeType());
    }

    public function testSetCaption() {
        $caption = 'This is a test caption';
        $this->asset->setCaption($caption);
        $this->assertEquals($caption, $this->asset->getCaption());
    }

    public function testGetLocation()
    {
        $expected = APPLICATION_ROOT . '/data/assets/default/te/test/original.jpg';
        $this->assertEquals($expected, $this->asset->getLocation());
    }

    public function testGetUrl()
    {
        $expected = '/assets/default/te/test/original.jpg';
        $this->assertEquals($expected, $this->asset->getUrl());
    }

    public function testToArray()
    {
        $data = array(
            'id' => null,
            'sysname' => 'test',
            'name' => 'Test',
            'extension' => '',
            'group' => '',
            'mimeType' => '',
            'caption' => '',
            'uploadDate' => $this->asset->getUploadDate()->format('Y-m-d H:i:s'),
            'url' => $this->asset->getUrl()
        );

        $this->assertEquals($data, $this->asset->toArray());
    }
}
?>
