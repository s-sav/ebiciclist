<?php
require 'config.php'; 

$message = '';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $stmt = $conn->prepare("SELECT user_id, expiry FROM password_resets WHERE token = ? AND expiry > NOW()");
    $stmt->execute([$token]);
    $reset = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$reset) {
        $message = 'Token invalid sau expirat.';
    }
} else {
    $message = 'Nu exista token';
}

if (isset($_POST['resetPassword']) && isset($_POST['token']) && isset($_POST['newPassword'])) {
    $token = $_POST['token'];
    $newPassword = $_POST['newPassword'];

    $stmt = $conn->prepare("SELECT user_id FROM password_resets WHERE token = ? AND expiry > NOW()");
    $stmt->execute([$token]);
    $reset = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($reset) {
        $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        if ($stmt->execute([$newPasswordHash, $reset['user_id']])) {
            $message = 'Parola a fost resetată.';
        } else {
            $message = 'A apărut o eroare în timpul actualizării parolei. Vă rugăm să încercați din nou.';
        }
    } else {
        $message = 'Acest token de resetare este invalid sau a expirat.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Resetare Parolă</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="css/componente.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <?php if ($message): ?>
        <div class="message">
            <span><?php echo $message; ?></span>
            <i class="fas fa-times" onclick="closeMessage()"></i>
        </div>
    <?php endif; ?>

    <script>
    function closeMessage() {
        window.location.href = "autentificare.php";
    }
    </script>

    <section class="form-container">
        <form action="resetare_parola.php" method="post">
            <h3>Resetare Parolă</h3>
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            <input type="password" id="newPassword" name="newPassword" class="box" placeholder="Parolă Nouă" required>
            <input type="submit" name="resetPassword" value="Resetare Parolă" class="btn">
        </form>
    </section>
</body>
</html>
