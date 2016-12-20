<?php
namespace Picamator\SteganographyKit\ObjectManager;

use Picamator\SteganographyKit\RuntimeException;

/**
 * Creates objects, the main usage inside factories.
 *
 * All objects are unshared, for shared objects please use DI service libraries
 */
interface ObjectManagerInterface
{
    /**
     * Create objects.
     *
     * @param string $className
     * @param array  $arguments
     *
     * @throws RuntimeException
     *
     * @return mixed
     */
    public function create($className, array $arguments = []);

    /**
     * Gets instance
     *
     * @return ObjectManagerInterface
     */
    static public function getInstance();

    /**
     * Sets instance
     *
     * @param ObjectManagerInterface $instance
     */
    static function setInstance(ObjectManagerInterface $instance);

    /**
     * Clean instance
     */
    static function cleanInstance();
}
