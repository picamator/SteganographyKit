<?php
namespace Picamator\SteganographyKit\Tests\Unit\ObjectManager;

use Picamator\SteganographyKit\Tests\Unit\BaseTest;

class AbstractLsbTest extends BaseTest
{
    /**
     * @var \Picamator\SteganographyKit\StegoSystem\AbstractLsb | \PHPUnit_Framework_MockObject_MockObject
     */
    private $lsbMock;

    protected function setUp()
    {
        parent::setUp();

        $this->lsbMock = $this->getMockBuilder('Picamator\SteganographyKit\StegoSystem\AbstractLsb')
            ->getMockForAbstractClass();
    }

    /**
     * @expectedException \Picamator\SteganographyKit\InvalidArgumentException
     */
    public function testFailSetChannels()
    {
        $this->lsbMock->setChannels(['test-channel']);
    }

    /**
     * @expectedException \Picamator\SteganographyKit\RuntimeException
     */
    public function testFailEncode()
    {
        $secretTextCount = 100;
        $coverTextCount = 10;

        // secret text mock
        $secretTextMock = $this->getMockBuilder('Picamator\SteganographyKit\SecretText\SecretTextInterface')
            ->getMock();

        $secretTextMock->expects($this->once())
            ->method('count')
            ->willReturn($secretTextCount);

        // cover text mock
        $coverTextMock = $this->getMockBuilder('Picamator\SteganographyKit\Image\ImageInterface')
            ->getMock();

        $coverTextMock->expects($this->once())
            ->method('count')
            ->willReturn($coverTextCount);

        // never
        $coverTextMock->expects($this->never())
            ->method('save');

        $this->lsbMock->encode($secretTextMock, $coverTextMock);
    }
}
