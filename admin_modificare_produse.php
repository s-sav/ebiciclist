<?php

@include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:autentificare.php');
};

if(isset($_POST['update_product'])){

   $pid = $_POST['pid'];
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
   $old_image = $_POST['old_image'];

   $update_product = $conn->prepare("UPDATE `products` SET name = ?, category = ?, details = ?, price = ? WHERE id = ?");
   $update_product->execute([$name, $category, $details, $price, $pid]);

   $message[] = 'update produs cu succes!';

   if(!empty($image)){
      if($image_size > 2000000){
         $message[] = 'imagine prea mare!';
      }else{

         $update_image = $conn->prepare("UPDATE `products` SET image = ? WHERE id = ?");
         $update_image->execute([$image, $pid]);

         if($update_image){
            move_uploaded_file($image_tmp_name, $image_folder);
            unlink('uploaded_img/'.$old_image);
            $message[] = 'update imagine cu succes!';
         }
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
   <title>update products</title>


   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/adminmeniu.css">

</head>
<body>
   
<?php include 'admin_header.php'; ?>

<section class="update-product">

   <h1 class="title">update product</h1>   

   <?php
      $update_id = $_GET['update'];
      $select_products = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
      $select_products->execute([$update_id]);
      if($select_products->rowCount() > 0){
         while($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)){ 
   ?>
   <form action="" method="post" enctype="multipart/form-data">
      <input type="hidden" name="old_image" value="<?= $fetch_products['image']; ?>">
      <input type="hidden" name="pid" value="<?= $fetch_products['id']; ?>">
      <img src="uploaded_img/<?= $fetch_products['image']; ?>" alt="">
      <input type="text" name="name" placeholder="Enter product name" required class="box" value="<?= $fetch_products['name']; ?>">
      <input type="number" name="price" min="0" placeholder="enter product price" required class="box" value="<?= $fetch_products['price']; ?>">
      <select name="category" class="box" required>
         <option selected><?= $fetch_products['category']; ?></option>
         <option value="biciclete">biciclete</option>
         <option value="piese">piese</option>
         <option value="accesorii">accesorii</option>
         <option value="echipamente">echipamente</option>
      </select>
      <textarea name="details" required placeholder="Detalii produs" class="box" cols="30" rows="10"><?= $fetch_products['details']; ?></textarea>
      <input type="file" name="image" class="box" accept="image/jpg, image/jpeg, image/png">
      <div class="flex-btn">
         <input type="submit" class="btn" value="update produs" name="update_product">
         <a href="admin_produse.php" class="option-btn">înapoi</a>
      </div>
   </form>
   <?php
         }
      }else{
         echo '<p class="empty">Nu au fost gasite produse!</p>';
      }
   ?>

</section>
<script src="js/script.js"></script>

</body>
</html>