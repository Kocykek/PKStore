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
    <?php if (isset($_SESSION['user_email'])): ?>
        <h1>Profil użytkownika</h1>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
        <p><strong>Imię:</strong> <?php echo htmlspecialchars($_SESSION['user_imie']); ?></p>
        <p><strong>Nazwisko:</strong> <?php echo htmlspecialchars($_SESSION['user_nazwisko']); ?></p>
        <p><strong>Rola:</strong> <?php echo htmlspecialchars($_SESSION['user_rola']); ?></p>
        <p><strong>Liczba kupionych produktów:</strong></p>
        <p><strong>Liczba wystawionych produktów:</strong></p>
        <p><strong>Liczba sprzedanych produktów:</strong></p>
        <p><strong>Konto utworzone:</strong></p>
        <p><a href="logout.php">Wyloguj się</a></p>
    <?php else: ?>
        <p>Nie jesteś zalogowany. <a href="login.php">Zaloguj się</a></p>
    <?php endif; ?>
</body>
</html>
