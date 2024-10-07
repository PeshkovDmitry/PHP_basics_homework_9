<?php

namespace Geekbrains\Homework\Domain\Controllers;
use Geekbrains\Homework\Application\Render;
use Geekbrains\Homework\Application\Auth;

class PageController {

    public function actionIndex() {
        $render = new Render();
        return $render->renderPage('page-index.twig', Auth::addSessionData(['title' => 'Главная страница'])); 
            
    }
}