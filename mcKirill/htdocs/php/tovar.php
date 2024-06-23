<?php

    // Получение данных из формы (личные данные)
    $name = htmlspecialchars($_POST['name']);
    $phone = htmlspecialchars($_POST['phone']);
    $email = htmlspecialchars($_POST['email']);
    $adres = htmlspecialchars($_POST['adres']);
    $city = htmlspecialchars($_POST['city']);
    $payment_method = htmlspecialchars($_POST['payment-method']);
    $items = json_decode($_POST['cart-items'], true); // Декодирование данных о товарах из JSON

    // Подключение к базе данных
    $servername = "localhost";
    $username = "root";
    $password = "root";
    $dbname = "zakaz_db";

    // Создание подключения
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Проверка подключения
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // SQL запрос для вставки данных заказа в таблицу orders
    $sql_order = "INSERT INTO zakaz (`name`, `phone`, `email`, `adres`, `city`, `payment_method`) 
                  VALUES ('$name', '$phone', '$email', '$adres', '$city', '$payment_method')";

    if ($conn->query($sql_order) === TRUE) {
        // Получение ID нового заказа
        $order_id = $conn->insert_id;

        // Закрытие подключения
        $conn->close();


}