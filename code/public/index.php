<?php

require_once '../vendor/autoload.php';

use Geekbrains\Homework\Application\Application;
use Geekbrains\Homework\Application\Render;

try{
    $app = new Application();
    echo $app->run();
}
catch(Exception $e){
    echo Render::renderExceptionPage($e);
}