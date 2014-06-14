<?php
/**
 * SteganographyKit Autoload
 * 
 * @link        https://github.com/picamator/SteganographyKit
 * @license     http://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace SteganographyKit;

function Autoload($class)
{
    $path = __DIR__ . DIRECTORY_SEPARATOR 
            . str_replace('\\', DIRECTORY_SEPARATOR, $class).'.php';
    if (file_exists($path)) {
        include_once ($path);
        
        return true;
    }
    
    return false;
}

spl_autoload_register(__NAMESPACE__.'\Autoload');