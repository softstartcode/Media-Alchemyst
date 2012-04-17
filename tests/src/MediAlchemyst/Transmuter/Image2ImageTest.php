<?php

namespace MediAlchemyst\Transmuter;

require_once __DIR__ . '/../Specification/UnknownSpecs.php';

class Image2ImageTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Image2Image
     */
    protected $object;
    protected $specs;
    protected $source;
    protected $dest;

    protected function setUp()
    {
        $this->object = new Image2Image(new \MediAlchemyst\DriversContainer(new \Symfony\Component\DependencyInjection\ParameterBag\ParameterBag(array())));

        Image2Image::$autorotate = false;

        $this->specs = new \MediAlchemyst\Specification\Image();
        $this->source = \MediaVorus\MediaVorus::guess(new \SplFileInfo(__DIR__ . '/../../../files/photo03.JPG'));
        $this->dest = __DIR__ . '/../../../files/output_auto_rotate.jpg';
    }

    public function tearDown()
    {
        if(file_exists($this->dest) && is_writable($this->dest))
        {
            unlink($this->dest);
        }
    }

    /**
     * @covers MediAlchemyst\Transmuter\Image2Image::execute
     */
    public function testExecute()
    {
        $this->object->execute($this->specs, $this->source, $this->dest);

        $MediaDest = \MediaVorus\MediaVorus::guess(new \SplFileInfo($this->dest));

        $this->assertEquals($this->source->getWidth(), $MediaDest->getWidth());
        $this->assertEquals($this->source->getHeight(), $MediaDest->getHeight());
    }

    /**
     * @covers MediAlchemyst\Transmuter\Image2Image::execute
     */
    public function testExecuteAutorotate()
    {
        Image2Image::$autorotate = true;

        $this->object->execute($this->specs, $this->source, $this->dest);

        $MediaDest = \MediaVorus\MediaVorus::guess(new \SplFileInfo($this->dest));

        $this->assertEquals($this->source->getWidth(), $MediaDest->getHeight());
        $this->assertEquals($this->source->getHeight(), $MediaDest->getWidth());
    }

    /**
     * @covers MediAlchemyst\Transmuter\Image2Image::execute
     */
    public function testExecuteSimpleResize()
    {
        $this->specs->setDimensions(320, 240);

        $this->object->execute($this->specs, $this->source, $this->dest);

        $MediaDest = \MediaVorus\MediaVorus::guess(new \SplFileInfo($this->dest));

        $this->assertTrue($MediaDest->getHeight() <= 240);
        $this->assertTrue($MediaDest->getWidth() <= 320);
    }

    /**
     * @covers MediAlchemyst\Transmuter\Image2Image::execute
     */
    public function testExecuteOutBoundResize()
    {
        $this->specs->setDimensions(240, 260);
        $this->specs->setStrip(true);
        $this->specs->setRotationAngle(-90);
        $this->specs->setResizeMode(\MediAlchemyst\Specification\Image::RESIZE_MODE_OUTBOUND);

        $this->object->execute($this->specs, $this->source, $this->dest);

        $MediaDest = \MediaVorus\MediaVorus::guess(new \SplFileInfo($this->dest));

        $this->assertEquals(240, $MediaDest->getHeight());
        $this->assertEquals(260, $MediaDest->getWidth());
    }

    /**
     * @covers MediAlchemyst\Transmuter\Image2Image::execute
     * @covers MediAlchemyst\Exception\SpecNotSupportedException
     * @expectedException \MediAlchemyst\Exception\SpecNotSupportedException
     */
    public function testWrongSpecs()
    {
        $this->object->execute(new \MediAlchemyst\Specification\UnknownSpecs(), $this->source, $this->dest);
    }

}