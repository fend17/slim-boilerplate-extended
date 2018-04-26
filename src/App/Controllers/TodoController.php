<?php

namespace App\Controllers;

class TodoController
{
    private $db;

    /**
     * Dependeny Injection (DI): http://www.phptherightway.com/#dependency_injection
     * If this class relies on a database-connection via PDO we inject that connection
     * into the class at start. If we do this TodoController will be able to easily
     * reference the PDO with '$this->db' in ALL functions INSIDE the class
     * This class is later being injected into our container inside of 'App/container.php'
     * This results in we being able to call '$this->get('Todos')' to call this class
     * inside of our routes.
     */
    public function __construct(\PDO $pdo)
    {
        $this->db = $pdo;
    }

    public function getAll()
    {
        $getAll = $this->db->prepare('SELECT * FROM todos');
        $getAll->execute();
        return $getAll->fetchAll();
    }

    public function getOne($id)
    {
        $getOne = $this->db->prepare('SELECT * FROM todos WHERE id = :id');
        $getOne->execute([':id' => $id]);
        return $getOne->fetch();
    }

    public function add($todo)
    {
        /**
         * Default 'completed' is false so we only need to insert the 'content'
         */
        $addOne = $this->db->prepare(
            'INSERT INTO todos (content) VALUES (:content)'
        );

        /**
         * Insert the value from the parameter into the database
         */
        $addOne->execute([':content'  => $todo['content']]);

        /**
         * A INSERT INTO does not return the created object. If we want to return it to the user
         * that has posted the todo we must build it ourself or fetch it after we have inserted it
         * We can always get the last inserted row in a database by calling 'lastInsertId()'-function
         */
        return [
          'id'          => (int)$this->db->lastInsertId(),
          'content'     => $todo['content'],
          'completed'   => false
        ];
    }
}
