<?php

namespace Framework; 

use Framework\Exceptions\InternalServerErrorException;

abstract class Application
{
    private $_config; 
    
    public function __construct(Config $config)
    {
        $this->_config = $config;        
    }
    
    public function run()
    {
        try {
            $this->setAppParams();
            
            $registry = Registry::instance(); 
            $router = $registry->get('router');
            $request = $registry->get('request');
            
            return $router->handleRequest($request);
        } catch (\Exception $exc) {
            $this->error($exc);
        }        
    }           
    
    abstract public function getDir();
    
    abstract public function getNamespace();

    protected function setAppParams()
    {
        $this->_config->load();
 
        $registry = Registry::instance();

        $registry->set('request', new Request); 

        $registry->set('router', new Router($this->_config->getGroup('routes')));
                
        $registry->set('app', $this);		
		
        try {
            $registry->set('db', $this->createDbConnection());  
        } catch (\PDOException $exc) {
            throw new InternalServerErrorException;
        }

        return $this;
    }
    
    protected function createDbConnection()
    {
        $host = $this->_config->get('db', 'host'); 
        $dbname = $this->_config->get('db', 'dbname');
        $username = $this->_config->get('db', 'username');
        $password = $this->_config->get('db', 'password');
		
        return new \PDO(sprintf('mysql:host=%s;dbname=%s', $host, $dbname), $username, $password);        
    }     
    
    protected function error(\Exception $exc)
    {
        header('Content-Encoding: utf8');
        echo $exc->getMessage();
    }
}