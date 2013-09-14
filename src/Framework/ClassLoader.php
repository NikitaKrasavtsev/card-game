<?php
namespace Framework;

use Framework\Exceptions\InternalServerErrorException;

class ClassLoader
{       
    private $_basePath;       
    
    public function __construct($basePath)
    {
        if (!is_readable($basePath)) {
            throw new InternalServerErrorException;
        }
        
        $this->_basePath = $basePath;
    }

    public function register()
    {
        spl_autoload_register(array($this, 'load'));
        
        return $this;
    }
    
    public function load($className)
    {    
        $className = trim($className, '\\');
        $pathToClassFile = str_replace('\\', DIRECTORY_SEPARATOR, $className);
        $fullPathToClassFile = $this->_basePath . DIRECTORY_SEPARATOR . $pathToClassFile . '.php';

        if (is_readable($fullPathToClassFile)) {
            include($fullPathToClassFile);
            
            return true;
        }
        
        return false;
    }
}