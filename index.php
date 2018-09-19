<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=base", "root", "");
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
if (!empty($_POST['name']) && !empty($_POST['add'])) {
    $name = $_POST['name'];
    $sql = "CREATE TABLE $name (
      `id` int (11) NOT NULL AUTO_INCREMENT,
      `name` varchar (50) NOT NULL,
      `description` varchar (255) NOT NULL,
      `price` int(11) NOT NULL,
      PRIMARY KEY (`id`)
      ) ENGINE = InnoDB DEFAULT CHARSET = utf8";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
}
$sql = "SHOW TABLES";
$stmt = $pdo->query($sql);
if (!empty($_GET['tables'])) {
    $table = $_GET['tables'];
    $sql = "DESCRIBE $table";
    $stmtDescribe = $pdo->prepare($sql);
    $stmtDescribe->execute();
}
if (isset($_POST['delete'])) {
    $table = $_GET['tables'];
    $fieldName = $_POST['field_name'];
    $sql = "ALTER TABLE $table DROP COLUMN $fieldName";
    $stmtDelete = $pdo->prepare($sql);
    $stmtDelete->execute();
}
if (isset($_POST['change_name'])) {
    if (!empty($_POST['old_name']) && !empty($_POST['new_name'])) {
        $table = $_GET['tables'];
        $oldName = $_POST['old_name'];
        $newName = $_POST['new_name'];
        $type = $_POST['type'];
        $sql = "ALTER TABLE $table CHANGE $oldName $newName $type";
        $stmtChange = $pdo->prepare($sql);
        $stmtChange->execute();
    }
}
if (isset($_POST['change_type'])) {
    if (!empty($_POST['field_name']) && !empty($_POST['new_type'])) {
        $table = $_GET['tables'];
        $fieldName = $_POST['field_name'];
        $newType = $_POST['new_type'];
        $sql = "ALTER TABLE $table MODIFY $fieldName $newType";
        $stmtChange = $pdo->prepare($sql);
        $stmtChange->execute();
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <title>Управление таблицами и базами данных</title>
    <meta charset="utf-8">
</head>
<body>
<h3>Добавить таблицу</h3>
<form method="POST">
    <input type="text" name="name" placeholder="Название таблицы">
    <input type="submit" name="add" value="Добавить">
</form>

<h3>Список таблиц в базе данных</h3>
<ul>
    <?php foreach ($stmt as $item) { ?>
        <li>
            <a href="?tables=<?php echo $item[0]; ?>"><?php echo $item[0]; ?></a>
        </li>
    <?php } ?>
</ul>
<?php if (!empty($_GET['tables'])) { ?>

    <form method="POST">
        <table border="1">
            <tr>
                <th>Поле</th>
                <th>Тип</th>
            </tr>
            <?php while ($row = $stmtDescribe->fetch(PDO::FETCH_ASSOC)) { ?>
                <tr>
                    <td><?php echo $row['Field']; ?></td>
                    <td><?php echo $row['Type']; ?></td>
                </tr>
            <?php } ?>
        </table>
        <br>
        <select name="action">
            <option>Выберите действие:</option>
            <option value="delete">Удалить поле</option>
            <option value="change_name">Изменить название</option>
            <option value="change_type">Изменить тип</option>
        </select>
        <input type="submit" value="Выбрать">

        <?php if ($_POST['action'] == 'delete') : ?>
            <input type="text" name="field_name" placeholder="Имя поля">
            <input type="submit" name="delete" value="Удалить">
        <?php endif; ?>

        <?php if ($_POST['action'] == 'change_name') : ?>
            <input type="text" name="old_name" placeholder="Введите старое название">
            <input type="text" name="new_name" placeholder="Введите новое название">
            <input type="text" name="type" placeholder="Введите тип">
            <input type="submit" name="change_name" value="Изменить название">
        <?php endif; ?>

        <?php if ($_POST['action'] == 'change_type') : ?>
            <input type="text" name="field_name" placeholder="Введите название поля">
            <input type="text" name="new_type" placeholder="Введите новый тип">
            <input type="submit" name="change_type" value="Изменить тип">
        <?php endif; ?>
    </form>
<?php } ?>
</body>
</html>