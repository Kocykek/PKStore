<html>
<head>
	<link rel="stylesheet" href="styles.css">
</head>
<body>
<div id="topMenu">
        <a href="index.php"><img src="images/logo.png" width='100'></a>
        <form method="GET" action="wyszukaj.php">
    <input type="text" name="title" placeholder="Wyszukaj produkt po tytule...">
        
        <select name="category">
            <?php
            $conn = new mysqli("localhost", "root", "newpassword", "pkstore");
            $conn->set_charset("utf8");

            if ($conn->connect_error) {
                die("Błąd połączenia: " . $conn->connect_error);
            }

            $result = $conn->query("SELECT nazwa FROM kategorie");

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='" . htmlspecialchars($row['nazwa']) . "'>" . htmlspecialchars($row['nazwa']) . "</option>";
                }
            } else {
                echo "<option>Brak kategorii</option>";
            }

            $conn->close();
            ?>
        </select>

        <input type="submit" value="Szukaj">
        </form>
  <?php
session_start();

$cartCount = 0;

if (isset($_SESSION['user_id'])) {
  
    $userId = $_SESSION['user_id'];
    $conn = new mysqli("localhost", "root", "newpassword", "pkstore");
    if ($conn->connect_error) {
        die("Błąd połączenia z bazą: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT SUM(ilosc) AS total FROM koszyk WHERE id_uzytkownika = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($total);
    $stmt->fetch();
    $cartCount = $total ?? 0;

    $stmt->close();
    $conn->close();

} else {
   
    if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $qty) {
            $cartCount += $qty;
        }
    }
}

echo '<a href="koszyk.php" style="position: relative; display: inline-block;">';
echo '<img src="images/shopping_cart.png" width="40" alt="Koszyk">';
if ($cartCount > 0) {
    echo '<span style="
        position: absolute;
        top: -5px;
        right: -5px;
        background: red;
        color: white;
        font-weight: bold;
        padding: 2px 6px;
        border-radius: 50%;
        font-size: 14px;
        user-select: none;
    ">' . $cartCount . '</span>';
}
echo '</a>';

// User greeting or login link
if (isset($_SESSION['user_imie'])) {
    $imie = htmlspecialchars($_SESSION['user_imie']);
    echo "<a href='uzytkownik.php'><div id='userPanel'>Witaj, $imie! <img src='images/uzytkownik.jpg' width='50' alt='znany użytkownik'></div></a>";
} else {
    echo '<a id="userPanel" href="login.php">
            Zaloguj się <img src="images/nieznany_uzytkownik.jpg" width="50" alt="Nieznany użytkownik">
          </a>';
}
?>



    </div>

<div id="finalizationContainerWrapper">
<div id="finalizationContainer">

<?php
session_start();



if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: finalizacja.php");
    exit;
}

$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    echo "Koszyk jest pusty. <a href='index.php'>Wróć do sklepu</a>";
    exit;
}

$payment_method = $_POST['payment_method'] ?? '';
$courier = $_POST['courier'] ?? '';
$street = trim($_POST['address_street'] ?? '');
$postcode = trim($_POST['address_postcode'] ?? '');
$city = trim($_POST['address_city'] ?? '');

if (!$payment_method || !$courier || !$street || !$postcode || !$city) {
    echo "Wszystkie pola są wymagane. <a href='finalizacja.php'>Wróć do formularza</a>";
    exit;
}

if (!isset($_SESSION['user_id'])) {
    echo "Musisz być zalogowany, aby złożyć zamówienie. <a href='login.php'>Zaloguj się</a>";
    exit;
}
$user_id = $_SESSION['user_id'];

$conn = new mysqli("localhost", "root", "newpassword", "pkstore");
if ($conn->connect_error) {
    die("Błąd połączenia: " . $conn->connect_error);
}

$stmt = $conn->prepare("SELECT imie, nazwisko, email FROM uzytkownicy WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows !== 1) {
    echo "Nie znaleziono użytkownika.";
    exit;
}
$userData = $result->fetch_assoc();
$stmt->close();

$fullAddress = $street . ", " . $postcode . " " . $city;

$productIds = array_keys($cart);
$idsPlaceholders = implode(',', array_fill(0, count($productIds), '?'));
$types = str_repeat('i', count($productIds));

$stmt = $conn->prepare("SELECT id, cena FROM produkty WHERE id IN ($idsPlaceholders)");
$stmt->bind_param($types, ...$productIds);
$stmt->execute();
$result = $stmt->get_result();

$totalPrice = 0;
while ($row = $result->fetch_assoc()) {
    $pid = $row['id'];
    if (isset($cart[$pid])) {
        $totalPrice += $row['cena'] * $cart[$pid];
    }
}
$stmt->close();

// Dodaj zamówienie
$stmt = $conn->prepare("INSERT INTO zamowienia (id_klienta, imie, nazwisko, adres, email, metoda_platnosci, data_zamowienia, typ_dostawy) VALUES (?, ?, ?, ?, ?, ?, NOW(), ?)");
$stmt->bind_param("issssss", $user_id, $userData['imie'], $userData['nazwisko'], $fullAddress, $userData['email'], $payment_method, $courier);

if ($stmt->execute()) {
    $orderId = $stmt->insert_id;
    $stmt->close();

    // Zapisz produkty w zamowienia_produkty
    $stmtDetails = $conn->prepare("INSERT INTO zamowione_produkty (id_zamowienia, id_produktu, ilosc) VALUES (?, ?, ?)");

    foreach ($cart as $productId => $quantity) {
        $stmtDetails->bind_param("iii", $orderId, $productId, $quantity);
        $stmtDetails->execute();

        // Aktualizacja ilości produktu w magazynie
        $stmtUpdate = $conn->prepare("UPDATE produkty SET ilosc = ilosc - ? WHERE id = ?");
        $stmtUpdate->bind_param("ii", $quantity, $productId);
        $stmtUpdate->execute();
        $stmtUpdate->close();
}

    $stmtDetails->close();

    unset($_SESSION['cart']);

    echo "<h2>Dziękujemy za złożenie zamówienia!</h2>";
    echo "<p>Twoje zamówienie nr <strong>$orderId</strong> zostało zarejestrowane.</p>";
    echo "<p>Łączna kwota do zapłaty: <strong>" . number_format($totalPrice, 2) . " zł</strong></p>";
    echo "<p>Metoda płatności: <strong>" . htmlspecialchars($payment_method) . "</strong></p>";
    echo "<p>Typ dostawy: <strong>" . htmlspecialchars($courier) . "</strong></p>";
    echo "<p>Adres dostawy: <strong>" . htmlspecialchars($fullAddress) . "</strong></p>";
    echo "<a href='index.php'>Powrót do sklepu</a>";

} else {
    echo "Błąd podczas składania zamówienia: " . $stmt->error;
}

$stmt = $conn->prepare("DELETE FROM koszyk WHERE id_uzytkownika = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->close();

// Wyczyść sesję koszyka
unset($_SESSION['cart']);

$conn->close();

?>
</div></div> 	
</body>
</html>
