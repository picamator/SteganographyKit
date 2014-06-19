<?php
/**
 * Abstract for Secret Text
 * 
 * @link        https://github.com/picamator/SteganographyKit
 * @license     http://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace SteganographyKit\SecretText;
use SteganographyKit\Options\OptionsTrait;

abstract class AbstractSecretText implements SecretTextInterface 
{
    use OptionsTrait;
    
    /**
     * Mark that is added to end of the text
     * it helps to identify where secret text end
     */
    const END_TEXT_MARK = '0000000';
}