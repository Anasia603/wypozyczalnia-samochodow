<?php declare(strict_types = 1); 
    require "polaczenie.php";
    require "funkcje.php";
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zwrot</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <!-- nawigacja -->
    <nav>
        <ul>
            <li><a href="index.php">start</a></li>
            <li><a href="rezerwacja.php">rezerwacja</a></li>
            <li><a href="#">zwrot</a></li>
        </ul>
    </nav>
    
    <!-- formularz zwrotu -->
    <form class="form-zwrot" action="zwrot.php" method="post">
        <h2>Wpisz swój telefon</h2>
        <input type="number" name="tel" size="5">

        <input type="submit" value="Zwróć">
    </form>
    <!-- formularz zwrotu koniec -->

    <?php
        // Obsługa formularza
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $tel = $_POST['tel'];
            if (empty($tel)) {
                echo "<h3>Wpisz telefon!</h3>";
                exit;
            }

            $sql_check = "SELECT wypozyczenia.id AS id FROM wypozyczenia JOIN klienci ON wypozyczenia.id_klienta = klienci.id WHERE wypozyczenia.status = 'wypożyczony' AND klienci.telefon = :tel;";
            $id_wypozycz = wykonaj_zapytanie($pdo, $sql_check, ['tel' => $tel])->fetch(); // pobranie id wypożyczenia
            
            if (!empty($id_wypozycz)) {
                
                $id_wyp = $id_wypozycz['id'];
                $sql = "UPDATE wypozyczenia SET status = 'zwrócony' WHERE id = :id;";
                wykonaj_zapytanie($pdo, $sql, ['id' => $id_wyp]);

                $sql2 = "SELECT id_pojazdu FROM wypozyczenia WHERE id = :id_wyp"; 
                $pojazd_id = wykonaj_zapytanie($pdo, $sql2, ['id_wyp' => $id_wyp])->fetch(); // pobieranie id wypożyczonego pojazdu

                $sql3 = "UPDATE pojazdy SET dostepnosc = 1 WHERE id = :id_poj;";
                wykonaj_zapytanie($pdo, $sql3, ['id_poj' => $pojazd_id['id_pojazdu']]);

                echo "<h2>Zwrot się powiódł!</h2>";
            }
            else {
                echo "<h2>Nie masz żadnego wypożyczenia.</h2>";
            }

            
        }
    ?>
</body>
</html>