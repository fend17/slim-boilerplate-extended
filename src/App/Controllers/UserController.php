<?php

namespace App\Controllers;

class UserController
{
    private $db;
    
    public function __construct($pdo)
    {
        $this->db = $pdo;
    }

    public function getAll()
    {
        $getAllUsers = $this->db->prepare("SELECT * FROM users");
        $getAllUsers->execute();
        $allUsers = $getAllUsers->fetchAll();
        return $allUsers;
    }

    public function getOne($id)
    {
        $getOneUser = $this->db->prepare("SELECT * FROM users WHERE id = :id");
        $getOneUser->execute([
          ":id" => $id
        ]);
        // Fetch -> single resource
        $oneUser = $getOneUser->fetch();
        return $oneUser;
    }
}
