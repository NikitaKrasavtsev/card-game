<?php
$ds = DIRECTORY_SEPARATOR; 

require __DIR__ . $ds . 'src' . $ds . 'Framework' . $ds . 'ClassLoader.php'; 
$paramsLoader = new Framework\ClassLoader(__DIR__ . $ds . 'src'); 
$paramsLoader->register();

$config = new Framework\Config(__DIR__ . $ds . 'src' . $ds . 'App' . $ds . 'Config' . $ds . 'config.ini');
$application = new App\CardGameApplication($config);
$application->run();