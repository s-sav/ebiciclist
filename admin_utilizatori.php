<?php

@include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:autentificare.php');
};

if(isset($_GET['delete'])){

   $delete_id = $_GET['delete'];
   $delete_users = $conn->prepare("DELETE FROM `users` WHERE id = ?");
   $delete_users->execute([$delete_id]);
   header('location:admin_utilizatori.php');

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>utilizatori</title>


   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/adminmeniu.css">

</head>
<body>
   
<?php include 'admin_header.php'; ?>

<section class="user-accounts">

   <h1 class="title">conturi utilizatori</h1>

   <div class="box-container">

   <?php
$select_users = $conn->prepare("SELECT * FROM `users`");
$select_users->execute();
if($select_users->rowCount() > 0){
   while($fetch_users = $select_users->fetch(PDO::FETCH_ASSOC)){
      if ($fetch_users['user_type'] !== 'admin') { 
?>
<div class="box" style="<?php if ($fetch_users['id'] == $admin_id) { echo 'display:none'; } ?>">
   <p> ID utilizator: <span><?= $fetch_users['id']; ?></span></p>
   <p> Username: <span><?= $fetch_users['name']; ?></span></p>
   <p> Email: <span><?= $fetch_users['email']; ?></span></p>
   <p> Tip utilizator: <span style="color:<?php if ($fetch_users['user_type'] == 'admin') { echo 'orange'; } ?>"><?= $fetch_users['user_type']; ?></span></p>
   <?php if ($fetch_users['user_type'] != 'admin') { ?>
   <a href="admin_utilizatori.php?delete=<?= $fetch_users['id']; ?>" onclick="return confirm('Dorești să ștergi acest utilizator?');" class="delete-btn">Șterge</a>
   <?php } ?>
</div>
<?php
      }
   }
} else {
   echo '<p class="empty">Nu există niciun utilizator!</p>';
}
?>
   </div>

</section>

<script src="js/script.js"></script>

</body>
</html>