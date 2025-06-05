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

if (!isset($_SESSION['user_id'])) {
    echo "Musisz być zalogowany, aby dodać produkt. <a href='login.php'>Zaloguj się</a>";
    exit;
}

$user_id = $_SESSION['user_id'];

// Połączenie do bazy tylko na potrzeby pobrania kategorii
$conn = new mysqli("localhost", "root", "newpassword", "pkstore");
if ($conn->connect_error) {
    die("Błąd połączenia: " . $conn->connect_error);
}

// Pobranie kategorii
$kategorie = [];
$result = $conn->query("SELECT id, nazwa FROM kategorie ORDER BY nazwa");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $kategorie[] = $row;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nazwa = trim($_POST['nazwa'] ?? '');
    $opis = trim($_POST['opis'] ?? '');
    $cena = floatval($_POST['cena'] ?? 0);
    $id_kategoria = intval($_POST['id_kategoria'] ?? 0);
    $tagi = trim($_POST['tagi'] ?? '');
    $data_dodania = date('Y-m-d H:i:s');
    
    // data_wygasniecia z input typu datetime-local ma format "YYYY-MM-DDTHH:MM"
    // trzeba zmienić T na spację i dodać sekundy ":00"
    $data_wygasniecia_raw = $_POST['data_wygasniecia'] ?? null;
    $data_wygasniecia = null;
    if ($data_wygasniecia_raw) {
        $data_wygasniecia = str_replace('T', ' ', $data_wygasniecia_raw) . ':00';
    }

    $errors = [];
    if (!$nazwa) $errors[] = "Nazwa jest wymagana.";
    if ($cena <= 0) $errors[] = "Cena musi być większa od 0.";
    if (!$id_kategoria) $errors[] = "Wybierz kategorię.";
    if (!$data_wygasniecia) $errors[] = "Podaj datę wygaśnięcia.";

    // Upload zdjęcia - tak jak wcześniej
    $uploadDir = __DIR__ . '/images/produkty/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $zdjecieName = null;
    if (isset($_FILES['zdjecie_file']) && $_FILES['zdjecie_file']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['zdjecie_file'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = "Błąd podczas przesyłania pliku.";
        } else {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($file['type'], $allowedTypes)) {
                $errors[] = "Dozwolone są tylko pliki JPG, PNG i GIF.";
            } else {
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $zdjecieName = uniqid('img_', true) . '.' . $ext;

                $destPath = $uploadDir . $zdjecieName;
                if (!move_uploaded_file($file['tmp_name'], $destPath)) {
                    $errors[] = "Nie udało się zapisać pliku na serwerze.";
                }
            }
        }
    }

    if (count($errors) === 0) {
        $stmt = $conn->prepare("INSERT INTO produkty (nazwa, opis, cena, zdjecie, id_kategoria, tagi, id_sprzedajacego, data_dodania, data_wygasniecia) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdsiisss", $nazwa, $opis, $cena, $zdjecieName, $id_kategoria, $tagi, $user_id, $data_dodania, $data_wygasniecia);

        if ($stmt->execute()) {
            echo "<p>Produkt został dodany pomyślnie! <a href='dodaj_produkt.php'>Dodaj kolejny</a> | <a href='index.php'>Do sklepu</a></p>";
        } else {
            echo "Błąd podczas dodawania produktu: " . $stmt->error;
        }

        $stmt->close();
    } else {
        foreach ($errors as $error) {
            echo "<p style='color:red;'>$error</p>";
        }
    }
}

$conn->close();
?>

<h2>Dodaj nowy produkt</h2>
<form method="post" action="dodaj_produkt.php" enctype="multipart/form-data">
    <label>Nazwa:<br><input type="text" name="nazwa" required></label><br><br>
    <label>Opis:<br><textarea name="opis" rows="4" cols="50"></textarea></label><br><br>
    <label>Cena:<br><input type="number" step="0.01" name="cena" required></label><br><br>
    <label>Zdjęcie:<br><input type="file" name="zdjecie_file" accept="image/*"></label><br><br>
    <label>Kategoria:<br>
        <select name="id_kategoria" required>
            <option value="">-- wybierz kategorię --</option>
            <?php foreach ($kategorie as $kat): ?>
                <option value="<?= htmlspecialchars($kat['id']) ?>"><?= htmlspecialchars($kat['nazwa']) ?></option>
            <?php endforeach; ?>
        </select>
    </label><br><br>
    <label>Tagi (oddzielone przecinkami):<br><input type="text" name="tagi"></label><br><br>
    <label>Data wygaśnięcia:<br><input type="datetime-local" name="data_wygasniecia" required></label><br><br>
    <button type="submit">Dodaj produkt</button>
</form></div></div>	
</body>
</html>
