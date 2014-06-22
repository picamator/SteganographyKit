<?php
/**
 * Abstract for Cover Text
 * 
 * @link        https://github.com/picamator/SteganographyKit
 * @license     http://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace SteganographyKit\CoverText;
use SteganographyKit\Options\OptionsTrait;
use SteganographyKit\Image\GeneralTrait;

abstract class AbstractCoverText implements StegoTextInterface 
{
    use OptionsTrait, GeneralTrait;

    /**
     * @param array $options
     */
    public function __construct(array $options) 
    {
        $this->validateGbLib();
    }
}