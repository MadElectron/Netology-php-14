<?php
    require_once 'Task.php';

    // Home database
    $host = 'localhost';
    $dbname = 'netology';
    $user = 'root';
    $pass = 'BJz5c8PI'; 
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, 
    ];

    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8";
    $pdo = new PDO($dsn, $user, $pass, $options);

    $descr = $_POST['description'] ?? '';
    $doneId = $_POST['done'] ?? '';
    $deleteId = $_POST['delete'] ?? '';
    $editId = $_POST['editId'] ?? '';

    $task = new Task($pdo);

    if($descr) {
        if($editId) {
            $task->updateTask($editId, $descr);
        } else {
            $task->insertTask($descr);
        }
    } 
    if($doneId) {
        $task->completeTask($doneId);
    }
    if($deleteId) {
        $task->deleteTask($deleteId);
    }

    $columnOrder = $_POST['column'] ?? 'id asc';
    $queryResult = $task->findAllOrderBy($columnOrder);