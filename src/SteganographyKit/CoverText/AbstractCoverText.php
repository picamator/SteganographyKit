<?php
/**
 * Abstract for Cover Text
 * 
 * @link        https://github.com/picamator/SteganographyKit
 * @license     http://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace SteganographyKit\CoverText;
use SteganographyKit\Options\OptionsTrait;

abstract class AbstractCoverText implements CoverTextInterface 
{
    use OptionsTrait;
   
    /**
     * @param array $options
     */
    public function __construct(array $options) 
    {
        $this->setOptions($options);
    }
}