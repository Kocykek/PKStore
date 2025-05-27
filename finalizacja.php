
<?php
session_start();
$userId = $_SESSION['user_id'] ?? null;
$cart = [];
if (!$userId) {
    echo "<p>Musisz być zalogowany, aby sfinalizować zamówienie. <a href='login.php'>Zaloguj się</a></p>";
    exit;
}

// Połączenie z bazą danych
$conn = new mysqli("localhost", "root", "newpassword", "pkstore");
if ($conn->connect_error) {
    die("Błąd połączenia: " . $conn->connect_error);
}

// Pobierz koszyk użytkownika z bazy
$stmt = $conn->prepare("SELECT id_produktu, ilosc FROM koszyk WHERE id_uzytkownika = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $cart[$row['id_produktu']] = $row['ilosc'];
    $_SESSION['cart'] = $cart;
}

$stmt->close();

if (empty($cart)) {
    echo "<p>Twój koszyk jest pusty. <a href='index.php'>Wróć do sklepu</a></p>";
    exit;
}


// Połączenie z bazą, pobranie szczegółów produktów jak wcześniej
$conn = new mysqli("localhost", "root", "newpassword", "pkstore");
if ($conn->connect_error) {
    die("Błąd połączenia: " . $conn->connect_error);
}

// szczegóły produktów dla koszyka
$productIds = array_keys($cart);
$productDetails = [];
if (!empty($productIds)) {
    $idsPlaceholders = implode(',', array_fill(0, count($productIds), '?'));
    $types = str_repeat('i', count($productIds));
    $stmt = $conn->prepare("SELECT id, nazwa, cena FROM produkty WHERE id IN ($idsPlaceholders)");
    $stmt->bind_param($types, ...$productIds);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $productDetails[$row['id']] = $row;
    }
    $stmt->close();
}
$conn->close();

$totalPrice = 0;
foreach ($cart as $pid => $qty) {
    if (isset($productDetails[$pid])) {
        $totalPrice += $productDetails[$pid]['cena'] * $qty;
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>PKStore - Finalizacja zamówienia</title>
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
<h1>Finalizacja zamówienia</h1>

<h2>Twoje produkty:</h2>
<ul>
<?php foreach ($cart as $pid => $qty):
    $product = $productDetails[$pid] ?? null;
    if (!$product) continue;
    $itemTotal = $product['cena'] * $qty;
?>
    <li><?= htmlspecialchars($product['nazwa']) ?> — <?= $qty ?> × <?= number_format($product['cena'], 2) ?> zł = <?= number_format($itemTotal, 2) ?> zł</li>
<?php endforeach; ?>
</ul>

<p><strong>Łączna kwota do zapłaty:</strong> <?= number_format($totalPrice, 2) ?> zł</p>

<form method="POST" action="potwierdzenie_zamowienia.php">
    <h3>Wybierz metodę płatności:</h3>
    <label><input type="radio" name="payment_method" value="przelew" required> Przelew bankowy</label><br>
    <label><input type="radio" name="payment_method" value="karta"> Karta płatnicza</label><br>
    <label><input type="radio" name="payment_method" value="przy_odbiorze"> Płatność przy odbiorze</label><br>

    <h3>Wybierz kuriera:</h3>
    <select name="courier" required>
        <option value="">-- Wybierz kuriera --</option>
        <option value="dpd">DPD</option>
        <option value="inpost">InPost</option>
        <option value="poczta_polska">Poczta Polska</option>
    </select>

    <h3>Adres dostawy:</h3>
    <label>Ulica i numer domu/mieszkania:<br>
        <input type="text" name="address_street" required>
    </label><br><br>

    <label>Kod pocztowy:<br>
        <input type="text" name="address_postcode" pattern="\d{2}-\d{3}" placeholder="00-000" required>
    </label><br><br>

    <label>Miasto:<br>
        <input type="text" name="address_city" required>
    </label><br><br>

    <button type="submit">Potwierdź zamówienie</button>
</form>
</div></div>
</body>
</html>
