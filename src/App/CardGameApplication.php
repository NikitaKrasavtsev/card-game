<?php

namespace App;

use Framework\Application;
use Framework\Renderer;

class CardGameApplication extends Application
{    
    public function getDir()
    {
        return __DIR__;
    }
    
    public function getNamespace()
    {
        return __NAMESPACE__;
    }
    
    protected function error(\Exception $exc) 
    {
        $renderer = new Renderer();
        $content = $renderer->render('pages/error', array('exception' => $exc));
        echo $renderer->render('layouts/default', array('content' => $content));
    }
}