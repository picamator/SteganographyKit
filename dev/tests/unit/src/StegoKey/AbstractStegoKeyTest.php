<?php
namespace Picamator\SteganographyKit\Tests\Unit\ObjectManager;

use Picamator\SteganographyKit\Tests\Unit\BaseTest;

class AbstractStegoKeyTest extends BaseTest
{
    /**
     * @var \Picamator\SteganographyKit\StegoKey\AbstractStegoKey | \PHPUnit_Framework_MockObject_MockObject
     */
    private $stegoKeyMock;

    protected function setUp()
    {
        parent::setUp();

        $this->stegoKeyMock = $this->getMockBuilder('Picamator\SteganographyKit\StegoKey\AbstractStegoKey')
            ->getMockForAbstractClass();
    }

    /**
     * @expectedException \Picamator\SteganographyKit\LogicException
     */
    public function testFailGetSecretKey()
    {
        $this->stegoKeyMock->getSecretKey();
    }
}
