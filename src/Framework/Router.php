<?php

namespace Framework; 

use Framework\Exceptions\NotFoundException;
use Framework\Exceptions\InternalServerErrorException;

class Router {
    
    protected $_routes = array();
    
    protected $_baseUrl;
    
    public function __construct($routes)
    {
        $this->setBaseUrl();
        $this->setRoutes($routes);   
    }   
    
    public function generate($routeName, $urlParams = array(), $queryParams = array())
    {
        if (!isset($this->_routes[$routeName])) {
            throw new InternalServerErrorException;
        }
        
        $url = $this->_routes[$routeName];
        
        if ($urlParams) {
            $url .= '/' . implode('/', $urlParams);
        }
        
        if ($queryParams) {
            $url .= '?' . http_build_query($queryParams);
        }    
        
        return $url;
    }
    
    public function handleRequest(Request $request)
    {
        $route = $request->getGetParam('__route__');

        if (!$route) {
            $route = 'index';
        }
                        
        $route = trim($route, '/');
        $routeParts = explode('/', $route);
        
        $registry = Registry::instance();
        $controllersDir = $registry->get('app')->getDir() . DIRECTORY_SEPARATOR . 'Controllers';
        $controllersNS = $registry->get('app')->getNamespace() . '\\' . 'Controllers';
        
        $controller = array_shift($routeParts);
        
        if(!is_file($controllersDir . DIRECTORY_SEPARATOR . ucfirst($controller) .'Controller.php')) {
            array_unshift($routeParts, $controller);
            $controller = 'index';
        }
        
        if (!is_readable($controllersDir . DIRECTORY_SEPARATOR . ucfirst($controller) .'Controller.php')) {
            throw new NotFoundException;
        }

        $controllerClass = $controllersNS . '\\' . ucfirst($controller) . 'Controller';
        $controllerObj = new $controllerClass;
        
        $action = array_shift($routeParts);
        
        if (!$action) {
            $action = 'index';
        }
        
        $action .= 'Action';
        
        $params = $routeParts;
        
        try {
        $reflectionAction = new \ReflectionMethod($controllerObj, $action); 
        } catch (\Exception $exc) {
            throw new NotFoundException;
        }
        
        if ($reflectionAction->getNumberOfParameters() < count($params) || 
                $reflectionAction->getNumberOfRequiredParameters() > count($params)) 
        {
            throw new NotFoundException;
        }         
        
         return $reflectionAction->invokeArgs($controllerObj, $params);
    }
    
    public function getBaseUrl()
    {
        return $this->_baseUrl;
    }
    
    protected function setBaseUrl()
    {
        $this->_baseUrl = 'http://'. implode('/', array_slice(explode('/', $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF']), 0, -1));
        $this->_baseUrl = trim($this->_baseUrl, '/');
        
        return $this;
    }    
    
    protected function setRoutes($routes)
    {
        $this->_routes['index'] = $this->_baseUrl;
        foreach ($routes as $name => $url) {
            $this->_routes[$name] = $this->_baseUrl . '/' . trim($url, '/');
        }                
        
        return $this;
    }    
}
