<?php

    // Получение данных из формы (личные данные)
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $message = htmlspecialchars($_POST['message']);

    $mysql = new mysqli('localhost', 'root', 'root', 'feedback');

    // Добавление сообщения пользователя в базу данных
$insertMessageQuery = $mysql->prepare("INSERT INTO `messages` (`name`, `email`, `message`) VALUES (?, ?, ?)");
$insertMessageQuery->bind_param("sss", $insertname, $insertemail, $insertmessage);
$insertMessageQuery->execute();

if ($insertMessageQuery->affected_rows == 1) {
    // Начинаем сессию
    session_start();
    
    // Устанавливаем данные сообщения пользователя в сессии
    $_SESSION['user_id'] = $mysql->insert_id;
    $_SESSION['user_name'] = $insertName;
    $_SESSION['user_email'] = $insertEmail;

    // Выводим сообщение об успешной регистрации и перенаправляем на страницу меню
    echo "<script>alert('Сообщение было отправлено. Добро Спасибо, $insertName! Вы Поможете сайту стать лучше и будете перенаправлены на страницу меню.'); window.location.href = '/index.html';</script>";
} else {
    echo "Ошибка при регистрации пользователя";
}

// Закрываем соединение с базой данных
$mysql->close();
?>