<?php
@include 'config.php';
session_start();

$admin_id = $_SESSION['admin_id'];
if(!isset($admin_id)){
   header('location:autentificare.php');
   exit; 
}

$message = []; 

if(isset($_POST['update_order'])){
   $order_id = $_POST['order_id'];
   $update_payment = $_POST['update_payment'];
   $update_payment = filter_var($update_payment, FILTER_SANITIZE_STRING);

   $update_orders = $conn->prepare("UPDATE `orders` SET payment_status = ? WHERE id = ?");
   $update_orders->execute([$update_payment, $order_id]);
   $message[] = 'Modificare status plată comandă realizată!';
}

if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   $delete_orders = $conn->prepare("DELETE FROM `orders` WHERE id = ?");
   $delete_orders->execute([$delete_id]);
   header('location:admin_comenzi.php');
   exit; 
}

$sql = "SELECT o.*, u.name AS user_name FROM `orders` o LEFT JOIN `users` u ON o.user_id = u.id WHERE 1";

if(isset($_GET['filter_user']) && !empty($_GET['filter_user'])) {
   $filter_user = $_GET['filter_user'];
   $sql .= " AND u.user_type = 'user' AND o.user_id = :user_id";
}

if(isset($_GET['filter_status']) && !empty($_GET['filter_status'])) {
   $filter_status = $_GET['filter_status'];
   $sql .= " AND o.payment_status = :payment_status";
}

$select_orders = $conn->prepare($sql);

if(isset($filter_user)) {
   $select_orders->bindParam(':user_id', $filter_user, PDO::PARAM_INT);
}

if(isset($filter_status)) {
   $select_orders->bindParam(':payment_status', $filter_status, PDO::PARAM_STR);
}

$select_orders->execute();
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Comenzi</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/adminmeniu.css">
</head>
<body>

<?php include 'admin_header.php'; ?>

<section class="placed-orders">
   <h1 class="title">Comenzi plasate</h1>

   <form method="GET" action="">
      <h2 for="filter_user">Filtrare după utilizator:</h2>
      <select name="filter_user" id="filter_user">
         <option value="">Toți utilizatorii</option>
         <?php
            $select_users = $conn->query("SELECT id, name FROM `users` WHERE user_type = 'user'");
            while($user = $select_users->fetch(PDO::FETCH_ASSOC)) {
               $selected = (isset($_GET['filter_user']) && $_GET['filter_user'] == $user['id']) ? 'selected' : '';
               echo '<option value="' . $user['id'] . '" ' . $selected . '>' . htmlspecialchars($user['name']) . '</option>';
            }
         ?>
      </select>

      <h2 for="filter_status">Filtrare după status:</h2>
      <select name="filter_status" id="filter_status">
         <option value="">Toate statusurile</option>
         <option value="in asteptare" <?php if(isset($_GET['filter_status']) && $_GET['filter_status'] == 'in asteptare') echo 'selected'; ?>>În așteptare</option>
         <option value="completata" <?php if(isset($_GET['filter_status']) && $_GET['filter_status'] == 'completata') echo 'selected'; ?>>Completată</option>
      </select>

      <input type="submit" value="Filtrează" class="btn">
   </form>

   <div class="box-container">
      <?php
      if($select_orders->rowCount() > 0){
         while($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)){
      ?>
         <div class="box">
            <p> ID utilizator: <span><?= $fetch_orders['user_name']; ?></span> </p>
            <p> Dată : <span><?= $fetch_orders['placed_on']; ?></span> </p>
            <p> Nume : <span><?= $fetch_orders['name']; ?></span> </p>
            <p> Email : <span><?= $fetch_orders['email']; ?></span> </p>
            <p> Telefon : <span><?= $fetch_orders['number']; ?></span> </p>
            <p> Adresă : <span><?= $fetch_orders['address']; ?></span> </p>
            <p> Total produse : <span><?= $fetch_orders['total_products']; ?></span> </p>
            <p> Total preț : <span><?= $fetch_orders['total_price']; ?> Lei</span> </p>
            <p> Metodă plată : <span><?= $fetch_orders['method']; ?></span> </p>
            <form action="" method="POST">
               <input type="hidden" name="order_id" value="<?= $fetch_orders['id']; ?>">
               <select name="update_payment" class="drop-down">
                  <option value="" selected disabled><?= $fetch_orders['payment_status']; ?></option>
                  <option value="in asteptare">În așteptare</option>
                  <option value="completata">Completată</option>
               </select>
               <div class="flex-btn">
                  <input type="submit" name="update_order" class="update-btn" value="Modifică">
                  <a href="admin_comenzi.php?delete=<?= $fetch_orders['id']; ?>" class="delete-btn" onclick="return confirm('Șterge această comandă?');">Șterge</a>
               </div>
            </form>
         </div>
      <?php
         }
      } else {
         echo '<p class="empty">Nu există nicio comandă!</p>';
      }
      ?>
   </div>

</section>

<script src="js/script.js"></script>

</body>
</html>
