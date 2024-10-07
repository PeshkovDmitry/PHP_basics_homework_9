<?php

namespace Geekbrains\Homework\Domain\Models;

use Geekbrains\Homework\Application\Application;
use Geekbrains\Homework\Application\Auth;

class User {

    private ?int $idUser;

    private ?string $userLogin;

    private ?string $userName;

    private ?string $userLastName;

    private ?int $userBirthday;

    private ?string $userPasswordHash;

    private ?string $userRole;

    public function __construct(
            int $id = null, 
            string $login = null, 
            string $name = null, 
            string $lastName = null, 
            int $birthday = null, 
            string $passwordHash = null, 
            string $role = null) {
        $this->idUser = $id;
        $this->userLogin = $login;
        $this->userName = $name;
        $this->userLastName = $lastName;
        $this->userBirthday = $birthday;
        $this->userPasswordHash = $passwordHash;
        $this->userRole = $role;
    }

    public function setUserId(int $id_user): void {
        $this->idUser = $id_user;
    }

    public function getUserId(): ?int {
        return $this->idUser;
    }

    public function setUserLogin(int $userLogin): void {
        $this->userLogin = $userLogin;
    }

    public function getUserLogin(): string {
        return $this->userLogin;
    }

    public function setUserName(string $userName) : void {
        $this->userName = $userName;
    }

    public function getUserName(): string {
        return $this->userName;
    }

    public function setLastName(string $userLastName) : void {
        $this->userLastName = $userLastName;
    }

    public function getUserLastName(): string {
        return $this->userLastName;
    }

    public function setBirthdayFromString(string $userBirthdayString) : void {
        $this->userBirthday = strtotime($userBirthdayString);
    }

    public function getUserBirthday(): int {
        return $this->userBirthday;
    }
    
    public function setPasswordHash(string $userPasswordHash) : void {
        $this->userPasswordHash = $userPasswordHash;
    }

    public function getUserPasswordHash(): string {
        return $this->userPasswordHash;
    }

    public function setUserRole(string $userRole) : void {
        $this->userRole = $userRole;
    }

    public function getUserRole(): string {
        return $this->userRole;
    }


    public static function getAllUsersFromStorage(): array {
        $sql = "SELECT * FROM users";
        $handler = Application::$storage->get()->prepare($sql);
        $handler->execute();
        $result = $handler->fetchAll();
        $users = [];
        foreach($result as $item){
            $user = new User(
                $item['id_user'], 
                $item['user_login'], 
                $item['user_name'], 
                $item['user_lastname'],
                $item['user_birthday_timestamp'],
                $item['user_password_hash'],
                $item['user_role']);
            $users[] = $user;
        }
        return $users;
    }

    public function saveToStorage(){
        $sql = "INSERT INTO users(user_login, user_name, user_lastname, user_birthday_timestamp, user_password_hash, user_role) VALUES (:user_login, :user_name, :user_lastname, :user_birthday, :user_password_hash, :user_role)";
        $handler = Application::$storage->get()->prepare($sql);
        $handler->execute([
            'user_login' => $this->userLogin,
            'user_name' => $this->userName,
            'user_lastname' => $this->userLastName,
            'user_birthday' => $this->userBirthday,
            'user_password_hash' => $this->userPasswordHash,
            'user_role' => $this->userRole
        ]);
    }


    public function updateInStorage(): void{
        $sql = "UPDATE users SET user_name = :user_name, user_lastname = :user_lastname, user_birthday_timestamp = :user_birthday WHERE id_user = :id";
        $handler = Application::$storage->get()->prepare($sql);
        $handler->execute([
            'id' => $this->idUser, 
            'user_name' => $this->userName,
            'user_lastname' => $this->userLastName,
            'user_birthday' => $this->userBirthday
        ]);
    }


    public static function deleteFromStorage(int $user_id) : void {
        $sql = "DELETE FROM users WHERE id_user = :id_user";
        $handler = Application::$storage->get()->prepare($sql);
        $handler->execute(['id_user' => $user_id]);
    }


    public static function exists(int $id): bool{
        $sql = "SELECT count(id_user) as user_count FROM users WHERE id_user = :id_user";
        $handler = Application::$storage->get()->prepare($sql);
        $handler->execute([
            'id_user' => $id
        ]);
        $result = $handler->fetchAll();
        return (count($result) > 0 && $result[0]['user_count'] > 0);
    }



    public static function validateRequestData(): bool{
        return isset($_POST['login']) && !empty($_POST['login'])
            && preg_match('/^[A-ZА-Яa-zа-я]+$/u', $_POST['login'])
            && isset($_POST['name']) && !empty($_POST['name'])
            && preg_match('/^[A-ZА-Я][a-zа-я]+$/u', $_POST['name'])
            && isset($_POST['lastname']) && !empty($_POST['lastname'])
            && preg_match('/^[A-ZА-Я][a-zа-я]+$/u', $_POST['lastname'])
            && isset($_POST['birthday']) && !empty($_POST['birthday'])
            && preg_match('/^(\d{2}-\d{2}-\d{4})$/', $_POST['birthday'])
            && isset($_POST['password']) && !empty($_POST['password'])
            && isset($_SESSION['csrf_token'])
            && $_SESSION['csrf_token'] == $_POST['csrf_token'];
    }
    

    public function setParamsFromRequestData(): void {
        if ($_POST['id'] != '') {
            $this->idUser = htmlspecialchars($_POST['id']);
        }
        $this->userLogin = htmlspecialchars($_POST['login']);
        $this->userName = htmlspecialchars($_POST['name']);
        $this->userLastName = htmlspecialchars($_POST['lastname']);
        $this->setBirthdayFromString($_POST['birthday']); 
        $this->userPasswordHash = Auth::getPasswordHash($_POST['password']);
        $this->userRole = "guest";
    }

}