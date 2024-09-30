<?php

namespace Geekbrains\Homework\Domain\Controllers;
use Geekbrains\Homework\Application\Render;

class PageController {

    public function actionIndex() {
        $render = new Render();
        
        return $render->renderPage('page-index.twig', ['title' => 'Главная страница']);
    }
}