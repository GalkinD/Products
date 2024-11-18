<?php
class CProducts {
    private $host;
    private $dbname;
    private $username;
    private $password;
    private $pdo;

    // Конструктор для инициализации подключения к базе данных
    public function __construct($host = 'localhost', $dbname = 'test_database', $username = 'root', $password = '54555455') {
        $this->host = $host;
        $this->dbname = $dbname;
        $this->username = $username;
        $this->password = $password;

        // Подключение к базе данных
        $this->connectToDatabase();
    }

    // Метод для подключения к базе данных
    private function connectToDatabase() {
        try {
            $this->pdo = new PDO("mysql:host={$this->host};dbname={$this->dbname};charset=utf8", $this->username, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Ошибка подключения: " . $e->getMessage());
        }
    }

    // Метод для получения массива товаров с ограничением на количество
    public function getProducts($limit = 10) {
        try {
            // Подготовленный SQL-запрос
            $sql = "SELECT * FROM Products ORDER BY DATE_CREATE DESC LIMIT :limit";
            $stmt = $this->pdo->prepare($sql);

            // Привязываем параметр $limit
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);

            // Выполняем запрос
            $stmt->execute();

            // Возвращаем результат в виде массива
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Ошибка выполнения запроса: " . $e->getMessage());
        }
    }
}

// Использование класса
$cproducts = new CProducts();
$products = $cproducts->getProducts(6); // Получаем максимум 5 товаров

// Выводим результат для теста
echo "<pre>";
print_r($products);
echo "</pre>";
?>
