<?php
session_start();
$conn = new mysqli("localhost", "root", "newpassword", "pkstore");

if ($conn->connect_error) {
    die("Błąd połączenia z bazą: " . $conn->connect_error);
}

$productId = intval($_POST['product_id']);
$newQty = intval($_POST['quantity']);

$stmt = $conn->prepare("SELECT ilosc FROM produkty WHERE id = ?");
$stmt->bind_param("i", $productId);
$stmt->execute();
$stmt->bind_result($availableQty);
if (!$stmt->fetch()) {

    $stmt->close();
    $conn->close();
    die("Produkt nie znaleziony.");
}
$stmt->close();


if ($newQty > $availableQty) {
    $newQty = $availableQty;
}

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    if ($newQty <= 0) {
        $stmt = $conn->prepare("DELETE FROM koszyk WHERE id_uzytkownika = ? AND id_produktu = ?");
        $stmt->bind_param("ii", $userId, $productId);
        $stmt->execute();
        $stmt->close();
    } else {
        $stmt = $conn->prepare("UPDATE koszyk SET ilosc = ? WHERE id_uzytkownika = ? AND id_produktu = ?");
        $stmt->bind_param("iii", $newQty, $userId, $productId);
        $stmt->execute();
        $stmt->close();
    }
} else {
    if ($newQty <= 0) {
        unset($_SESSION['cart'][$productId]);
    } else {
        $_SESSION['cart'][$productId] = $newQty;
    }
}

$conn->close();
header("Location: koszyk.php");
exit;
