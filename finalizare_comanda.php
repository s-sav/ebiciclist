<?php

require 'vendor/autoload.php';
require_once __DIR__ . '/vendor/stripe/stripe-php/init.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

@include 'config.php';

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:autentificare.php');
    exit;
}

if (isset($_POST['order'])) {
    // Retrieve and sanitize input
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $number = filter_var($_POST['number'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $method = filter_var($_POST['method'], FILTER_SANITIZE_STRING);
    $address = filter_var($_POST['address'] . ' ' . $_POST['city'] . ' ' . $_POST['state'] . ' ' . $_POST['country'] . ' - ' . $_POST['pin_code'], FILTER_SANITIZE_STRING);
    $placed_on = date('d-M-Y');

    $isCardPayment = ($method === 'card');

    $cart_total = 0;
    $cart_products = [];

    // Fetch cart items
    $cart_query = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
    $cart_query->execute([$user_id]);
    if ($cart_query->rowCount() > 0) {
        while ($cart_item = $cart_query->fetch(PDO::FETCH_ASSOC)) {
            $cart_products[] = [
                'name' => htmlspecialchars($cart_item['name']),
                'quantity' => $cart_item['quantity'],
                'price' => $cart_item['price']
            ];
            $cart_total += ($cart_item['price'] * $cart_item['quantity']);
        }
    }

    $total_products = '';
    foreach ($cart_products as $item) {
        $total_products .= "{$item['name']} - {$item['quantity']} ; ";
    }
    $total_products = rtrim($total_products, ' ; ');

    // introducere comanda in baza de date
    $order_query = $conn->prepare("INSERT INTO `orders` (user_id, name, number, email, method, address, total_products, total_price, placed_on) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $order_query->execute([$user_id, $name, $number, $email, $method, $address, $total_products, $cart_total, $placed_on]);

    // generare pdf
    require_once('vendor/fpdf/fpdf/src/Fpdf/Fpdf.php');
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetAutoPageBreak(true, 10);

    $subtotal = 0;
    $vatRate = 19;

    // Logo
    $logoPath = 'images/logo.png';
    $pdf->Image($logoPath, 10, 10, 20);
    $nextSectionY = $pdf->GetY() + 20;

    // titlu factura
    $pdf->SetY($nextSectionY);
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'FACTURA', 0, 1, 'C');

    // detalii factura
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 6, 'Detalii factura', 0, 1, 'C');
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 6, 'Nr Factura: 02912', 0, 1, 'C');
    $pdf->Cell(0, 6, 'Data Factura: ' . date('Y-m-d'), 0, 1, 'C');

    // detelii firma
    $pdf->SetY(70);
    $pdf->SetX(10);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(90, 6, 'Furnizor:', 0, 1);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(90, 6, 'eBiciclist', 0, 1);
    $pdf->Cell(90, 6, 'Soseaua Stefan Cel Mare 9 Bucuresti 021327', 0, 1);
    $pdf->Cell(90, 6, 'J41/5841/20.03.2020', 0, 1);
    $pdf->Cell(90, 6, 'C.I.F.:RO563754310', 0, 1);
    $pdf->Cell(90, 6, 'Telefon: 0760111101', 0, 1);

    // detalii client
    $pdf->SetY(70);
    $pdf->SetX(120);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 6, 'Client:', 0, 1);
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetX(120);
    $pdf->Cell(0, 6, $name, 0, 1);
    $pdf->SetX(120);
    $pdf->Cell(0, 6, $address, 0, 1);

    // tabel factura
    $pdf->SetY(110);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetFillColor(224, 235, 255);
    $pdf->Cell(60, 7, 'Denumire', 1, 0, 'C', true);
    $pdf->Cell(25, 7, 'Cant.', 1, 0, 'C', true);
    $pdf->Cell(30, 7, 'Pret unitar', 1, 0, 'C', true);
    $pdf->Cell(35, 7, 'Valoare', 1, 0, 'C', true);
    $pdf->Cell(40, 7, 'Valoare TVA', 1, 1, 'C', true);

    $totalVAT = 0;
    $subtotal = 0;

    foreach ($cart_products as $item) {
        if (isset($item['name'], $item['quantity'], $item['price'])) {
            $priceWithVAT = $item['price'];
            $priceWithoutVAT = $priceWithVAT / (1 + ($vatRate / 100));
            $lineTotalWithoutVAT = $item['quantity'] * $priceWithoutVAT;
            $lineTotalWithVAT = $item['quantity'] * $priceWithVAT;
            $vatAmount = $lineTotalWithVAT - $lineTotalWithoutVAT;

            $subtotal += $lineTotalWithoutVAT;
            $totalVAT += $vatAmount;

            $pdf->Cell(60, 7, $item['name'], 1, 0, 'L');
            $pdf->Cell(25, 7, $item['quantity'], 1, 0, 'C');
            $pdf->Cell(30, 7, number_format($priceWithoutVAT, 2) . ' Lei', 1, 0, 'R');
            $pdf->Cell(35, 7, number_format($lineTotalWithoutVAT, 2) . ' Lei', 1, 0, 'R');
            $pdf->Cell(40, 7, number_format($vatAmount, 2) . ' Lei', 1, 1, 'R');
        }
    }

    // Total
    $totalWithVAT = $subtotal + $totalVAT;
    $pdf->Ln(5);
    $pdf->SetFillColor(220, 220, 220);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetX(95);
    $pdf->Cell(30, 7, 'Total', 1, 0, 'R', true);
    $pdf->Cell(35, 7, number_format($subtotal, 2) . ' Lei', 1, 0, 'R', true);
    $pdf->Cell(40, 7, number_format($totalVAT, 2) . ' Lei', 1, 1, 'R', true);
    $pdf->SetX(95);
    $pdf->Cell(30, 7, 'Total plata', 1, 0, 'R', true);
    $pdf->Cell(75, 7, number_format($totalWithVAT, 2) . ' Lei', 1, 1, 'C', true);

    // Footer
    $pdf->Ln(50);
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(0, 10, 'Factura este valabila fara semnatura si stampila, conform art. 319 alin. 29 din legea 227/2015.', 0, 1, 'C');

    // salvare PDF
    $pdfFilePath = 'factura.pdf';
    $pdf->Output('F', $pdfFilePath);

    // trimitere email
    $mail = new PHPMailer(true);
    try {
        // Email config
        $mail->isSMTP();
        $mail->Host = $_ENV['SMTP_HOST'];
        $mail->SMTPAuth = true;
        $mail->Username = $_ENV['SMTP_USERNAME'];
        $mail->Password = $_ENV['SMTP_PASSWORD'];
        $mail->SMTPSecure = $_ENV['SMTP_SECURE'];
        $mail->Port = $_ENV['SMTP_PORT'];

        $mail->setFrom($_ENV['SMTP_USERNAME'], 'Admin');
        $mail->addAddress($email, $name);
        $mail->addAttachment($pdfFilePath);
        $mail->isHTML(true);
        $mail->Subject = 'Factura comanda';
        $mail->Body = 'Va multumim pentru comanda! Factura dvs. este atasata.';

        $mail->send();
        
    
        $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
        $delete_cart->execute([$user_id]);

    } catch (Exception $e) {
        echo "Emailul nu s-a putut trimite. Mailer Error: {$mail->ErrorInfo}";
    }

    // plata
    if ($cart_total == 0) {
        $message[] = 'Cosul este gol';
    } else {
        if ($isCardPayment) {
            // Stripe 
            \Stripe\Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

            try {
                $session = \Stripe\Checkout\Session::create([
                    'payment_method_types' => ['card'],
                    'customer_email' => $email,
                    'line_items' => [[
                        'price_data' => [
                            'currency' => 'ron',
                            'product_data' => [
                                'name' => 'Comanda eBiciclist',
                                'description' => $total_products,
                            ],
                            'unit_amount' => $cart_total * 100,
                        ],
                        'quantity' => 1,
                    ]],
                    'mode' => 'payment',
                    'success_url' => 'http://localhost/ebiciclist/comenzi.php',
                    'cancel_url' => 'http://localhost/ebiciclist/finalizare_comanda.php',
                ]);

                // Inserare comanda în baza de date
                $insert_order = $conn->prepare("INSERT INTO `orders`(user_id, name, number, email, method, address, total_products, total_price, placed_on) VALUES(?,?,?,?,?,?,?,?,?)");
                $insert_order->execute([$user_id, $name, $number, $email, $method, $address, $total_products, $cart_total, $placed_on]);

                // Clear cart
                $reset_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
                $reset_cart->execute([$user_id]);

                // Redirect la Stripe checkout
                header('Location: ' . $session->url);
                exit();
            } catch (Exception $e) {
                $message[] = 'Eroare la procesarea plății: ' . $e->getMessage();
            }
        } elseif ($method === 'ramburs' || $method === 'transfer bancar') {
            $insert_order = $conn->prepare("INSERT INTO `orders`(user_id, name, number, email, method, address, total_products, total_price, placed_on) VALUES(?,?,?,?,?,?,?,?,?)");
            $insert_order->execute([$user_id, $name, $number, $email, $method, $address, $total_products, $cart_total, $placed_on]);
            $reset_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
            $reset_cart->execute([$user_id]);

            // Redirect la pagina comenzi
            header('Location: comenzi.php');
            exit();
        } else {
            $message[] = 'Metoda de plata nu este suportată';
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
        <title>Finalizare comandă</title>

        <script src="https://js.stripe.com/v3/"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
        <link rel="stylesheet" href="css/general.css">

    </head>
    <body>
    
        <?php include 'header.php'; ?>


        <section class="display-orders">
            
            <h5><span>Pentru opțiunea de plată prin virament bancar, livrarea comenzii se face după intrarea sumei în contul nostru.</span>
            <br> <span>Datele necesare pentru efectuarea plății:</span>
            <br>SC EBICICLIST SRL<br>
            CUI RO 563754310<br>
            Nr. Reg.Com. J41/5841/2020<br>
            Sediul social: Soseaua Stefan Cel Mare 9 Bucuresti<br>
            Banca Raiffeisen Bank Cont IBAN: RO75 RZBR 0000 0100 1001 0001</h5>
            <hr>
        
            <?php
                $cart_grand_total = 0;
                $select_cart_items = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
                $select_cart_items->execute([$user_id]);
                if($select_cart_items->rowCount() > 0){
                    while($fetch_cart_items = $select_cart_items->fetch(PDO::FETCH_ASSOC)){
                        $cart_total_price = ($fetch_cart_items['price'] * $fetch_cart_items['quantity']);
                        $cart_grand_total += $cart_total_price;
            ?>
            <div class="grand-total"><?= $fetch_cart_items['name']; ?> <span>(<?= ''.$fetch_cart_items['price'].' Lei x '. $fetch_cart_items['quantity']; ?>)</span> </div>
            <?php
                }
            }else{
                echo '<p class="empty">coșul tau este gol!</p>';
                header('Location: comenzi.php');
                    exit;
            }
            
            ?>
            <div class="grand-total">Total : <span><?= $cart_grand_total; ?></span> Lei</div>
            </section>

        <section class="checkout-orders">

            <form action="" method="POST">
                <h3>Plasează comanda</h3>

                <div class="flex">
                    <div class="inputBox">
                        <span>Nume:</span>
                        <input type="text" name="name" placeholder="Introdu numele tău" class="box" required>
                    </div>
                    <div class="inputBox">
                        <span>Tel:</span>
                        <input type="text" name="number" placeholder="0760000000" class="box" pattern="07\d{8}" required>
                    </div>
                    <div class="inputBox">
                        <span>Email:</span>
                        <input type="email" name="email" placeholder="Introdu email" class="box" required>
                    </div>
                    <div class="inputBox">
                        <span>Metodă plată:</span>
                        <select name="method" class="box" required>
                            <option value="ramburs">Plata ramburs</option>
                            <option value="transfer bancar">Transfer bancar</option>
                            <option value="card">Card</option>
                        </select>
                    </div>
                    <div class="inputBox">
                        <span>Adresă:</span>
                        <input type="text" name="address" placeholder="Strada, nr" class="box" required>
                    </div>
                    <div class="inputBox">
                        <span>Oraș:</span>
                        <input type="text" name="city" placeholder="București" class="box" required>
                    </div>
                    <div class="inputBox">
                        <span>Județ:</span>
                        <input type="text" name="state" placeholder="București" class="box" required>
                    </div>
                    <div class="inputBox">
                        <span>Tară:</span>
                        <input type="text" name="country" placeholder="România" class="box" required>
                    </div>
                    <div class="inputBox">
                        <span>Cod poștal:</span>
                        <input type="number" min="0" name="pin_code" placeholder="02010" class="box" required>
                    </div>
                </div>

                <input type="submit" name="order" class="btn <?= ($cart_grand_total > 1) ? '' : 'disabled'; ?>" value="Plasează comanda">
            </form>


        </section>

        <?php include 'footer.php'; ?>

        <script src="js/script.js"></script>

    </body>
</html>