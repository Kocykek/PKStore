<?php
session_start();

$conn = new mysqli("localhost", "root", "newpassword", "pkstore");
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $conn->real_escape_string(trim($_POST["email"]));

    $stmt = $conn->prepare("SELECT haslo, imie, nazwisko, rola FROM uzytkownicy WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($hashed_password, $imie, $nazwisko, $rola);
    $stmt->fetch();

    if ($hashed_password && password_verify($_POST["haslo"], $hashed_password)) {
        $_SESSION["user_email"] = $email;
        $_SESSION["user_imie"] = $imie;
        $_SESSION["user_nazwisko"] = $nazwisko;
        $_SESSION["user_rola"] = $rola;
        $stmt->close();
        $conn->close();
        header("Location: index.php");
        exit;
    } else {
        echo "Niepoprawny email lub hasło.";
    }

    $stmt->close();
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
    <div id="loginContainer">
            <a href="index.php"><img src="images/logo.png" width='250'></a>
<div id="formThingy">
<form method="post">
    <h2>Logowanie</h2>
    Email: <input type="email" name="email" required><br><br>
    Hasło: <input type="password" name="haslo" required><br>
    <button id="loginButton" type="submit">Zaloguj się</button>

    <br><br>Nie posiadasz konta? <a href="register.php">Zarejestruj się!</a>
</form>
</div>
</div>
</body>
</html>