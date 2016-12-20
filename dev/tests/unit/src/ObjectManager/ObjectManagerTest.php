<?php
namespace Picamator\SteganographyKit\Tests\Unit\ObjectManager;

use Picamator\SteganographyKit\ObjectManager\ObjectManager;
use Picamator\SteganographyKit\Tests\Unit\BaseTest;

class ObjectManagerTest extends BaseTest
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    protected function setUp()
    {
        parent::setUp();

        $this->objectManager = ObjectManager::getInstance();
    }

    /**
     * @dataProvider providerCreate
     *
     * @param array $arguments
     */
    public function testCreate(array $arguments)
    {
        $className = '\DateTime';

        $actual = $this->objectManager->create($className, $arguments);
        $this->assertInstanceOf($className, $actual);
    }

    /**
     * @expectedException \Picamator\SteganographyKit\RuntimeException
     */
    public function testFailCreate()
    {
        $this->objectManager->create('Picamator\SteganographyKit\ObjectManager', [1, 2]);
    }

    public function providerCreate()
    {
        return [
            [['now']],
            [[]]
        ];
    }
}
