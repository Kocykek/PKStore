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
        <a href=""><img src="images/logo.png" width='100'></a>
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

if (isset($_SESSION['user_imie'])) {
    $imie = htmlspecialchars($_SESSION['user_imie']);
    echo "<a href='uzytkownik.php'><div id='userPanel'>Witaj, $imie! <img src='images/uzytkownik.jpg' width='50' alt='znany użytkownik'></div></a>";
} else {
    ?>
    <a id="userPanel" href="login.php">
        Zaloguj się <img src="images/nieznany_uzytkownik.jpg" width="50" alt="Nieznany użytkownik">
    </a>
    <?php
}
?>


    </div>

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
    echo "<h2>Wyniki wyszukiwania:</h2>";

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

        // pusty tytul -> pokaz wszystko
        if (empty($searchWords)) {
            $match = true;
        } else {
            $match = false;

            foreach ($searchWords as $word) {
                if (
                    stripos($tytul, $word) !== false ||
                    stripos($opis, $word) !== false ||
                    levenshtein($word, $tytul) <= $threshold ||
                    levenshtein($word, $opis) <= $threshold
                ) {
                    $match = true;
                    break; 
                }
            }
        }

        // wygeneruj blok dla tych co pasują
        if ($match) {
            $found = true;
            echo "<div style='margin-bottom: 20px;'>";
            echo "<strong>" . htmlspecialchars($row['tytul']) . "</strong><br>";
            echo "Opis: " . htmlspecialchars($row['opis']) . "<br>";
            echo "Cena: " . htmlspecialchars($row['cena']) . " zł<br>";
            echo "</div><hr>";
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

</body>
</html>