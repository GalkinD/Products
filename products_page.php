<?php
// Функция для подключения к базе данных и получения данных
function getProductsSortedByDate() {
    // Настройки подключения к базе данных
    $host = 'localhost';
    $dbname = 'test_database';
    $username = 'root';
    $password = '54555455';

    try {
        // Создаем подключение к базе данных
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // SQL-запрос: выбрать только не скрытые товары, отсортированные по дате создания
        $sql = "SELECT * FROM Products WHERE IS_HIDDEN = 0 ORDER BY DATE_CREATE DESC";

        // Выполнение запроса
        $stmt = $pdo->query($sql);

        // Получаем данные в виде массива
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        die("Ошибка подключения или выполнения запроса: " . $e->getMessage());
    }
}

// Получаем данные
$products = getProductsSortedByDate();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Товары</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .quantity-controls {
            display: flex;
            gap: 5px;
            align-items: center;
        }
        .quantity-controls button {
            padding: 5px 10px;
            cursor: pointer;
        }
    </style>
    <script>
        // Функция для изменения количества товаров
        function updateQuantity(productId, action) {
            const quantityElement = document.getElementById(`quantity-${productId}`);
            let currentQuantity = parseInt(quantityElement.textContent);

            // Определяем новое количество
            const newQuantity = action === 'increase' ? currentQuantity + 1 : Math.max(0, currentQuantity - 1);

            // Отправляем AJAX-запрос для сохранения изменений
            fetch('update_quantity.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `productId=${encodeURIComponent(productId)}&newQuantity=${encodeURIComponent(newQuantity)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Обновляем количество в таблице
                    quantityElement.textContent = newQuantity;
                } else {
                    alert('Ошибка обновления количества: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Ошибка при выполнении запроса:', error);
            });
        }
    </script>
</head>
<body>
    <h1>Актуальные товары</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Название</th>
                <th>Цена</th>
                <th>Артикул</th>
                <th>Количество</th>
                <th>Дата создания</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?= htmlspecialchars($product['ID']) ?></td>
                        <td><?= htmlspecialchars($product['PRODUCT_NAME']) ?></td>
                        <td><?= htmlspecialchars($product['PRODUCT_PRICE']) ?></td>
                        <td><?= htmlspecialchars($product['PRODUCT_ARTICLE']) ?></td>
                        <td>
                            <div class="quantity-controls">
                                <button onclick="updateQuantity(<?= htmlspecialchars($product['ID']) ?>, 'decrease')">-</button>
                                <span id="quantity-<?= htmlspecialchars($product['ID']) ?>"><?= htmlspecialchars($product['PRODUCT_QUANTITY']) ?></span>
                                <button onclick="updateQuantity(<?= htmlspecialchars($product['ID']) ?>, 'increase')">+</button>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($product['DATE_CREATE']) ?></td>
                        <td>
                            <button class="hide-button" onclick="hideProduct(this, <?= htmlspecialchars($product['ID']) ?>)">Скрыть</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7">Нет товаров для отображения</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
