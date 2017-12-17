<?php  
    require_once 'User.php';
    require_once 'Controller.php';

    if ($_POST['login'] ?? '' && $_POST['pass'] ?? '') {

        $login = $_POST['login'];
        $pass = $_POST['pass'];

        $user = new User($pdo);

        if ($_POST['action'] == 'Зарегистрироваться') {
            $selectedUser = $user->findOneBy([
                'login' => $login,
            ]);

            if ($selectedUser) {
                $errorMsg = "Пользователь с таким именем уже зарегистрирован";
            }
            else {
                $user->add($login, $pass);
            }
        } elseif ($_POST['action'] == 'Войти') {
            $selectedUser = $user->findOneBy([
                'login' => $login,
                'password' => $pass
            ]);
            if($selectedUser) {
                $_SESSION['user'] = $selectedUser['id'];
                header('Location:manager.php');
            } else {
                $errorMsg = "Неверный логин или пароль";
            }
        }
    } 
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Авторизация</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <div class="login">
            <h2>Авторизация</h2>
            <p class="error"><?= nl2br($errorMsg ?? '') ?></p>

            <form action="" method="post" accept-charset="utf-8">
                <input type="text" name="login" value="<?= $_POST['login'] ?? '' ?>" placeholder="Логин" autofocus required>
                <input type="password" name="pass" value="" placeholder="Пароль" required>
                <input type="submit" name="action" value="Войти">
                <input type="submit" name="action" value="Зарегистрироваться">
            </form>    
        </div>
    </div>
</body>
</html>