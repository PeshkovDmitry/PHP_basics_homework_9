<?php

namespace Geekbrains\Homework\Domain\Controllers;

use Geekbrains\Homework\Application\Application;

class AbstractController {

    protected array $actionsPermissions = [];
    

    public function getUserRoles(): array{
        $roles = [];
        if(isset($_SESSION['id_user'])){
            $result = $this->getRolesFromDB();
            if(!empty($result)){
                foreach($result as $role){
                    $roles[] = $role['role'];
                }
            }
        }      
        return $roles;
    }

    private function getRolesFromDB() : array {
        $rolesSql = "SELECT * FROM user_roles WHERE id_user = :id";
        $handler = Application::$storage->get()->prepare($rolesSql);
        $handler->execute(['id' => $_SESSION['id_user']]);
        return $handler->fetchAll();
    }


    public function getActionsPermissions(string $methodName): array {
        return isset($this->actionsPermissions[$methodName]) ? $this->actionsPermissions[$methodName] : [];
    }
}