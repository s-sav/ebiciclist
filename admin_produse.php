<?php


@include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:autentificare.php');
};

if(isset($_POST['add_product'])){

   $name = $_POST['name'];
   $name = filter_var($name, );
   $price = $_POST['price'];
   $price = filter_var($price, );
   $category = $_POST['category'];
   $category = filter_var($category, );
   $details = $_POST['details'];
   $details = filter_var($details, );

   $image = $_FILES['image']['name'];
   $image = filter_var($image, );
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = 'uploaded_img/'.$image;

   $select_products = $conn->prepare("SELECT * FROM `products` WHERE name = ?");
   $select_products->execute([$name]);

   if($select_products->rowCount() > 0){
      $message[] = 'produsul deja există!';
   }else{

      $insert_products = $conn->prepare("INSERT INTO `products`(name, category, details, price, image) VALUES(?,?,?,?,?)");
      $insert_products->execute([$name, $category, $details, $price, $image]);

      if($insert_products){
         if($image_size > 2000000){
            $message[] = 'image size is too large!';
         }else{
            move_uploaded_file($image_tmp_name, $image_folder);
            $message[] = 'produs nou adaugat!';
         }

      }

   }

};

if(isset($_GET['delete'])){

   $delete_id = $_GET['delete'];
   $select_delete_image = $conn->prepare("SELECT image FROM `products` WHERE id = ?");
   $select_delete_image->execute([$delete_id]);
   $fetch_delete_image = $select_delete_image->fetch(PDO::FETCH_ASSOC);
   unlink('uploaded_img/'.$fetch_delete_image['image']);
   $delete_products = $conn->prepare("DELETE FROM `products` WHERE id = ?");
   $delete_products->execute([$delete_id]);
   $delete_wishlist = $conn->prepare("DELETE FROM `wishlist` WHERE pid = ?");
   $delete_wishlist->execute([$delete_id]);
   $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE pid = ?");
   $delete_cart->execute([$delete_id]);
   header('location:admin_produse.php');


}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta cha0et="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>products</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/adminmeniu.css">
</head>
<body>
   
<?php include 'admin_header.php'; ?>

<section class="add-products">

   <h1 class="title">Introduceti produse noi</h1>

   <form action="" method="POST" enctype="multipart/form-data">
      <div class="flex">
         <div class="inputBox">
         <input type="text" name="name" class="box" required placeholder="introduceti nume produs">
         <select name="category" class="box" required>
            <option value="" selected disabled>alege categoria</option>
               <option value="biciclete">Biciclete</option>
               <option value="piese">Piese</option>
               <option value="accesorii">Accesorii</option>
               <option value="echipamente">Echipamente</option>
               <option value="test">test</option>
         </select>
         </div>
         <div class="inputBox">
         <input type="number" min="0" name="price" class="box" required placeholder="pret produs">
         <input type="file" name="image" required class="box" accept="image/jpg, image/jpeg, image/png">
         </div>
      </div>
      <textarea name="details" class="box" required placeholder="detalii produs..." cols="30" rows="10"></textarea>
      <input type="submit" class="btn" value="adaugati produs" name="add_product" >
      
      </style>
   </form>

</section>

<section class="show-products">

   <h1 class="title">Produse Adaugate</h1>

   <div class="box-container">

   <?php
      $show_products = $conn->prepare("SELECT * FROM `products`");
      $show_products->execute();
      if($show_products->rowCount() > 0){
         while($fetch_products = $show_products->fetch(PDO::FETCH_ASSOC)){  
   ?>
   <div class="box">
      <div class="price"><?= $fetch_products['price']; ?>Lei</div>
      <img src="uploaded_img/<?= $fetch_products['image']; ?>" alt="">
      <div class="name"><?= $fetch_products['name']; ?></div>
      <div class="cat"><?= $fetch_products['category']; ?></div>
      <div class="details"><?= $fetch_products['details']; ?></div>
      <div class="flex-btn">
         <a href="admin_modificare_produse.php?update=<?= $fetch_products['id']; ?>" class="update-btn">update</a>
         <a href="admin_produse.php?delete=<?= $fetch_products['id']; ?>" class="delete-btn" onclick="return confirm('Vrei sa stergi acest produs?');">șterge</a>
      </div>
   </div>
   <?php
      }
   }else{
      echo '<p class="empty">produse adaugate cu succes!</p>';
   }
   ?>

   </div>

</section>

<script src="js/script.js"></script>

</body>
</html>