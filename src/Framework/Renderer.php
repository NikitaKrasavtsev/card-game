<?php

namespace Framework; 

use Framework\Exceptions\InternalServerErrorException;

class Renderer
{
    const VIEWS_DIR = 'Views'; 
    
    public $registry; 
    
    private $_pathToViews;
    
    private $_router;
    
    public function __construct()
    {                
        $this->registry  = Registry::instance();
        
        $this->_pathToViews = $this->get('app')->getDir() . DIRECTORY_SEPARATOR . self::VIEWS_DIR;

        if (!is_readable($this->_pathToViews)) {
            throw new InternalServerErrorException;
        }
                
        $this->_router = $this->registry->get('router');
    }
    
    public function render($view, $params)
    {
        $view = trim($view, '\\/');
        $pathToViewFile = $this->_pathToViews . DIRECTORY_SEPARATOR . $view. '.php';

        if(!is_readable($pathToViewFile)) {
            throw new InternalServerErrorException;
        }
        
        return $this->getHTML($pathToViewFile, $params);
    }
    
    public function getHTML($pathToViewFile, $params)
    {
        extract($params);
        ob_start();
        include($pathToViewFile);
        $result = ob_get_clean();
        
        return $result;
    }
    
    public function url($routeName, $routeParams = array(), $queryParams = array())
    {
        return $this->_router->generate($routeName, $routeParams, $queryParams);        
    }
    
    public function e($str) {
        return htmlspecialchars($str);
    }
    
    public function get($key, $default = null)
    {
        return $this->registry->get($key, $default);
    }
}