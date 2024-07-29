<?php
if(isset($message)){
   foreach($message as $message){
      echo '
      <div class="message">
         <span>'.$message.'</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
}
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';
?>

<header class="header">

   <div class="flex">

      <a href="index.php" class="logo"> <img src="images/logo.png" alt="" style="height: 100px; width: 100px;"></a>
      <nav class="navbar" style="align-items: flex-start;">
         <a href="index.php" class="nav-link">Acasă</a>
         <a href="produse.php" class="nav-link">Produse</a>
         <a href="metodeplata.php" class="nav-link">Metode plată</a>
         <a href="comenzi.php" class="nav-link">Comenzi</a> 
         <a href="contact.php" class="nav-link">Contact</a>
         
      </nav>
      <div class="icons">
         <div id="menu-btn" class="fas fa-bars"></div>
         <a href="pagina_cautare.php" class="fas fa-search"></a>
         <div id="user-btn" class="fas fa-user"></div> 
         <?php
            $count_cart_items = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
            $count_cart_items->execute([$user_id]);
            $count_wishlist_items = $conn->prepare("SELECT * FROM `wishlist` WHERE user_id = ?");
            $count_wishlist_items->execute([$user_id]);
         ?>
         <a href="favorite.php"><i class="fas fa-heart"></i><span>(<?= $count_wishlist_items->rowCount(); ?>)</span></a>
         <a href="cos.php"><i class="fas fa-shopping-cart"></i><span>(<?= $count_cart_items->rowCount(); ?>)</span></a>
      </div>
      <div class="profile">
         <?php
            $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
            $select_profile->execute([$user_id]);
            $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
         ?>
         <p>Bine ai venit <br><?= $fetch_profile['name']; ?></p>
         <?php if(isset($_SESSION['user_id'])): ?>
            <a href="comenzi.php" class="update-btn">Comenzi</a>
            <a href="modificare_profil_utilizator.php" class="update-btn">Modificare profil</a>
            <a href="deconectare.php" class="delete-btn">Deconectare</a>
         <?php else:  ?>
         <div class="flex-btn">
            <a href="autentificare.php" class="option-btn">Autentificare</a>
            <a href="inregistrare.php" class="option-btn">Înregistrare</a>
      </div>
      <?php endif; ?>
</div>
   </div>
</header>