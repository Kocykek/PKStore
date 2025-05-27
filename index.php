<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>PKStore - Sklep internetowy</title>
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
    <div id="allCategoriesWrapper" style="text-align: center;">
        <div id="allCategories">
            <?php
            $conn = new mysqli("localhost", "root", "newpassword", "pkstore");
            $conn->set_charset("utf8");
            if ($conn->connect_error) {
                die("Błąd połączenia: " . $conn->connect_error);
            }
            $result2 = $conn->query("SELECT nazwa, zdjecie FROM kategorie");
            
            if ($result2->num_rows > 0)
            {
                while ($row = $result2->fetch_assoc())
                {
					$categoryName = htmlspecialchars($row['nazwa']);
            $categoryUrl = urlencode($row['nazwa']); // bezpieczne do URL
					echo "<a href='wyszukaj.php?title=&category=$categoryUrl' class='categoryLink'>";
                    echo "<div class='box'><div id='boxImage'><img src='images/low_".htmlspecialchars($row['zdjecie']).".jpg' width='100' height='100'></div><div id='boxText'>".htmlspecialchars($row['nazwa'])."</div></div>";
					echo "</a>";
                }
            }
            $conn->close();
            ?>
        </div>
    </div>
    <div id="bannerContainer">
        <div id="leftBlok"></div>
        <a href="wyszukaj.php?title=&category=Zabawki">
    <img src="images/banner.jpg">
    </a>
            <div id="rightBlok"></div>
    </div>
    <div id="recommendedOffers"><br>
    <span id="recommendedOffersText">Wybrane produkty dla ciebie:</span><br>

    <?php
    // Połączenie z bazą
    $conn = new mysqli("localhost", "root", "newpassword", "pkstore");
    if ($conn->connect_error) {
        die("Błąd połączenia z bazą: " . $conn->connect_error);
    }

    // Zapytanie losujące 5 produktów
    $sql = "SELECT id, nazwa, zdjecie, cena FROM produkty ORDER BY RAND() LIMIT 5";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $image = !empty($row['zdjecie']) ? htmlspecialchars($row['zdjecie']) : 'unknown.jpg';

            echo "<a id='productHyperLink' href='produkt.php?id=" . urlencode($row['id']) . "'>";
            echo "<div id='oneSingleProduct'>";
            echo "<div id='photoForProduct'><img src='images/produkty/" . $image . "' alt='Product Image' style='max-width:120px; max-height: 120px;'></div><br>";

            echo "<div id='titleForProduct'>" . htmlspecialchars($row['nazwa']) . "</div><br>";

            echo "<div id='priceForProduct'>" . htmlspecialchars($row['cena']) . " zł";
            echo "<br><input id='dodajDoKoszykaView' type='submit' value='Zobacz produkt'>";
            echo "</div><br>";

            echo "</div></a>";
        }
    } else {
        echo "<p>Brak produktów do wyświetlenia.</p>";
    }

    $conn->close();
    ?>
</div>


</body>
</html>
