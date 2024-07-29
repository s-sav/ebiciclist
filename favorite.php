<?php

@include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:autentificare.php');
};

if(isset($_POST['add_to_cart'])){

   $pid = $_POST['pid'];
   $pid = filter_var($pid, );
   $p_name = $_POST['p_name'];
   $p_name = filter_var($p_name, );
   $p_price = $_POST['p_price'];
   $p_price = filter_var($p_price, );
   $p_image = $_POST['p_image'];
   $p_image = filter_var($p_image, );
   $p_qty = $_POST['p_qty'];
   $p_qty = filter_var($p_qty, );

   $check_cart_numbers = $conn->prepare("SELECT * FROM `cart` WHERE name = ? AND user_id = ?");
   $check_cart_numbers->execute([$p_name, $user_id]);

   if($check_cart_numbers->rowCount() > 0){
      $message[] = 'produs deja adăugat în coș!';
   }else{

      $check_wishlist_numbers = $conn->prepare("SELECT * FROM `wishlist` WHERE name = ? AND user_id = ?");
      $check_wishlist_numbers->execute([$p_name, $user_id]);

      if($check_wishlist_numbers->rowCount() > 0){
         $delete_wishlist = $conn->prepare("DELETE FROM `wishlist` WHERE name = ? AND user_id = ?");
         $delete_wishlist->execute([$p_name, $user_id]);
      }

      $insert_cart = $conn->prepare("INSERT INTO `cart`(user_id, pid, name, price, quantity, image) VALUES(?,?,?,?,?,?)");
      $insert_cart->execute([$user_id, $pid, $p_name, $p_price, $p_qty, $p_image]);
      $message[] = 'adaugat în coș!';
   }

}

if(isset($_GET['delete'])){

   $delete_id = $_GET['delete'];
   $delete_wishlist_item = $conn->prepare("DELETE FROM `wishlist` WHERE id = ?");
   $delete_wishlist_item->execute([$delete_id]);
   header('location:favorite.php');

}

if(isset($_GET['delete_all'])){

   $delete_wishlist_item = $conn->prepare("DELETE FROM `wishlist` WHERE user_id = ?");
   $delete_wishlist_item->execute([$user_id]);
   header('location:favorite.php');

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Favorite</title>


   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/general.css">

</head>
<body>
   
<?php include 'header.php'; ?>

<section class="wishlist">

   <h1 class="title">Produse Favorite</h1>

   <div class="box-container">

   <?php
      $grand_total = 0;
      $select_wishlist = $conn->prepare("SELECT * FROM `wishlist` WHERE user_id = ?");
      $select_wishlist->execute([$user_id]);
      if($select_wishlist->rowCount() > 0){
         while($fetch_wishlist = $select_wishlist->fetch(PDO::FETCH_ASSOC)){ 
   ?>
   <form action="" method="POST" class="box">
      <a href="favorite.php?delete=<?= $fetch_wishlist['id']; ?>" class="fas fa-times" onclick="return confirm('Ștergi din favorite?');"></a>
      <a href="vizualizare_produs.php?pid=<?= $fetch_wishlist['pid']; ?>" class="fas fa-eye"></a>
      <img src="uploaded_img/<?= $fetch_wishlist['image']; ?>" alt="">
      <div class="name"><?= $fetch_wishlist['name']; ?></div>
      <div class="price"><?= $fetch_wishlist['price']; ?> Lei</div>
      <input type="hidden" name="pid" value="<?= $fetch_wishlist['pid']; ?>">
      <input type="hidden" name="p_name" value="<?= $fetch_wishlist['name']; ?>">
      <input type="hidden" name="p_price" value="<?= $fetch_wishlist['price']; ?>">
      <input type="hidden" name="p_image" value="<?= $fetch_wishlist['image']; ?>">
      <div class="latest-btn">
      <button type="submit" class="option-buttn" name="add_to_cart"><i class="fas fa-cart-shopping fa-bounce" style=" --fa-bounce-start-scale-x: 1; --fa-bounce-start-scale-y: 1; --fa-bounce-jump-scale-x: 1; --fa-bounce-jump-scale-y: 1; --fa-bounce-land-scale-x: 1; --fa-bounce-land-scale-y: 1; " ></i></button>
      <input type="number" min="0" value="1" name="p_qty" class="qty" >
      </div>
   </form>
   <?php
      $grand_total += $fetch_wishlist['price'];
      }
   }else{
      echo '<p class="empty">listă favorite goală</p>';
   }
   ?>
   </div>

   <div class="wishlist-total">
      <p>Total: <span><?= $grand_total; ?></span> Lei</p>
      <a href="produse.php" class="option-btn">continuă cumparăturile</a>
      <a href="favorite.php?delete_all" class="delete-btn <?= ($grand_total > 1)?'':'disabled'; ?>">șterge tot</a>
   </div>

</section>
<?php include 'footer.php'; ?>
<script src="js/script.js"></script>

</body>
</html>