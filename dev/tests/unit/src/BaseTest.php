<?php
namespace Picamator\SteganographyKit\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Picamator\SteganographyKit\InvalidArgumentException;
use Picamator\SteganographyKit\ObjectManager\ObjectManager;

abstract class BaseTest extends TestCase
{

    /**
     * Path to the data folder
     *
     * @var string
     */
    private $dataPath = '/data/';

    /**
     * Gets full path to data
     *
     * @param string $path
     *
     * @return string | boolean - full path or false if failed
     */
    protected function getDataPath($path)
    {
        return __DIR__ . $this->dataPath . $path;
    }

    /**
     * Stub object manager
     *
     * @param array $data
     *
     * @return \Picamator\SteganographyKit\ObjectManager\ObjectManagerInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected function stubObjectManager(array $data)
    {
        $objectManager = $this->getMockBuilder('Picamator\SteganographyKit\ObjectManager\ObjectManagerInterface')
            ->getMock();
        ObjectManager::setInstance($objectManager);

        $objectManager->expects($this->any())
            ->method('create')
            ->willReturnCallback(function($className) use ($data) {
                if (!isset($data[$className])) {
                    throw new InvalidArgumentException(
                        sprintf('Undefined class name "%s"', $className)
                    );
                }

                if (!isset($data[$className]['object'])) {
                    throw new InvalidArgumentException('Invalid data structure. Required parameter "object" was not set.');
                }

                $arguments = isset($data[$className]['arguments']) ? $data[$className]['arguments'] : [];
                $objectMock = is_string($data[$className]['object'])
                    ? $this->getMockBuilder($data[$className]['object'])
                        ->setConstructorArgs($arguments)
                        ->getMock()
                    : $data[$className]['object'];

                return $objectMock;
            });
    }
}
