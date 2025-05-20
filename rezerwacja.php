<?php declare(strict_types = 1); 
    require "polaczenie.php";
    require "funkcje.php";

    // Wyświetlenie listy
    $sql = "SELECT id, marka, model, rok_produkcji FROM pojazdy WHERE dostepnosc = 1 AND typ = :typ;";
    
    $samochod_osob = wykonaj_zapytanie($pdo, $sql, ['typ' => 'osobowy'])->fetchAll();
    $samochod_dost = wykonaj_zapytanie($pdo, $sql, ['typ' => 'dostawczy'])->fetchAll();
    $samochod_skut = wykonaj_zapytanie($pdo, $sql, ['typ' => 'skuter'])->fetchAll();
    // Wyświetlenie listy koniec
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rezerwacja</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <!-- nawigacja -->
    <nav>
        <ul>
            <li><a href="index.php">start</a></li>
            <li><a href="#">rezerwacja</a></li>
            <li><a href="zwrot.php">zwrot</a></li>
        </ul>
    </nav>
    
    <!-- formularz rezerwacji -->
    <form class="form-rezerwacja" action="rezerwacja.php" method="post">
        <h2>Wybierz samochód</h2>

        <select name="auto-select" id="">
            <option value="" disabled selected hidden>--</option>
            <optgroup label="osobowe">
                <?php 
                if (!empty($samochod_osob)) {
                    foreach($samochod_osob as $samochod) { ?>
                    <option value="<?= zastap_html($samochod['id']) ?>"><?= zastap_html($samochod['marka']) . ' ' . zastap_html($samochod['model']) . ' ' . zastap_html($samochod['rok_produkcji']) ?></option>
                <?php } } else { ?>
                    <option disabled value="">Brak dostępnych pojazdów</option>
                <?php } ?>
            </optgroup>

            <optgroup label="dostawcze">
                <?php 
                if (!empty($samochod_dost)) {
                    foreach($samochod_dost as $samochod) { ?>
                    <option value="<?= zastap_html($samochod['id']) ?>"><?= zastap_html($samochod['marka']) . ' ' . zastap_html($samochod['model']) . ' ' . zastap_html($samochod['rok_produkcji']) ?></option>
                <?php } } else { ?>
                    <option disabled value="">Brak dostępnych pojazdów</option>
                <?php } ?>
            </optgroup>

            <optgroup label="skutery">
                <?php 
                if (!empty($samochod_skut)) {
                    foreach($samochod_skut as $samochod) { ?>
                    <option value="<?= zastap_html($samochod['id']) ?>"><?= zastap_html($samochod['marka']) . ' ' . zastap_html($samochod['model']) . ' ' . zastap_html($samochod['rok_produkcji']) ?></option>
                <?php } } else { ?>
                    <option disabled value="">Brak dostępnych pojazdów</option>
                <?php } ?>
            </optgroup>
        </select>

        <h2>Wpisz swoje dane</h2>

        <div class="dane-klienta">
            <label for="imie">Imię: </label>
            <input type="text" name="imie" id="imie" value="<?= zastap_html($_POST['imie'] ?? '') // jeśli wystąpił błąd podczas wprowadzania danych, to zostawi wcześniej wprowadzone dane ?>">
        </div>

        <div class="dane-klienta">
            <label for="nazw">Nazwisko: </label>
            <input type="text" name="nazwisko" id="nazw" value="<?= zastap_html($_POST['nazwisko'] ?? '') ?>">
        </div>

        <div class="dane-klienta">
            <label for="mail">Email: </label>
            <input type="email" name="email" id="mail" value="<?= zastap_html($_POST['email'] ?? '') ?>">
        </div>

        <div class="dane-klienta">
            <label for="tel">Telefon: </label>
            <input type="number" name="telefon" id="tel" value="<?= zastap_html($_POST['telefon'] ?? '') ?>">
        </div>

        <h2>Wybierz datę wypożyczenia i zwrotu</h2>

        <div class="dane-klienta">
            <label for="wyp">Data wypożyczenia: </label>
            <input type="date" name="data-wypozycz" id="wyp" value="<?= zastap_html($_POST['data-wypozycz'] ?? '') ?>">
        </div>

        <div class="dane-klienta">
            <label for="zwr">Data zwrotu: </label>
            <input type="date" name="data-zwrot" id="zwr" value="<?= zastap_html($_POST['data-zwrot'] ?? '') ?>">
        </div>

        <input type="submit" value="Rezerwuj">
    </form>
    <!-- formularz rezerwacji koniec -->


<?php
    // Obsługa formularza
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $wybrane_auto = $_POST["auto-select"];
        $imie = $_POST["imie"]; // pobieranie danych o klincie z formularza
        $nazwisko = $_POST["nazwisko"];
        $email = $_POST["email"];
        $telefon = $_POST["telefon"];
        $data_wypozycz = $_POST["data-wypozycz"];
        $data_zwrot = $_POST["data-zwrot"];

        // walidacja dat
        $dzis = new DateTime();
        $dzis->setTime(0, 0); // żeby porównywały się tylko daty, bez godzin
        $data_wypozyczenia = new DateTime($data_wypozycz);
        $data_zwrotu = new DateTime($data_zwrot);
        
        if (empty($wybrane_auto) || empty($imie) || empty($nazwisko) || empty($email) || empty($telefon) || empty($data_wypozycz) || empty($data_zwrot)) { // jeśli fromularz nie był uzupełniony
            echo '<h3>Uzupełnij wszystkie dane!</h3>';
        }
        elseif ($data_wypozyczenia < $dzis) { // jeśli data wypożyczenia jest wcześniej niż dziś
            echo '<h3>Wybierz poprawną datę wypożyczenia!</h3>';
        }
        elseif ($data_zwrotu < $data_wypozyczenia) {
            echo '<h3>Wybierz poprawną datę zwrotu!</h3>';
        }
        else {

            $sql_tel = "SELECT id FROM klienci WHERE telefon = :tel LIMIT 1;";
            $tel_wprowadzony = wykonaj_zapytanie($pdo, $sql_tel, ['tel' => $telefon])->fetch();
            
            if (!empty($tel_wprowadzony)) { // jeśli id klienta z wprowadzonym telefonem już istnieje
                $id_klienta = $tel_wprowadzony['id']; // jeśli klient istneije, to jedo id to właśnie pobrane
            }
            else {
                // dodanie danych do tabeli klient
                $sql = "INSERT INTO klienci(imie, nazwisko, email, telefon) VALUES(:imie, :nazwisko, :email, :telefon);";
                $parametry = ['imie' => $imie, 'nazwisko' => $nazwisko, 'email' => $email, 'telefon' => $telefon];
                wykonaj_zapytanie($pdo, $sql, $parametry);
                $id_klienta = $pdo->lastInsertId(); // pobranie ostatniego id z bazy (czyli to które było dopiero co wprowadzone)
            }
            
            $sql_wypoz = "SELECT id FROM wypozyczenia WHERE id_klienta = :id_klienta LIMIT 1;";
            $wypozycz_id = wykonaj_zapytanie($pdo, $sql_wypoz, ['id_klienta' => $id_klienta])->fetch();

            if (!empty($wypozycz_id)) { // jeśli klient już ma wypożyczenie
                echo "<h2>Już masz jedno wypożyczenie! Id: " . zastap_html($wypozycz_id['id']) . "</h2>";
                exit;
            } else {

                // dodanie danych do tabeli wypozyczenia
                $sql2 = "INSERT INTO wypozyczenia(id_pojazdu, id_klienta, data_wypozyczenia, data_zwrotu, rzeczywista_data_zwrotu, status) VALUES(:id_pojazdu, :id_klienta, :data_wypozyczenia, :data_zwrotu, NULL, 'wypożyczony');"; // dodanie wypożyczenia

                $sql3 = "UPDATE pojazdy SET dostepnosc = 0 WHERE id = :id;"; // zmiana dostępności wypożyczonego samochodu

                
                $parametry2 = ['id_pojazdu' => $wybrane_auto, 'id_klienta' => $id_klienta, 'data_wypozyczenia' => $data_wypozycz, 'data_zwrotu' => $data_zwrot];

                wykonaj_zapytanie($pdo, $sql2, $parametry2);
                $id_wypozyczenia = $pdo->lastInsertId(); // pobranie ostatniego id z bazy (od razy z tabeli wypozyczenia)

                wykonaj_zapytanie($pdo, $sql3, ['id' => $wybrane_auto]);

                $sql4 = "SELECT marka, model, rok_produkcji FROM pojazdy WHERE id = :id;";
                $auto = wykonaj_zapytanie($pdo, $sql4, ['id' => $wybrane_auto])->fetch(); // pobranie nazwy wypożyczonego auta
            }

?>
    <div class="wypozyczenie">
        <h2>Rezerwacja się powiodła!</h2>
        <ul>
            <li>Imię: <?= zastap_html($imie) ?></li>
            <li>Nazwisko: <?= zastap_html($nazwisko) ?></li>
            <li>Wypożyczony pojazd: <?= zastap_html($auto['marka']) . ' ' . zastap_html($auto['model']) . ' ' . zastap_html($auto['rok_produkcji']) ?></li>
            <li>Data wypożyczenia: <?= zastap_html($data_wypozycz) ?></li>
            <li>Planowana data zwrotu: <?= zastap_html($data_zwrot) ?></li>
            <li>Id wypożyczenia (WAŻNE): <?= zastap_html($id_wypozyczenia) ?></li>
        </ul>
    </div>
<?php
        }
    }
    // Obsługa formularza koniec
?>

</body>
</html>