


<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "SCRIPT STARTED<br>";
session_start();

echo "SESSION CONTENT:<br>";
var_dump($_SESSION);
echo "<br>";

function getDbConnection() {
    $conn = new mysqli("localhost", "root", "newpassword", "pkstore");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

function mergeCartToDatabase($userId, $sessionCart) {
    $conn = getDbConnection();

    foreach ($sessionCart as $productId => $quantity) {
        $stmt = $conn->prepare("SELECT ilosc FROM koszyk WHERE id_uzytkownika = ? AND id_produktu = ?");
        $stmt->bind_param("ii", $userId, $productId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $newQuantity = $row['ilosc'] + $quantity;

            $updateStmt = $conn->prepare("UPDATE koszyk SET ilosc = ?, data_dodania = NOW() WHERE id_uzytkownika = ? AND id_produktu = ?");
            $updateStmt->bind_param("iii", $newQuantity, $userId, $productId);
            $updateStmt->execute();
            $updateStmt->close();
            echo "Merged UPDATE for product ID $productId<br>";
        } else {
            $insertStmt = $conn->prepare("INSERT INTO koszyk (id_uzytkownika, id_produktu, ilosc, data_dodania) VALUES (?, ?, ?, NOW())");
            $insertStmt->bind_param("iii", $userId, $productId, $quantity);
            $insertStmt->execute();
            $insertStmt->close();
            echo "Merged INSERT for product ID $productId<br>";
        }

        $stmt->close();
    }

    $conn->close();
}

if (isset($_SESSION['user_id']) && isset($_SESSION['cart']) && !isset($_SESSION['cart_merged'])) {
    mergeCartToDatabase($_SESSION['user_id'], $_SESSION['cart']);
    $_SESSION['cart_merged'] = true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = intval($_POST['product_id'] ?? 0);
    $quantity = max(1, intval($_POST['quantity'] ?? 1));

    if ($productId > 0) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // dodaj do koszyka
        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId] += $quantity;
        } else {
            $_SESSION['cart'][$productId] = $quantity;
        }

        echo "Added to session cart.<br>";

        // jesli zalogowany!
        if (isset($_SESSION['user_id'])) {
            $conn = getDbConnection();

            $stmt = $conn->prepare("SELECT ilosc FROM koszyk WHERE id_uzytkownika = ? AND id_produktu = ?");
            $stmt->bind_param("ii", $_SESSION['user_id'], $productId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $newQuantity = $row['ilosc'] + $quantity;

                $updateStmt = $conn->prepare("UPDATE koszyk SET ilosc = ?, data_dodania = NOW() WHERE id_uzytkownika = ? AND id_produktu = ?");
                $updateStmt->bind_param("iii", $newQuantity, $_SESSION['user_id'], $productId);
                $updateStmt->execute();
                $updateStmt->close();
                echo "UPDATE successful.<br>";
            } else {
                $insertStmt = $conn->prepare("INSERT INTO koszyk (id_uzytkownika, id_produktu, ilosc, data_dodania) VALUES (?, ?, ?, NOW())");
                $insertStmt->bind_param("iii", $_SESSION['user_id'], $productId, $quantity);
                $insertStmt->execute();
                $insertStmt->close();
                echo "INSERT successful.<br>";
            }

            $stmt->close();
            $conn->close();
        }
    }
}


header("Location: koszyk.php");
exit;
