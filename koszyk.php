<?php
session_start();

$cart = [];


if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $conn = new mysqli("localhost", "root", "newpassword", "pkstore");
    if ($conn->connect_error) {
        die("Błąd połączenia z bazą: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT id_produktu, ilosc FROM koszyk WHERE id_uzytkownika = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $cart[$row['id_produktu']] = $row['ilosc'];
    }
    $stmt->close();
} else {
    $conn = new mysqli("localhost", "root", "newpassword", "pkstore");
    $cart = $_SESSION['cart'] ?? [];
}

$productDetails = [];
if (!empty($cart)) {
    $ids = implode(',', array_map('intval', array_keys($cart)));
    $sql = "SELECT * FROM produkty WHERE id IN ($ids)";
    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
        $productDetails[$row['id']] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Koszyk - szczegóły produktów</title>
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

<div id="shoppingCartPanelWrapper">
	<div id="shoppingCartPanel">

<?php if (empty($productDetails)): ?>
    <p>Twój koszyk jest pusty.</p>
<?php else: ?>
    <ul>
        <?php 
        $totalPrice = 0;
        foreach ($cart as $productId => $quantity): 
            $product = $productDetails[$productId] ?? null;
            if (!$product) continue;
            $itemTotal = $product['cena'] * $quantity;
            $totalPrice += $itemTotal;

        ?>
        <div id='oneInShoppingCart'>
               <?php
$zdjecie = !empty($product['zdjecie']) ? htmlspecialchars($product['zdjecie']) : 'unknown.jpg';
?>
<div id='leftSideOfShoppingItem'>
    <img src="images/produkty/<?= $zdjecie ?>" alt="Produkt" width="100" height="100">
</div><div id='rightPartOfShoppingItem'><h3><?= htmlspecialchars($product['nazwa']) ?></h3>
                <p><strong>Cena:</strong> <?= number_format($product['cena'], 2) ?> zł</p>
                <p><strong>Ilość w koszyku:</strong> <?= htmlspecialchars($quantity) ?></p></div>
                
                <form method="POST" action="aktualizuj_koszyk.php" style="margin-top: 10px;">
    <input type="hidden" name="product_id" value="<?= $productId ?>">
    <label>
        Ilość:
        <input type="number" name="quantity" value="<?= htmlspecialchars($quantity) ?>" min="0" style="width: 50px;">
    </label>
    <button type="submit">Zaktualizuj</button>
    <button type="submit" name="quantity" value="0">Usuń</button>
</form>
                </div><hr>
            
        <?php endforeach; ?>
    </ul>
    
    <div id="cartSummary" style="font-weight: bold; font-size: 1.2em; margin-top: 15px;">
            <p>Łączna kwota do zapłaty: <?= number_format($totalPrice, 2) ?> zł</p>
            <form action="finalizacja.php" method="POST">
                <button type="submit" style="padding: 10px 20px; font-size: 1em; cursor: pointer;">Przejdź do płatności</button>
            </form>
        </div>
<?php endif; ?>
</div></div>
</body>
</html>
