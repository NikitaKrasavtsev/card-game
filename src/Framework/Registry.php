<?php

namespace Framework; 

class Registry
{
    private $_params = array(); 

    private static $_instance;    
       
    public static function instance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new Registry; 
        }
        return self::$_instance;
    }

    public function get($key, $default = null)
    {
        if (!isset($this->_params[$key])) {
            return $default; 
        }
        
        return $this->_params[$key];
    }
    
    public function set($key, $value) 
    {
        $this->_params[$key] = $value; 
        
        return $this;
    }
    
    private function __construct(){}
    
    private function __clone(){}
    
    private function __wakeup(){}
}
