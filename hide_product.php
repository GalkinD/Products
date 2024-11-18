<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаем ID товара из AJAX-запроса
    $productId = $_POST['productId'] ?? null;

    if ($productId) {
        // Настройки базы данных
        $host = 'localhost';
        $dbname = 'test_database';
        $username = 'root';
        $password = '54555455';

        try {
            // Подключение к базе данных
            $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Обновляем статус товара
            $sql = "UPDATE Products SET IS_HIDDEN = 1 WHERE ID = :id";
            $stmt = $pdo->prepare($sql);
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
        echo json_encode(['success' => false, 'error' => 'Invalid product ID']);
    }
} else {
    // Метод запроса не POST
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
}
?>
