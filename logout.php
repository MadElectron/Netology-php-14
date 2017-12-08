<?php 
    session_start();
    session_destroy();

    echo 'Вы вышли из аккаунта. Перемещаю на страницу авторизации...';
    header('Refresh: 1 Url="index.php"');
