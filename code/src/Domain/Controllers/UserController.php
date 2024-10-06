<?php

namespace Geekbrains\Homework\Domain\Controllers;

use Exception;
use Geekbrains\Homework\Application\Application;
use Geekbrains\Homework\Application\Render;
use Geekbrains\Homework\Domain\Models\User;
use Geekbrains\Homework\Application\Auth;


class UserController extends AbstractController {


    protected array $actionsPermissions = [
        'actionHash' => ['admin', 'guest'],
        'actionSave' => ['admin']
    ];


    public function actionIndex() {
        $users = User::getAllUsersFromStorage();
        $render = new Render();
        return $render->renderPage(
            'user-index.twig', 
            [
                'title' => 'Список пользователей в хранилище',
                'users' => $users
            ]);
    }


    public function actionCreate(): string {
        $render = new Render();
        return $render->renderPageWithForm(
                'user-form.twig', 
                [
                    'title' => 'Форма создания пользователя',
                    'action' => 'save',
                    'editing' => false
                ]);
    }


    public function actionEdit(): string {
        if(User::exists($_POST['id'])) {
            $render = new Render();
            return $render->renderPageWithForm(
                'user-form.twig', 
                [
                    'title' => 'Форма создания пользователя',
                    'action' => 'update',
                    'editing' => true,
                    'id' => $_POST['id'],
                    'login' => $_POST['login'],
                    'name' => $_POST['name'],
                    'lastname' => $_POST['lastname'],
                    'birthday' => $_POST['birthday'],
                    'password' => $_POST['password']
                ]);
        }
        else {
            throw new Exception("Пользователь не существует");
        }
    }


    public function actionDelete(): string {
        if(User::exists($_POST['id'])) {
            User::deleteFromStorage($_POST['id']);
            return $this->actionIndex();
        }
        else {
            throw new Exception("Пользователь не существует");
        }
    }


    public function actionSave(): string {
        if(User::validateRequestData()) {
            $user = new User();
            $user->setParamsFromRequestData();
            $user->saveToStorage();
            return $this->actionIndex();
        }
        else {
            throw new Exception("Переданные данные некорректны");
        }
    }

    public function actionUpdate(): string {
        if(User::validateRequestData()) {
            $user = new User();
            $user->setParamsFromRequestData();
            $user->updateInStorage();
            return $this->actionIndex();
        }
        else {
            throw new Exception("Переданные данные некорректны");
        }
    }


    public function actionAuth(): string {
        $render = new Render();
        return $render->renderPageWithForm(
                'user-auth.twig', 
                [
                    'title' => 'Форма логина'
                ]);
    }

    public function actionLogin(): string {
        $result = isset($_POST['login']) 
            && isset($_POST['password'])
            && Application::$auth->proceedAuth($_POST['login'], $_POST['password']);
        if(!$result){
            echo "Не залогинились однако";
            $render = new Render();
            return $render->renderPageWithForm(
                'user-auth.twig', 
                [
                    'title' => 'Форма логина',
                    'auth-success' => false,
                    'auth-error' => 'Неверные логин или пароль'
                ]);
        }
        else{
            header('Location: /');
            return "";
        }
    }





}