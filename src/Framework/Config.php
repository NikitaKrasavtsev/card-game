<?php
namespace Framework; 

use Framework\Exceptions\InternalServerErrorException;

class Config
{
    private $_params; 
    
    private $_pathToConfig;

    public function __construct($pathToConfig)
    {
        $this->_pathToConfig = $pathToConfig;
    }
    
    public function load()
    {
        $this->_params = parse_ini_file($this->_pathToConfig, true);
        
        if ($this->_params === false) {
            throw new InternalServerErrorException;
        }
        
        return $this;
    }
 
    public function getParams()
    {
        return $this->_params;
    }
    
    public function get($group, $key, $default = null)
    {
        if (isset($this->_params[$group][$key])) {
            return $this->_params[$group][$key];
        }
        
        return $default;
    }
    
    public function getGroup($group, $default = null)
    {        
        if (isset($this->_params[$group])) {
            return $this->_params[$group];
        }
        
        return $default;
    }
}