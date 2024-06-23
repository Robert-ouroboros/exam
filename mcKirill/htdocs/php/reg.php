<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Фильтрация входных данных
$insertName = filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);
$insertPass = filter_var(trim($_POST['pass']), FILTER_SANITIZE_STRING);
$insertEmail = filter_var(trim($_POST['email']), FILTER_SANITIZE_STRING);

// Проверка длины логина и email
if (mb_strlen($insertName) < 2 || mb_strlen($insertName) > 90) {
    echo "Недопустимая длина логина";
    exit();
}
if (mb_strlen($insertEmail) < 1 || mb_strlen($insertEmail) > 100) {
    echo "Недопустимая длина почты";
    exit();
}

// Хешируем пароль
$hashedPass = password_hash($insertPass, PASSWORD_DEFAULT);

// Подключение к базе данных
$mysql = new mysqli('localhost', 'root', 'root', 'db4');

if ($mysql->connect_error) {
    die("Ошибка подключения: " . $mysql->connect_error);
}

// Проверка, существует ли уже пользователь с таким email
$checkEmailQuery = $mysql->prepare("SELECT * FROM `users` WHERE `email` = ?");
$checkEmailQuery->bind_param("s", $insertEmail);
$checkEmailQuery->execute();
$existingUser = $checkEmailQuery->get_result()->fetch_assoc();

if ($existingUser) {
    echo "Пользователь с таким email уже существует";
    exit();
}

// Добавление нового пользователя в базу данных
$insertUserQuery = $mysql->prepare("INSERT INTO `users` (`name`, `pass`, `email`) VALUES (?, ?, ?)");
$insertUserQuery->bind_param("sss", $insertName, $hashedPass, $insertEmail);
$insertUserQuery->execute();

// Проверяем, был ли пользователь успешно добавлен
if ($insertUserQuery->affected_rows == 1) {
    // Начинаем сессию
    session_start();
    
    // Устанавливаем данные пользователя в сессии
    $_SESSION['user_id'] = $mysql->insert_id;
    $_SESSION['user_name'] = $insertName;
    $_SESSION['user_email'] = $insertEmail;

    // Выводим сообщение об успешной регистрации и перенаправляем на страницу меню
    echo "<script>alert('Регистрация успешна. Добро пожаловать, $insertName! Вы будете перенаправлены на страницу меню.'); window.location.href = '/index.html';</script>";
} else {
    echo "Ошибка при регистрации пользователя";
}

// Закрываем соединение с базой данных
$mysql->close();
?>