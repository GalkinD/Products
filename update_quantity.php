<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаем данные из AJAX-запроса
    $productId = $_POST['productId'] ?? null;
    $newQuantity = $_POST['newQuantity'] ?? null;

    if ($productId && is_numeric($newQuantity)) {
        // Настройки подключения к базе данных
        $host = 'localhost';
        $dbname = 'test_database';
        $username = 'root';
        $password = '54555455';

        try {
            // Подключение к базе данных
            $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Обновляем количество товара
            $sql = "UPDATE Products SET PRODUCT_QUANTITY = :quantity WHERE ID = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':quantity', $newQuantity, PDO::PARAM_INT);
            $stmt->bindParam(':id', $productId, PDO::PARAM_INT);
            $stmt->execute();

            // Отправляем успешный ответ
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            // Ошибка базы данных
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    } else {
        // Некорректные данные
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid input data']);
    }
} else {
    // Метод запроса не POST
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
}
?>
