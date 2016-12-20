<?php
namespace Picamator\SteganographyKit\Tests\Unit;

use Picamator\SteganographyKit\StegoContainer;

class StegoContainerTest extends BaseTest
{
    /**
     * @var StegoContainer
     */
    private $stegoContainer;

    /**
     * @var \Picamator\SteganographyKit\StegoSystem\StegoSystemInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private $stegoSystemMock;

    /**
     * @var \Picamator\SteganographyKit\SecretText\SecretTextInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private $secretTextMock;

    /**
     * @var \Picamator\SteganographyKit\Image\ImageInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private $imageMock;

    protected function setUp()
    {
        $this->stegoSystemMock = $this->getMockBuilder('Picamator\SteganographyKit\StegoSystem\StegoSystemInterface')
            ->getMock();

        $this->secretTextMock = $this->getMockBuilder('Picamator\SteganographyKit\SecretText\SecretTextInterface')
            ->getMock();

        $this->imageMock = $this->getMockBuilder('Picamator\SteganographyKit\Image\ImageInterface')
            ->getMock();

        // stub object manager
        $this->stubObjectManager([
            'Picamator\SteganographyKit\StegoSystem\PureLsb' => ['object' => $this->stegoSystemMock],
            'Picamator\SteganographyKit\SecretText\PlainText' => ['object' =>  $this->secretTextMock],
            'Picamator\SteganographyKit\Image\Image' => ['object' => $this->imageMock]
        ]);

        $this->stegoContainer = new StegoContainer();
    }

    public function testEncode()
    {
        $coverPath = 'coverPath';
        $stegoPath = 'stegoPath';
        $text = 'Lorem Ipsum';

        // stego system mock
        $this->stegoSystemMock->expects($this->once())
            ->method('encode')
            ->with($this->equalTo($this->secretTextMock), $this->equalTo($this->imageMock));

        $this->stegoContainer->encode($coverPath, $stegoPath, $text);
    }

    public function testDecode()
    {
        $stegoPath = 'stegoPath';

        // stego system mock
        $this->stegoSystemMock->expects($this->once())
            ->method('decode')
            ->with($this->equalTo($this->imageMock), $this->equalTo($this->secretTextMock));

        $this->stegoContainer->decode($stegoPath);
    }
}
