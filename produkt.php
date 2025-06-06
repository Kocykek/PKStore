<?php
// produkt.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$conn = new mysqli("localhost", "root", "newpassword", "pkstore");
if ($conn->connect_error) {
    die("Błąd połączenia: " . $conn->connect_error);
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Nieprawidłowe ID produktu.";
    exit;
}
$productId = intval($_GET['id']);

$stmt = $conn->prepare("
    SELECT p.*, u.email AS sprzedajacy_email, p.id_sprzedajacego
    FROM produkty p 
    LEFT JOIN uzytkownicy u ON p.id_sprzedajacego = u.id 
    WHERE p.id = ?
");
$stmt->bind_param("i", $productId);
$stmt->execute();
$result76 = $stmt->get_result();

if ($result76->num_rows === 0) {
    echo "Produkt nie znaleziony.";
    exit;
}

$row76 = $result76->fetch_assoc();
$image76 = !empty($row76['zdjecie']) ? htmlspecialchars($row76['zdjecie']) : 'unknown.jpg';
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($row76['nazwa']) ?> - Szczegóły Produktu</title>
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
$userId = $_SESSION['user_id'] ?? null;
$isSeller = ($userId !== null && $userId == $row76['id_sprzedajacego']);
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
    <div id="productPanelWrapper">
<div id="productPanel">
    <img src="images/produkty/<?= $image76 ?>" width='200' alt="Zdjęcie produktu">

    <div id="productTitle"><?= htmlspecialchars($row76['nazwa']) ?></div>
    <div id="productPrice"><?= htmlspecialchars($row76['cena']) ?> zł</div>
    <div id="productQuantity" style="font-weight: bold; font-size: 20px;">
    Ilość dostępna: <?= htmlspecialchars($row76['ilosc']) ?>
</div>


    <div id="productDescription">
        <?= !empty($row76['opis']) ? nl2br(htmlspecialchars($row76['opis'])) : "Brak opisu produktu." ?>
    </div>

    <div id="sellerInfo">
        Sprzedający: 
        <?= htmlspecialchars($row76['sprzedajacy_email']) ?>
    </div>

<form id="buyForm" method="post" action="dodaj_do_koszyka.php">
    <input type="hidden" name="product_id" value="<?= $row76['id'] ?>">

    <?php if (!$isSeller): ?>
    Ilość: 
    <input type="number" name="quantity" value="1" min="1"> 
        <input type="submit" name="action" value="Dodaj do koszyka">
        <input type="submit" name="action" value="Kup teraz">
    <?php else: ?>
        <p><em>Jesteś sprzedającym tego produktu.</em></p>
    <?php endif; ?>
</form>

</div>
</div>
<br><hr>
PKStore &copy;
</body>
</html>
