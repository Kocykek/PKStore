


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
    $quantityToAdd = max(1, intval($_POST['quantity'] ?? 1));

    if ($productId > 0) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        $conn = getDbConnection();

        // Pobierz ilość dostępnych sztuk produktu z tabeli produkty
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

        // Obecna ilość w sesji
        $currentSessionQty = $_SESSION['cart'][$productId] ?? 0;

        // Obecna ilość w bazie (jeśli zalogowany)
        $currentDbQty = 0;
        if (isset($_SESSION['user_id'])) {
            $userId = $_SESSION['user_id'];
            $stmt = $conn->prepare("SELECT ilosc FROM koszyk WHERE id_uzytkownika = ? AND id_produktu = ?");
            $stmt->bind_param("ii", $userId, $productId);
            $stmt->execute();
            $stmt->bind_result($currentDbQty);
            $stmt->fetch();
            $stmt->close();
        }

        // Oblicz nową łączną ilość (sesja + baza + dodawana)
        $totalQty = $currentSessionQty + $currentDbQty + $quantityToAdd;

        // Ogranicz ilość do dostępnej w magazynie
        if ($totalQty > $availableQty) {
            $totalQty = $availableQty;
        }

        // Oblicz ile dodać do sesji (bo baza zawiera już $currentDbQty)
        $quantityToStoreInSession = $totalQty - $currentDbQty;
        if ($quantityToStoreInSession < 0) {
            $quantityToStoreInSession = 0;
        }

        // Zaktualizuj sesję
        $_SESSION['cart'][$productId] = $quantityToStoreInSession;

        echo "Added to session cart.<br>";

        // Aktualizuj bazę jeśli zalogowany
        if (isset($_SESSION['user_id'])) {
            if ($totalQty == 0) {
                // Usuń z bazy jeśli ilość 0
                $delStmt = $conn->prepare("DELETE FROM koszyk WHERE id_uzytkownika = ? AND id_produktu = ?");
                $delStmt->bind_param("ii", $userId, $productId);
                $delStmt->execute();
                $delStmt->close();
                echo "Removed product from database (quantity 0).<br>";
            } else {
                // Sprawdź czy jest wpis, zaktualizuj lub dodaj
                $stmt = $conn->prepare("SELECT ilosc FROM koszyk WHERE id_uzytkownika = ? AND id_produktu = ?");
                $stmt->bind_param("ii", $userId, $productId);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows > 0) {
                    $stmt->close();
                    $updateStmt = $conn->prepare("UPDATE koszyk SET ilosc = ?, data_dodania = NOW() WHERE id_uzytkownika = ? AND id_produktu = ?");
                    $updateStmt->bind_param("iii", $totalQty, $userId, $productId);
                    $updateStmt->execute();
                    $updateStmt->close();
                    echo "UPDATE successful.<br>";
                } else {
                    $stmt->close();
                    $insertStmt = $conn->prepare("INSERT INTO koszyk (id_uzytkownika, id_produktu, ilosc, data_dodania) VALUES (?, ?, ?, NOW())");
                    $insertStmt->bind_param("iii", $userId, $productId, $totalQty);
                    $insertStmt->execute();
                    $insertStmt->close();
                    echo "INSERT successful.<br>";
                }
            }
        }

        $conn->close();
    }
}


header("Location: koszyk.php");
exit;
