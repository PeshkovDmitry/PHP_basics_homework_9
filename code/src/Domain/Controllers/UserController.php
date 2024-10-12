<?php

namespace Geekbrains\Homework\Domain\Controllers;

use Exception;
use Geekbrains\Homework\Application\Application;
use Geekbrains\Homework\Application\Render;
use Geekbrains\Homework\Domain\Models\User;
use Geekbrains\Homework\Application\Auth;
use Geekbrains\Homework\Domain\Controllers\PageController;


class UserController extends AbstractController {


    protected array $actionsPermissions = [
        'actionIndex' => ['admin', 'guest'],
        'actionIndexRefresh' => ['admin', 'guest'],
        'actionCreate' => ['admin'],
        'actionEdit' => ['admin'],
        'actionDelete' => ['admin'],
        'actionSave' => ['admin'],
        'actionUpdate' => ['admin']
    ];

    protected array $alwaysEnabledMethods = ['actionAuth', 'actionLogin', 'actionLogout'];

    public function actionIndex() {
        $users = User::getAllUsersFromStorage();
        $isAdmin = false;
        foreach($users as $user){
            if ($user->getUserId() == $_SESSION['id_user'] && $user->getUserRole() == "admin") {
                $isAdmin = true;                
            }
        }
        $render = new Render();
        return $render->renderPage(
            'user-index.twig', 
            Auth::addSessionData(
                [
                    'title' => 'Список пользователей в хранилище',
                    'users' => $users,
                    'isAdmin' => $isAdmin
                ]));
    }

    public function actionIndexRefresh(){
        $limit = null;
        if(isset($_GET['maxId']) && ($_GET['maxId'] > 0)){
            $limit = (int) $_GET['maxId'];
        }
        $users = User::getAllUsersFromStorage($limit);
        $usersData = [];
        if(count($users) > 0) {
            foreach($users as $user){
                $usersData[] = $user->getUserDataAsArray();
            }
        }
        return json_encode($usersData);
    }


    public function actionCreate(): string {
        $render = new Render();
        return $render->renderPageWithForm(
                'user-form.twig', 
                Auth::addSessionData(
                    [
                        'title' => 'Форма создания пользователя',
                        'action' => 'save',
                        'editing' => false
                    ]));
    }


    public function actionEdit(): string {
        if(User::exists($_POST['id'])) {
            $render = new Render();
            return $render->renderPageWithForm(
                'user-form.twig',
                Auth::addSessionData( 
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
                    ]));
        }
        else {
            throw new Exception("Пользователь не существует");
        }
    }


    public function actionDelete(): string {
        if(User::exists($_POST['id'])) {
            User::deleteFromStorage($_POST['id']);
            // return $this->actionIndex();
            $pageController = new PageController();
            return $pageController->actionIndex();
        }
        else {
            throw new Exception("Пользователь не существует");
        }
    }


    public function actionSave(): string {
        User::validateRequestData();
        $user = new User();
        $user->setParamsFromRequestData();
        $user->saveToStorage();
        // return $this->actionIndex();
        $pageController = new PageController();
        return $pageController->actionIndex();
    }

    public function actionUpdate(): string {
        User::validateRequestData();
        $user = new User();
        $user->setParamsFromRequestData();
        $user->updateInStorage();
        // return $this->actionIndex();
        $pageController = new PageController();
        return $pageController->actionIndex();
    }


    public function actionAuth(): string {
        $render = new Render();
        return $render->renderPageWithForm(
                'user-auth.twig', 
                Auth::addSessionData(
                    [
                        'title' => 'Форма логина'
                    ]));
    }

    public function actionLogin(): string {
        $result = isset($_POST['login']) 
            && isset($_POST['password'])
            && Application::$auth->proceedAuth($_POST['login'], $_POST['password']);
        if(!$result){
            $render = new Render();
            return $render->renderPageWithForm(
                'user-auth.twig', 
                [
                    'title' => 'Форма логина',
                    'auth-fail' => true,
                    'auth-error' => 'Неверные логин или пароль'
                ]);
        }
        else{
            $controller = new PageController;
            return $controller->actionIndex();
        }
    }


    public function actionLogout(): string {
        session_unset();
        $controller = new PageController;
        return $controller->actionIndex();
    }

    


}