<?php
    require_once 'Task.php';
    require_once 'User.php';

    session_start();

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

    // Task manager page
    if ($_SESSION['user'] ?? '') {


    $users = new User($pdo);
    $userId = $_SESSION['user'];
    $user = $users->find($userId);

    $descr = $_POST['description'] ?? '';
    $doneId = $_POST['done'] ?? '';
    $deleteId = $_POST['delete'] ?? '';
    $editId = $_POST['editId'] ?? '';
    $assignId = $_POST['assign'] ?? '';
    $assignedUserId = $_POST['assignedUser'] ?? '';

    $task = new Task($pdo);

    if($descr) {
        if($editId) {
            $task->updateTask($editId, $descr);
        } else {
            $task->insertTask($userId, $descr);
        }
    } 
    if($doneId) {
        $task->completeTask($doneId);
    }
    if($deleteId) {
        $task->deleteTask($deleteId);
    }
    if ($assignedUserId) {
        $task->assignTask($assignId, $assignedUserId);
    }

    $columnOrder = $_POST['column'] ?? 'id asc';
    $myTasks = $task->findByUserOrderBy($userId, $columnOrder);
    $assignedTasks = $task->findByAssignedUserOrderBy($userId, $columnOrder);
}