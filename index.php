<?php
session_start();

$db = new mysqli('192.168.199.13', 'learn', 'learn', 'learn_vershininmp-is64') or die('Ошибка подключения: ' . $db->connect_error);


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
    $name = $_POST['name'];
    $category = $_POST['category'];
    $price = $_POST['price'];

    if (empty($name) || empty($price)) {
        die('Все поля должны быть заполнены!');
    }

    if ($db->query("INSERT INTO products (name, category, price) VALUES ('$name', '$category', '$price')")) {
        echo 'Товар успешно добавлен!';
    } else {
        echo 'Ошибка: ' . $db->error;
    }
}


$products = [];
$result = $db->query("SELECT * FROM products");

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
} else {
    echo 'Ошибка: ' . $db->error;
}


$searchResults = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sort_by_info'])) {

    
    $sortByCategory = isset($_POST['sort_by_element']) ? $_POST['sort_by_element'] : '';
    $sortByPrice = isset($_POST['sort_by_price']) ? $_POST['sort_by_price'] : '';


    if (empty($sortByCategory or $sortByPrice)) {
        echo 'Пожалуйста, введите информацию для поиска.';
    } else {
        $query = "SELECT * FROM products WHERE 1=1"; 

        if (!empty($sortByCategory)) {
            $query .= " AND category = '$sortByCategory'";
        }

        if (!empty($sortByPrice)) {
            $query .= " AND price <= $sortByPrice";
        }

        $result = $db->query($query);

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $searchResults[] = $row;
            }
        } else {
            echo 'Ошибка: ' . $db->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./asset/css/style.css">
    <title>Сортировка товара</title>
</head>

<body>
    <div class="create">
        <form method="post">
            <input type="text" name="name" placeholder="Название товара" required>
            <select name="category">
                <option value="" disabled selected hidden required>Выберите категорию</option>
                <option value="electronics">Электроника</option>
                <option value="clothing">Одежда</option>
                <option value="furniture">Мебель</option>
            </select>
            <input type="number" name="price" placeholder="Укажите цену товара" required>
            <input type="submit" value="Создать товар">
        </form>
    </div>

    <div class="sort">
        <form method="post">
            <select name="sort_by_element">
                <option value="" disabled selected hidden>Что будем искать?</option>
                <option value="electronics">Электроника</option>
                <option value="clothing">Одежда</option>
                <option value="furniture">Мебель</option>
            </select>
            <input type="number" name="sort_by_price" placeholder="Укажите максимальную сумму">
            <input type="submit" name="sort_by_info" value="Применить">
        </form>
    </div>


    <div class="cards">
        <div class="all_products">
            <div class="inname">
                <h2>Все товары</h2>
            </div>
            <div class="prod">
                <?php
                if (empty($products)) {
                    echo 'Нет товаров';
                } else {
                    foreach ($products as $product) {
                        echo '<div class="product">';
                        echo '<h3>' . htmlspecialchars($product['name']) . '</h3>';
                        echo '<p>Категория: ' . htmlspecialchars($product['category']) . '</p>';
                        echo '<p>Цена: ' . htmlspecialchars($product['price']) . '</p>';
                        echo '</div>';
                    }
                }
                ?>
            </div>
        </div>

        <div class="search">
            <div class="inname">
                <h2>Товары по поиску</h2>
            </div>

            <div class="prod">
                <?php
                if (empty($searchResults)) {
                    echo 'Нет товаров';
                } else {
                    foreach ($searchResults as $product) {
                        echo '<div class="product">';
                        echo '<h3>' . htmlspecialchars($product['name']) . '</h3>';
                        echo '<p>Категория: ' . htmlspecialchars($product['category']) . '</p>';
                        echo '<p>Цена: ' . htmlspecialchars($product['price']) . '</p>';
                        echo '</div>';
                    }
                }
                ?>
            </div>
        </div>

</body>

</html>