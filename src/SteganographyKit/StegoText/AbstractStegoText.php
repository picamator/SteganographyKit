<?php
/**
 * Abstract for Stego Text
 * 
 * @link        https://github.com/picamator/SteganographyKit
 * @license     http://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace SteganographyKit\StegoText;
use SteganographyKit\Options\OptionsTrait;
use SteganographyKit\Image\GeneralTrait;

abstract class AbstractStegoText implements StegoTextInterface 
{
    use OptionsTrait, GeneralTrait;

    /**
     * @param array $options
     */
    public function __construct(array $options) 
    {
        self::validateGbLib();
    }
}