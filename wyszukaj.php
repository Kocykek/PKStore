<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PKStore - Wyszukiwarka</title>
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
	<div id='produktyContainerWyszukaj'>
<?php
// polaczenie
$conn = new mysqli("localhost", "root", "newpassword", "pkstore");
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("Błąd połączenia: " . $conn->connect_error);
}

// $_GET zmienne
$title = isset($_GET['title']) ? trim($_GET['title']) : '';
$categoryName = isset($_GET['category']) ? trim($_GET['category']) : '';


// sql
$stmt = $conn->prepare("SELECT id FROM kategorie WHERE nazwa = ?");
$stmt->bind_param("s", $categoryName);
$stmt->execute();
$result = $stmt->get_result();
$category = $result->fetch_assoc();
$categoryId = $category ? $category['id'] : null;
$stmt->close();

if ($categoryId !== null) {

    $stmt = $conn->prepare("SELECT * FROM produkty WHERE id_kategoria = ?");
    $stmt->bind_param("i", $categoryId);
    $stmt->execute();
    $result = $stmt->get_result();

    $found = false;
    $threshold = 3;
    $searchWords = array_filter(explode(' ', mb_strtolower($title)));

    while ($row = $result->fetch_assoc()) {
        $tytul = mb_strtolower($row['tytul']);
        $opis = mb_strtolower($row['opis']);

        // rozbij tytuł i opis na słowa
        $tytulWords = array_filter(explode(' ', $tytul));
        $opisWords = array_filter(explode(' ', $opis));

        if (empty($searchWords)) {
            $match = true;  // brak słów do wyszukiwania, pokaż wszystko
        } else {
            $match = false;

            foreach ($searchWords as $word) {
                // sprawdzaj każde słowo tytułu
                foreach ($tytulWords as $tWord) {
                    if (
                        stripos($tWord, $word) !== false ||  // zawiera fragment
                        levenshtein($word, $tWord) <= $threshold
                    ) {
                        $match = true;
                        break 2;  // przerwij oba foreach - znaleziono dopasowanie
                    }
                }

                // jeśli nie znaleziono w tytule, sprawdź opis
                if (!$match) {
                    foreach ($opisWords as $oWord) {
                        if (
                            stripos($oWord, $word) !== false ||
                            levenshtein($word, $oWord) <= $threshold
                        ) {
                            $match = true;
                            break 2;
                        }
                    }
                }
            }
        }

        // wygeneruj blok dla tych co pasują
        if ($match) {
            $found = true;
            echo "<a id='productHyperLink' href='produkt.php?id=".urlencode($row['id'])."'>";
            echo "<div id='oneSingleProduct'>";
                        $image = !empty($row['zdjecie']) ? htmlspecialchars($row['zdjecie']) : 'unknown.jpg';
    echo "<div id='photoForProduct'><img src='images/produkty/" . $image . "' alt='Product Image' style='max-width:120px; max-height: 120px;'></div><br>";
            
            echo "<div id='titleForProduct'>" . htmlspecialchars($row['nazwa']) . "<br>";
            //echo "<span id='starsProducts'>4/5 Gwiazdek</span><br><span id='ileOsobProducts'>2800 osób kupiło ten produkt</span>";
            echo "</div><br>";
            echo "<div id='priceForProduct'>" . htmlspecialchars($row['cena']) . " zł";
            //echo "<br><span id='secondPriceForProduct'>Z dostawą 134.99 zł<br>Stan: Nowy</span>";
            echo "<br><input id='dodajDoKoszykaView' 'type='submit' value='Zobacz produkt'>";
            echo "</div><br>";
			
echo "</div></a>";
        }
    }

    if (!$found) {
        echo "Przykro nam, nie znaleźliśmy żadnych wyników pasujących do zapytania.";
    }

    $stmt->close();
} else {
    echo "Nie znaleziono podanej kategorii.";
}

$conn->close();
?>
</div>
</body>
</html>
