<?php 

class Task {
    private static $allowedOrders = ['asc', 'desc'];
    private static $allowedColumns = ['description', 'date_added', 'is_done'];

    private $pdo;

    private static function checkedColumnAndOrder($columnOrder) {
        list($column, $order) = explode(' ', $columnOrder);

        return in_array($column, self::$allowedColumns) && in_array($order, self::$allowedOrders) ? $columnOrder : 'id asc';
    }

    public function __construct($pdo) {
        $this->pdo = $pdo;

    }

    public function findAllOrderBy($columnOrder)
    {
        $columnOrder = self::checkedColumnAndOrder($columnOrder);

        $query = "SELECT * from task
            ORDER BY $columnOrder;
            ";
        $prepquery = $this->pdo->prepare($query);
        $prepquery->execute();

        return $prepquery->fetchAll();
    }

    public function findByUserOrderBy($user, $columnOrder)
    {
        $columnOrder = self::checkedColumnAndOrder($columnOrder);

        $query = "SELECT task.id as id, user.login as assigned_user, description, is_done, date_added from task
            JOIN user on assigned_user_id = user.id 
            WHERE user_id = :user
            ORDER BY $columnOrder;
            ";
        $prepquery = $this->pdo->prepare($query);
        $prepquery->execute([
            'user' => $user,
        ]);

        return $prepquery->fetchAll();
    }

    public function findByAssignedUserOrderBy($user, $columnOrder)
    {
        $columnOrder = self::checkedColumnAndOrder($columnOrder);

        $query = "SELECT task.id as id, user.login as user, description, is_done, date_added from task
            JOIN user on user_id = user.id
            WHERE assigned_user_id = :user
            AND user_id != :user
            ORDER BY $columnOrder;
            ";
        $prepquery = $this->pdo->prepare($query);
        $prepquery->execute([
            'user' => $user,
        ]);

        return $prepquery->fetchAll();
    }

    public function findTask($id)
    {
        if ($id) {
            $query = "SELECT * from task
                WHERE id = :id;
                ";
            $prepquery = $this->pdo->prepare($query);
            $prepquery->execute([
                'id' => $id,
            ]);    

            return $prepquery->fetch();
        }
        return null;
    }

    public function insertTask($user, $descr) 
    {
        if($descr) {
            $dt = new \Datetime();
            $dt = $dt->format('Y-m-d H:i:s');

            $query = "INSERT into task
            VALUES(null, :user, :user, :descr, 0, :dt); 
                ";
            $prepquery = $this->pdo->prepare($query);
            $prepquery->execute([
                'user' => $user,
                'descr' => $descr,
                'dt' => $dt,
            ]);
        }
    }

    public function completeTask($id) 
    {
        if ($id) {
            $query = "UPDATE task
                set is_done = 1
                where id = :id;
                ";
            $prepquery = $this->pdo->prepare($query);
            $prepquery->execute([
                'id' => $id,
            ]);        
        }            
    }

    public function deleteTask($id) 
    {
        if ($id) {
            $query = "DELETE from task
                WHERE id = :id;
                ";
            $prepquery = $this->pdo->prepare($query);
            $prepquery->execute([
                'id' => $id,
            ]);                        
        }
    }

    public function updateTask($id, $descr)
    {
        if ($id) {
            $query = "UPDATE task
                set description = :descr
                where id = :id;
                ";
            $prepquery = $this->pdo->prepare($query);
            $prepquery->execute([
                'descr' => $descr,
                'id' => $id,
            ]);            
        }
    }

    public function assignTask($id, $userId)
    {
        if ($id) {
            $query = "UPDATE task
                set assigned_user_id = :user
                where id = :id;
                ";
            $prepquery = $this->pdo->prepare($query);
            $prepquery->execute([
                'user' => $userId,
                'id' => $id,
            ]);            
        }

    }
}   