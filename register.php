<?php
$conn = new mysqli("localhost", "root", "newpassword", "pkstore");
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("Błąd z połączeniem: " . $conn->connect_error);
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $haslo_plain = $_POST["haslo"];
    $imie = trim($_POST["imie"]);
    $nazwisko = trim($_POST["nazwisko"]);
    $rola = 'uzytkownik';

    // czy wszystko wypełnione
    if (empty($email) || empty($haslo_plain) || empty($imie) || empty($nazwisko)) {
        $message = "❌ Proszę wypełnić wszystkie pola.";
    } else {
        // czy email istnieje?
        $stmt = $conn->prepare("SELECT id FROM uzytkownicy WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $message = "❌ Użytkownik z tym adresem email już istnieje.";
        } else {

            $haslo = password_hash($haslo_plain, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO uzytkownicy (email, haslo, imie, nazwisko, rola) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $email, $haslo, $imie, $nazwisko, $rola);

            if ($stmt->execute()) {
                $message = "✅ Rejestracja udana. <a href='login.php'>Zaloguj się</a>.";
            } else {
                $message = "❌ Wystąpił błąd przy rejestracji.";
            }
        }

        $stmt->close();
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div id="registerContainer">
    <a href="index.php"><img src="images/logo.png" width='250'></a>
    <div id="formThingy">
<form method="post">
    <h2>Rejestracja</h2>
    <?php if (!empty($message)) echo "<p>$message</p>"; ?>
    Email: <input type="email" name="email" required><br>
    Hasło: <input type="password" name="haslo" required><br>
    Imię: <input type="text" name="imie" required><br>
    Nazwisko: <input type="text" name="nazwisko" required><br>
    <button type="submit">Zarejestruj się</button>

    <br><br>Masz już konto? <a href="login.php">Zaloguj się!</a>
</form>
</div>
</div>
</body>
</html>
