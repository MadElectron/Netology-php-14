<?php 

class User {
    private $pdo;
    private $id;
    private $login;
    private $pass;
    private static $allowedColumns = ['id', 'login', 'password'];

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function findAll() {
        $query = "SELECT * FROM user";

        $prepquery = $this->pdo->prepare($query);
        $prepquery->execute();

        return $prepquery->fetchAll();
    }

    public function find($id)
    {
        $query = "SELECT * FROM user
            WHERE id = :id
            LIMIT 1
        ";

        $prepquery = $this->pdo->prepare($query);
        $prepquery->execute([
            'id' => $id,
        ]);

        return $prepquery->fetch();
    } 

    public function findOneBy(array $columns)
    {
        $query = "SELECT * FROM user WHERE 1";

        foreach(array_keys($columns) as $column) {
            if(in_array($column, self::$allowedColumns)) {

                $query .= " AND $column = :$column";
            }
        }

        $prepquery = $this->pdo->prepare($query);
        $prepquery->execute($columns);

        return $prepquery->fetch();
    }

    public function add($login, $pass)
    {
        $query = "INSERT INTO user (login, password)
            VALUES (:login, :password)
        ";     

        $prepquery = $this->pdo->prepare($query);
        $prepquery->execute(['login' => $login, 'password' => $pass]);
    }



}