<?php 
    session_start();
    session_destroy();

    header('Refresh: 1 Url="index.php"');
    echo 'Вы вышли из аккаунта. Перемещаю на страницу авторизации...';
