<?php
namespace Picamator\SteganographyKit\ObjectManager;

use Picamator\SteganographyKit\RuntimeException;

/**
 * Creates objects, the main usage inside factories.
 *
 * All objects are unshared, for shared objects please use DI service libraries
 */
class ObjectManager implements ObjectManagerInterface
{
    /**
     * @var array
     */
    private $reflectionContainer;

    /**
     * @var ObjectManagerInterface | null
     */
    private static $instance = null;

    /**
     * Singleton implementation require private construct
     */
    private function __construct()
    {

    }

    /**
     * Singleton implementation require private clone
     */
    private function __clone()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function create($className, array $arguments = [])
    {
        if (empty($arguments)) {
            return new $className();
        }

        // construction does not available
        if (method_exists($className, '__construct') === false) {
            throw new RuntimeException(sprintf('Class "%s" does not have __construct', $className));
        }

        return $this->getReflection($className)
            ->newInstanceArgs($arguments);
    }

    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    static public function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    static function setInstance(ObjectManagerInterface $instance)
    {
        self::$instance = $instance;
    }

    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    static function cleanInstance()
    {
        self::$instance = null;
    }

    /**
     * Retrieve reflection.
     *
     * @param string $className
     *
     * @return \ReflectionClass
     */
    private function getReflection($className)
    {
        if (empty($this->reflectionContainer[$className])) {
            $this->reflectionContainer[$className] = new \ReflectionClass($className);
        }

        return $this->reflectionContainer[$className];
    }
}
