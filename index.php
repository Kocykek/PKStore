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
                    echo "<div class='box'><div id='boxImage'><img src='images/low_".htmlspecialchars($row['zdjecie']).".jpg' width='100' height='100'></div><div id='boxText'>".htmlspecialchars($row['nazwa'])."</div></div>";
                }
            }
            $conn->close();
            ?>
        </div>
    </div>
    <div id="bannerContainer">
        <div id="leftBlok"></div>
    <img src="images/banner.jpg">
            <div id="rightBlok"></div>
    </div>
    <div id="recommendedOffers">
        Wybrane dla ciebie:
    </div>


</body>
</html>