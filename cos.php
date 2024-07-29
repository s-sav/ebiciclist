<?php

@include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:autentificare.php');
};

if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   $delete_cart_item = $conn->prepare("DELETE FROM `cart` WHERE id = ?");
   $delete_cart_item->execute([$delete_id]);
   header('location:cos.php');
}

if(isset($_GET['delete_all'])){
   $delete_cart_item = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
   $delete_cart_item->execute([$user_id]);
   header('location:cos.php');
   exit();
}

if(isset($_POST['update_qty'])){
   $cart_id = $_POST['cart_id'];
   $p_qty = $_POST['p_qty'];
   $p_qty = filter_var($p_qty, );
   $update_qty = $conn->prepare("UPDATE `cart` SET quantity = ? WHERE id = ?");
   $update_qty->execute([$p_qty, $cart_id]);
   $message[] = 'modificare cantitate coș cu succes';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Coș</title>


   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/general.css">

</head>
<body>
   
<?php include 'header.php'; ?>

<section class="shopping-cart">

   <h1 class="title">produse adăugate</h1>

   <div class="box-container">

   <?php
      $grand_total = 0;
      $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
      $select_cart->execute([$user_id]);
      if($select_cart->rowCount() > 0){
         while($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)){ 
   ?>
   <form action="" method="POST" class="box">
      <a href="cos.php?delete=<?= $fetch_cart['id']; ?>" class="fas fa-times" onclick="return confirm('ștergi asta din coș?');"></a>
      <a href="vizualizare_produs.php?pid=<?= $fetch_cart['pid']; ?>" class="fas fa-eye"></a>
      <img src="uploaded_img/<?= $fetch_cart['image']; ?>" alt="">
      <div class="name"><?= $fetch_cart['name']; ?></div>
      <div class="price"><?= $fetch_cart['price']; ?>Lei</div>
      <input type="hidden" name="cart_id" value="<?= $fetch_cart['id']; ?>">
      <input type="number" min="0" value="<?= $fetch_cart['quantity']; ?>" class="qty" name="p_qty" >
      <div class="flex-btn">
         <input type="submit" value="modifică" name="update_qty" class="option-btn">
      </div>
      <div class="sub-total"> Total : <span><?= $sub_total = ($fetch_cart['price'] * $fetch_cart['quantity']); ?>lei</span> </div>
   </form>
   <?php
      $grand_total += $sub_total;
      }
   }else{
      echo '<p class="empty">coșul este gol</p>';
   }
   ?>
   </div>

   <div class="cart-total">
      <p>Total : <span><?= $grand_total; ?></span> Lei</p>
      <a href="produse.php" class="option-btn">continuă cumpărăturile</a>
      <a href="cos.php?delete_all" class="delete-btn <?= ($grand_total > 1)?'':'disabled'; ?>">șterge </a>
      <a href="finalizare_comanda.php" class="btn <?= ($grand_total > 1)?'':'disabled'; ?>">finalizează comanda</a>
   </div>

</section>

<?php include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>