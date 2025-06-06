<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>PKStore - profil użytkownika</title>
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


    </div>
    <?php if (isset($_SESSION['user_email'])): ?>
    <div id="userContainerPanelProfileWrapper">
		<div id="userContainerPanelProfile">
			<p><strong><a href="dodaj_produkt.php">Dodaj produkt</a></strong></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
        <p><strong>Imię:</strong> <?php echo htmlspecialchars($_SESSION['user_imie']); ?></p>
        <p><strong>Nazwisko:</strong> <?php echo htmlspecialchars($_SESSION['user_nazwisko']); ?></p>
        <p><strong>Rola:</strong> <?php echo htmlspecialchars($_SESSION['user_rola']); ?></p>
        <p><a href="logout.php">Wyloguj się</a></p>
        </div>

        <?php
if (!isset($_SESSION['user_id'])) {
    echo "<p>Musisz być zalogowany, aby zobaczyć swoje zamówienia.</p>";
    exit;
}

$userId = $_SESSION['user_id'];

$conn = new mysqli("localhost", "root", "newpassword", "pkstore");
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("Błąd połączenia z bazą: " . $conn->connect_error);
}

// Pobieramy wszystkie zamówienia użytkownika
$stmtOrders = $conn->prepare("SELECT id, adres, metoda_platnosci, data_zamowienia, typ_dostawy FROM zamowienia WHERE id_klienta = ? ORDER BY data_zamowienia DESC");
$stmtOrders->bind_param("i", $userId);
$stmtOrders->execute();
$resultOrders = $stmtOrders->get_result();

if ($resultOrders->num_rows === 0) {
    echo "<p>Nie masz jeszcze żadnych zamówień.</p>";
} else {
    echo "<h2>Twoje zamówienia:</h2>";

    while ($order = $resultOrders->fetch_assoc()) {
        
        echo "<div style='border:1px solid #ccc; margin-bottom:20px; padding:10px;'>";
        echo "<h3>Zamówienie #" . htmlspecialchars($order['id']) . " z " . htmlspecialchars($order['data_zamowienia']) . "</h3>";
        echo "<p><strong>Adres:</strong> " . htmlspecialchars($order['adres']) . "</p>";
        echo "<p><strong>Metoda płatności:</strong> " . htmlspecialchars($order['metoda_platnosci']) . "</p>";
        echo "<p><strong>Typ dostawy:</strong> " . htmlspecialchars($order['typ_dostawy']) . "</p>";

        // Pobieramy produkty w tym zamówieniu
        $stmtProducts = $conn->prepare("
            SELECT p.nazwa, p.cena, zp.ilosc 
            FROM zamowione_produkty zp
            JOIN produkty p ON zp.id_produktu = p.id
            WHERE zp.id_zamowienia = ?
        ");
        $stmtProducts->bind_param("i", $order['id']);
        $stmtProducts->execute();
        $resultProducts = $stmtProducts->get_result();
        if ($resultProducts->num_rows > 0) {
            echo "<h4>Produkty:</h4><ul>";
            $totalPrice = 0;
            while ($product = $resultProducts->fetch_assoc()) {
                $linePrice = $product['cena'] * $product['ilosc'];
                $totalPrice += $linePrice;
                echo "<li>" 
                    . htmlspecialchars($product['nazwa']) . " (" . (int)$product['ilosc'] . " szt.) - " 
                    . number_format($linePrice, 2, ',', ' ') . " zł</li>";
            }
            echo "</ul>";
            echo "<p><strong>Razem:</strong> " . number_format($totalPrice, 2, ',', ' ') . " zł</p>";
        } else {
            echo "<p>Brak produktów w zamówieniu.</p>";
        }

        $stmtProducts->close();

        echo "</div>";
    }
}

$stmtOrders->close();
$conn->close();
?>

        </div>
    <?php else: ?>
        <p>Nie jesteś zalogowany. <a href="login.php">Zaloguj się</a></p>
    <?php endif; ?>
</body>
</html>
