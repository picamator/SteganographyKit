<?php
/**
 * Interface for Secret Text
 * 
 * @link        https://github.com/picamator/SteganographyKit
 * @license     http://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace SteganographyKit\SecretText;

interface SecretTextInterface 
{
    /**
     * @param array $options
     */
    public function __construct(array $options);
    
    /**
     * Gets converted data to binary format
     * 
     * @return array each eaqual number of bits
     */
    public function getBinaryData();
}
