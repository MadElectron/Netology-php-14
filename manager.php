<?php 
    require_once 'Controller.php';

    if(!($_SESSION['user'] ?? '')) {
        http_response_code(403);
        echo 'Вход только для авторизованных пользователей!';
        exit;
    }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Task Manager</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>

    <div class="container">
        <header id="header">
            <div class="container">
                <p>Пользователь:&nbsp; <?= $user['login'] ?> | 
                    <a href="logout.php">Выйти</a>
                </p>
            </div>
        </header>
        
        <h1>Task Manager</h1>

        <form action="" method="post" accept-charset="utf-8">
            <?php if($_POST['edit'] ?? '') : ?>
                <?php $editRow = $task->findTask($_POST['edit']); ?>
                <input type="text" name="description" value="<?= $editRow['description'] ?>" placeholder="Название" autofocus>
                <input type="hidden" name="editId" value="<?= $_POST['edit'] ?>">
                <input type="submit" name="submit" value="Изменить">
            <?php else : ?>
                <input type="text" name="description" value="<?= $_POST['name'] ?? '' ?>" placeholder="Название">
                <input type="submit" name="submit" value="Добавить">
            <?php endif; ?>
        </form>

        <h2>Созданные задачи</h2>
        <?php  if (!$myTasks) : ?>
            <p><em>Нет задач</em></p>
        <?php else : ?>
        <table>
            <thead>
                <tr>
                    <th>№ п/п</th>
                    <th>ID задачи</th>
                    <th>
                        Описание задачи
                        <form action="" method="post" accept-charset="utf-8">
                            <input type="hidden" name="column" value="<?= (($_POST['column'] ?? '') == 'description asc' ? 'description desc' : 'description asc') ?>">
                            <button class="filter" type="submit" value="sort">
                                <?= (($_POST['column'] ?? '') == 'description asc' ? '&#x25BC;' : '&#x25B2;') ?>
                            <button class="filter" type="submit" value="sort" ?>
                            </button>
                        </form>
                    </th>
                    <th>
                        Дата добавления
                        <form action="" method="post" accept-charset="utf-8">
                            <input type="hidden" name="column" value="<?= (($_POST['column'] ?? '') == 'date_added asc' ? 'date_added desc' : 'date_added asc') ?>">
                            <button class="filter" type="submit" value="sort">
                                <?= (($_POST['column'] ?? '') == 'date_added asc' ? '&#x25BC;' : '&#x25B2;') ?>
                            </button>
                        </form>

                    </th>
                    <th>
                        Статус
                        <form action="" method="post" accept-charset="utf-8">
                            <input type="hidden" name="column" value="<?= (($_POST['column'] ?? '') == 'is_done asc' ? 'is_done desc' : 'is_done asc') ?>">
                            <button class="filter" type="submit" value="sort">
                                <?= (($_POST['column'] ?? '') == 'is_done asc' ? '&#x25BC;' : '&#x25B2;') ?>
                            </button>
                        </form>                        
                    </th>
                    <th>Действия</th>

                    <th>Ответственный</th>
                    <th>Автор</th>
                    <th>Делегировать</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($myTasks as $index => $row) : ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= $row['id'] ?></td>
                        <td><?= $row['description'] ?></td>
                        <td><?= $row['date_added'] ?></td>
                        <?= $row['is_done'] ? '<td class="task-done">Выполнено</td>': '<td class="task-progress">В процессе</td>' ?>
                        <td>
                            <form action="" method="post" accept-charset="utf-8">
                                <button type="submit" name="done" value="<?= $row['id']  ?>" 
                                    <?= 
                                    $row['is_done'] || 
                                    $row['assigned_user'] != $user['login'] ? 'disabled' : '' 
                                    ?>
                                >
                                    Выполнить
                                </button>
                            </form>
                            <form action="" method="post" accept-charset="utf-8">
                                <button type="submit" name="edit" value="<?= $row['id'] ?>">
                                    Изменить
                                </button>
                            </form>                            
                            <form action="" method="post" accept-charset="utf-8">
                                <button type="submit" name="delete" value="<?= $row['id'] ?>" onclick="confirm('Вы действительно хотите удалить задание &laquo;<?php echo $row['description']; ?>&raquo;')">
                                    Удалить
                                </button>
                            </form>
                        </td>

                        <td><?= $row['assigned_user'].($row['assigned_user'] == $user['login'] ? ' (Вы)' : '') ?></td>
                        <td><?= $user['login'].' (Вы)' ?></td>
                        <td>
                            <form action="" method="post" accept-charset="utf-8">
                                <select name="assignedUser">
                                <?php foreach($users->findAll() as $u) : ?>
                                    <option value="<?= $u['id'] ?>">
                                        <?= $u['login'] ?>
                                    </option>
                                <?php endforeach ?>
                                </select>
                                <button type="submit" name="assign" value="<?= $row['id'] ?>">Ок</button>  
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table> 
        <?php endif; ?>


        <h2>Делегированные задачи</h2>
        <?php  if (!$assignedTasks) : ?>
            <p><em>Нет задач</em></p>
        <?php else : ?>
        <table>
            <thead>
                <tr>
                    <th>№ п/п</th>
                    <th>ID задачи</th>
                    <th>Описание задачи</th>
                    <th>Дата добавления</th>
                    <th>Статус</th>
                    <th>Действия</th>
                    <th>Ответственный</th>
                    <th>Автор</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($assignedTasks as $index => $row) : ?>                
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['description'] ?></td>
                    <td><?= $row['date_added'] ?></td>
                    <?= $row['is_done'] ? '<td class="task-done">Выполнено</td>': '<td class="task-progress">В процессе</td>' ?>
                    <td>
                        <form action="" method="post" accept-charset="utf-8">
                            <button type="submit" name="done" value="<?= $row['id']  ?>" <?= $row['is_done'] ? 'disabled' : '' ?>>
                                Выполнить
                            </button>
                        </form>
                    </td>

                    <td><?= $user['login'].' (Вы)' ?></td>
                    <td><?= $row['user'] ?></td>                
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</body>
</html>

