<?php
namespace Framework; 

class Request
{
    private $_getParams; 
    
    private $_postParams;
    
    public function __construct()
    {
        $this->_getParams = $_GET; 
        $this->_postParams = $_POST; 
    }
    
    public function isAjax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) 
            && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest');
    }
    
    public function isPost()
    {
        return $this->getMethod() == 'POST';
    }
    
    public function isGet()
    {
        return $this->getMethod() == 'GET';
    }
    
    public function getMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }
    
    public function getGetParam($key, $default = null)
    {
        return $this->getParam($this->_getParams, $key, $default);        
    }
    
    public function getPostParam($key, $default = null)
    {
         return  $this->getParam($this->_postParams, $key, $default);
    }
    
    private function getParam($arr, $key, $default)
    {
        if (isset($arr[$key])) {
            return $arr[$key];
        }
        
        return $default;
    }
}