<?php
// Включение отображения ошибок для отладки
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Фильтрация входных данных
$authName = filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);
$authPass = filter_var(trim($_POST['pass']), FILTER_SANITIZE_STRING);
$authEmail = filter_var(trim($_POST['email']), FILTER_SANITIZE_STRING);

// Подключение к базе данных
$mysql = new mysqli('localhost', 'root', 'root', 'db4');

// Поиск пользователя по имени и email
$authQuery = $mysql->prepare("SELECT * FROM `users` WHERE `name` = ? AND `email` = ?");
$authQuery->bind_param("ss", $authName, $authEmail);
$authQuery->execute();
$result = $authQuery->get_result();

if ($result->num_rows == 0) {
    echo "Пользователь не найден";
    exit();
}

// Получение данных о пользователе
$userData = $result->fetch_assoc();

// Проверка наличия ключа 'pass' в массиве $userData
if (!isset($userData['pass'])) {
    echo "Ошибка: поле 'pass' отсутствует в данных пользователя.";
    exit();
}

// Сравнение хешированного пароля из базы данных с введенным паролем
if (!password_verify($authPass, $userData['pass'])) {
    echo "Неправильный пароль";
    exit();
}

// Начало сессии
session_start();

// Установка данных пользователя в сессии
$_SESSION['user_id'] = $userData['id'];
$_SESSION['user_name'] = $userData['name'];
$_SESSION['user_email'] = $userData['email'];

// Закрытие подключения к базе данных
$authQuery->close();
$mysql->close();

// Перенаправление на главную страницу через заголовок
header("Location: /index.html");
exit();
?>
