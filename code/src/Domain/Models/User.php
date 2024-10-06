<?php

namespace Geekbrains\Homework\Domain\Models;

use Geekbrains\Homework\Application\Application;

class User {

    private ?int $idUser;

    private ?string $userName;

    private ?string $userLastName;

    private ?int $userBirthday;

    public function __construct(string $name = null, string $lastName = null, int $birthday = null, int $id_user = null){
        $this->userName = $name;
        $this->userLastName = $lastName;
        $this->userBirthday = $birthday;
        $this->idUser = $id_user;
    }

    public function setUserId(int $id_user): void {
        $this->idUser = $id_user;
    }

    public function getUserId(): ?int {
        return $this->idUser;
    }

    public function setName(string $userName) : void {
        $this->userName = $userName;
    }

    public function setLastName(string $userLastName) : void {
        $this->userLastName = $userLastName;
    }

    public function getUserName(): string {
        return $this->userName;
    }

    public function getUserLastName(): string {
        return $this->userLastName;
    }

    public function getUserBirthday(): int {
        return $this->userBirthday;
    }
    
    public function setBirthdayFromString(string $birthdayString) : void {
        $this->userBirthday = strtotime($birthdayString);
    }

    public static function getAllUsersFromStorage(): array {
        $sql = "SELECT * FROM users";
        $handler = Application::$storage->get()->prepare($sql);
        $handler->execute();
        $result = $handler->fetchAll();
        $users = [];
        foreach($result as $item){
            $user = new User($item['user_name'], $item['user_lastname'], $item['user_birthday_timestamp'], $item['id_user']);
            $users[] = $user;
        }
        return $users;
    }

    public function saveToStorage(){
        $sql = "INSERT INTO users(user_name, user_lastname, user_birthday_timestamp) VALUES (:user_name, :user_lastname, :user_birthday)";
        $handler = Application::$storage->get()->prepare($sql);
        $handler->execute([
            'user_name' => $this->userName,
            'user_lastname' => $this->userLastName,
            'user_birthday' => $this->userBirthday
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
        return isset($_POST['name']) && !empty($_POST['name'])
            && preg_match('/^[A-ZА-Я][a-zа-я]+$/u', $_POST['name'])
            && isset($_POST['lastname']) && !empty($_POST['lastname'])
            && preg_match('/^[A-ZА-Я][a-zа-я]+$/u', $_POST['lastname'])
            && isset($_POST['birthday']) && !empty($_POST['birthday'])
            && preg_match('/^(\d{2}-\d{2}-\d{4})$/', $_POST['birthday'])
            && isset($_SESSION['csrf_token'])
            && $_SESSION['csrf_token'] == $_POST['csrf_token'];
    }

    public function setParamsFromRequestData(): void {
        if ($_POST['id'] != '') {
            $this->idUser = (int) htmlspecialchars($_POST['id']);
        }
        $this->userName = htmlspecialchars($_POST['name']);
        $this->userLastName = htmlspecialchars($_POST['lastname']);
        $this->setBirthdayFromString($_POST['birthday']); 
    }

}