<?php

require 'config.php'; 

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Raport Produse</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/adminmeniu.css">
</head>
<body>
   
<?php include 'admin_header.php'; ?>

<section class="filter-container">
   <form action="" method="GET">
      <div class="flex">
         <div class="inputBox"> 
            <select name="category" class="box">
               <option value="">Toate categoriile</option>
               <option value="biciclete">Biciclete</option>
               <option value="piese">Piese</option>
               <option value="accesorii">Accesorii</option>
               <option value="echipamente">Echipamente</option>
            </select>
         </div>
      </div>
      <input type="submit" class="btn" value="Filtrează">
   </form>
</section>

<section class="show-products">

   <h1 class="title">Raport Produse</h1>

   <?php
      $query = "SELECT * FROM `products` WHERE 1";
      $params = [];

      if (!empty($_GET['name'])) {
         $query .= " AND name LIKE ?";
         $params[] = "%" . $_GET['name'] . "%";
      }
      if (!empty($_GET['category'])) {
         $query .= " AND category = ?";
         $params[] = $_GET['category'];
      }

      $stmt = $conn->prepare($query);
      $stmt->execute($params);

      if($stmt->rowCount() > 0){
   ?>
   <div class="box">
      <ul>
      <?php
         while($fetch_products = $stmt->fetch(PDO::FETCH_ASSOC)){  
      ?>
         <li>
            <h2><?= $fetch_products['name']; ?></h2>
            <h3><strong>Preț:</strong> <?= $fetch_products['price']; ?> Lei</h3>
            <h3><strong>Categorie:</strong> <?= $fetch_products['category']; ?></h3>
            <h3><strong>Detalii:</strong> <?= $fetch_products['details']; ?></h3>
         </li>
      <?php
         }
      ?>
      </ul>
   </div>
   <?php
      }else{
         echo '<p class="empty">Nu au fost găsite produse!</p>';
      }
   ?>

</section>

<script src="js/script.js"></script>

</body>
</html>
