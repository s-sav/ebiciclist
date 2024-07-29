<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');


require 'config.php'; 

  // vanzari totale
$total_sales_query = $conn->prepare("SELECT SUM(total_price) AS total_sales FROM orders");
$total_sales_query->execute();
$total_sales_fetch = $total_sales_query->fetch(PDO::FETCH_ASSOC);
$total_sales = $total_sales_fetch['total_sales'];

  // vanzari in functie de metoda de plata
$sales_by_method_query = $conn->prepare("SELECT method, SUM(total_price) AS total_sales FROM orders GROUP BY method");
$sales_by_method_query->execute();

  // vanzari in functie de categoria de produse
$sales_by_category_query = $conn->prepare("SELECT p.category, SUM(o.total_price) AS total_sales FROM orders o INNER JOIN products p ON o.total_products LIKE CONCAT('%', p.name, '%') GROUP BY p.category");
$sales_by_category_query->execute();

  // vanzari in functie de utilizator
$sales_by_user_query = $conn->prepare("SELECT name, SUM(total_price) AS total_sales FROM orders GROUP BY name");
$sales_by_user_query->execute();

?>




<!DOCTYPE html>
<html lang="en">
<?php include 'admin_header.php'; ?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raport vanzari</title>
    <link rel="stylesheet" href="css/adminmeniu.css">
    
</head>

<body>
<div class="container">
    <h1>Raport vanzari</h1>

    <div class="box-container">

        <div class="box">
            <h2>Total Vanzari luna curentă:</h2>
            <h3><?= $total_sales; ?> Lei</h3>
        </div>

        <div class="box">
            <h2>Vanzari in functie de metoda de plată luna curentă:</h2>
            <?php
            while ($method_row = $sales_by_method_query->fetch(PDO::FETCH_ASSOC)) {
                echo '<h3>' . $method_row['method'] . ': ' . $method_row['total_sales'] . ' Lei</h3>';
            }
            ?>
        </div>

        <div class="box">
            <h2>Vanzari pe categorii:</h2>
            <?php
            while ($category_row = $sales_by_category_query->fetch(PDO::FETCH_ASSOC)) {
                echo '<h3>' . $category_row['category'] . ': ' . $category_row['total_sales'] . ' Lei</h3>';
            }
            ?>
        </div>

        <div class="box">
            <h2>Vanzari in functie de utilizatori:</h2>
            <?php
            while ($user_row = $sales_by_user_query->fetch(PDO::FETCH_ASSOC)) {
                echo '<h3>' . $user_row['name'] . ': ' . $user_row['total_sales'] . ' Lei</h3>';
            }
            ?>
        </div>
    </div>

</div>

</body>

</html>
