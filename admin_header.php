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

?>

<header class="header">
   <div class="flex">
      <a href="admin_meniu.php" class="logo"><img src="images/logo.png" alt="" style="height: 100px; width: auto;"></a>
      <nav class="navbar">
         <a href="admin_meniu.php">Acasă</a>
         <a href="admin_produse.php">Produse</a>
         <a href="admin_comenzi.php">Comenzi</a>
         <a href="admin_utilizatori.php">Utilizatori</a>
         <a href="admin_mesaje.php">Mesaje</a>
         <a href="admin_review-uri.php">Review-uri</a>
      </nav>
      <div class="icons">
         <div id="menu-btn" class="fas fa-bars"></div>
         <div id="user-btn" class="fas fa-user"></div>
      </div>
      <div class="profile">
         <?php
         if(isset($_SESSION['admin_id'])){
            $admin_id = $_SESSION['admin_id'];
            $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
            $select_profile->execute([$admin_id]);
            $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
            ?>
            <p>Bine ai venit <br><?= $fetch_profile['name']; ?></p>
            <a href="admin_modificare_profil.php" class="btn">modificare profil</a>
            <a href="deconectare.php" class="delete-btn">deconectare</a>
         <?php } else { ?>
            <div class="flex-btn">
               <a href="autentificare.php" class="option-btn">autentificare</a>
               <a href="inregistrare.php" class="option-btn">înregistrare</a>
            </div>
         <?php } ?>
      </div>
   </div>
</header>
