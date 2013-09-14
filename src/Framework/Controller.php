<?php

namespace Framework;

use Framework\Exceptions\NotFoundException;
use Framework\Exceptions\InternalServerErrorException;

abstract class Controller
{
    public $registry;     
    
    protected $_renderer;
    
    protected $_layout;
    
    protected $_renderParams = array(
        'title' => '',
        'content' => '',
    );
    
    public function __construct()
    {
        $this->registry = Registry::instance();         
        
        $this->_renderer = new Renderer; 
    }    
    
    abstract public function indexAction();
    
    protected function setLayout($layout)
    {
        $this->_layout = $layout; 
        
        return $this;
    }       
    
    protected function render($view, $params = array(), $withLayout = true)
    {
        $content = $this->renderPartial($view, $params, false);
        if (!$withLayout) {
            echo $content;
            
            return;
        }
        
        $this->setRenderParam('content', $content);
        
        echo $this->_renderer->render($this->_layout, $this->_renderParams);
        
        return $this;
    }
    
    protected function renderPartial($view, $params = array(), $doRender = true)
    {
        $content = $this->_renderer->render($view, $params);
        
        if ($doRender) {
            echo $content; 
        } else {
            return $content;
        }
        
        return $this;
    }
    
    protected function setRenderParam($key, $value)
    {
        if (!isset($this->_renderParams[$key])) {
           throw new InternalServerErrorException;
        }
        
        $this->_renderParams[$key] = $value;
        
        return $this;
    }
    
    protected function json($data)
    {
        echo json_encode($data);
        
        return $this;
    }    

    protected function redirect($url = '', $relative = true)
    {     
        if (!$relative) {
            return header('Location: ' . $url);
        }
        
        $url = trim($url, '/');
        return header('Location: ' . $this->get('router')->getBaseUrl() . '/' . $url);
    }
    
    protected function error($code)
    {       
        if ($code == 404) {
            throw new NotFoundException;
        }
        
        if ($code == 500) {
            throw new InternalServerErrorException;
        }
    }
    
    protected function get($key, $default = null) 
    {
        return $this->registry->get($key, $default);
    }
}