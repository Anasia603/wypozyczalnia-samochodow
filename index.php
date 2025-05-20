<?php declare(strict_types = 1); 
    require "polaczenie.php";
    require "funkcje.php";

    // Wyświetlenie listy
    $sql = "SELECT marka, model FROM pojazdy WHERE dostepnosc = 1 AND typ = :typ;";
    $samochod_osob = wykonaj_zapytanie($pdo, $sql, ['typ' => 'osobowy'])->fetchAll();
    $samochod_dost = wykonaj_zapytanie($pdo, $sql, ['typ' => 'dostawczy'])->fetchAll();
    $samochod_skut = wykonaj_zapytanie($pdo, $sql, ['typ' => 'skuter'])->fetchAll();
    // Wyświetlenie listy koniec


    // wyszukiwanie wg kryterium
    $szukany_typ = $_GET["typ-select"] ?? ''; // jeśli nie podano w formularzu, to pusty string
    $szukany_rok = $_GET["rok-select"] ?? '';
    $szukana_cena = $_GET["cena-select"] ?? '';

    $parametry = []; // najpierw parametry mają być zainicjalizowane, by nie było błędów
    $warunki = ['dostepnosc = 1']; // tablica z warunkami, która uzupełnia się kolejnymi

    if ($szukany_typ == '' && $szukany_rok == '' && $szukana_cena == '') { // jeśli szukane typ, rok i cena są puste, wyszukaj wszystkie dostępne samochody
        $sql_form = "SELECT marka, model, rok_produkcji, cena_za_dzien, zdjecie FROM pojazdy WHERE dostepnosc = 1;";
        $wszystkie_samochody = wykonaj_zapytanie($pdo, $sql_form)->fetchAll();

    } else { // jeśli odnaleziono coś

        if ($szukany_typ !== '') { // jeśli uzyskano typ
            $warunki[] = 'typ = :typ'; // dodawanie warunku
            $parametry['typ'] = $szukany_typ; // parametry jest tablicą indeksowaną z argumentami i wartościami do nich
        }

        if ($szukany_rok !== '') { // jeśli uzyskano rok
            [$rok_od, $rok_do] = explode('-', $szukany_rok); // ta funkcja rozdziela string i zwraca tablicę

            $warunki[] = 'rok_produkcji BETWEEN :rok_od AND :rok_do';
            $parametry['rok_od'] = $rok_od;
            $parametry['rok_do'] = $rok_do;
        }

        if ($szukana_cena !== '') {
            $operator = preg_match('/^</', $szukana_cena) ? '<' : '>='; // czy szukana cena jest mniejsza? jeśli tak, to mniejsza, jeśli nie, to większa lub równa, bo innej opcji nie ma
            $cena_liczba = str_replace(['<', '>='], '', $szukana_cena); // funkcja str_replace zamienia operatory na puste znaki, by została tylko liczba
            $warunki[] = "cena_za_dzien $operator :cena";
            $parametry['cena'] = $cena_liczba;
        }

        $sql_form = "SELECT marka, model, rok_produkcji, cena_za_dzien, zdjecie FROM pojazdy WHERE " . implode(' AND ', $warunki) . ";"; // zapytanie jest przerywane i dodają się do niego warunki, które są pobierane z tablicy z złączane operatorem AND za pomocą funkcji implode

        $wszystkie_samochody = wykonaj_zapytanie($pdo, $sql_form, $parametry)->fetchAll();
    }
    // wyszukiwanie wg kryterium koniec

?> 


 <!DOCTYPE html>
 <html lang="pl">
 <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wypożyczalnia</title>
    <link rel="stylesheet" href="style.css">
 </head>

 <body>
    <!-- nawigacja -->
    <nav>
        <ul>
            <li><a href="#">start</a></li>
            <li><a href="rezerwacja.php">rezerwacja</a></li>
            <li><a href="zwrot.php">zwrot</a></li>
        </ul>
    </nav>

    <main>
        <!-- Lista pojazdów -->
        <section class="lista-pojazdow">
            <div class="pojazd">
                <h2>Osobowe</h2>
                <ul>
                    <?php 
                    if (!empty($samochod_osob)) { // jeśli zapytanie nie zwróciło pustą tablicę
                        foreach($samochod_osob as $samochod) { ?>
                        <li>
                            <?= zastap_html($samochod['marka']) . ' ' . zastap_html($samochod['model']) ?>
                        </li>
                    <?php } } else { ?>
                        <li>Brak dostępnych pojazdów</li>
                    <?php } ?>
                </ul>
            </div>

            <div class="pojazd">
                <h2>Dostawcze</h2>
                <ul>
                    <?php 
                    if (!empty($samochod_dost)) {
                        foreach($samochod_dost as $samochod) { ?>
                        <li>
                            <?= zastap_html($samochod['marka']) . ' ' . zastap_html($samochod['model']) ?>
                        </li>
                    <?php } } else { ?>
                        <li>Brak dostępnych pojazdów</li>
                    <?php } ?>
                </ul>
            </div>

            <div class="pojazd">
                <h2>Skutery</h2>
                <ul>
                    <?php 
                    if (!empty($samochod_skut)) {
                        foreach($samochod_skut as $samochod) { ?>
                        <li>
                            <?= zastap_html($samochod['marka']) . ' ' . zastap_html($samochod['model']) ?>
                        </li>
                    <?php } } else { ?>
                        <li>Brak dostępnych pojazdów</li>
                    <?php } ?>
                </ul>
            </div>
        </section>
        <!-- Lista pojazdów koniec -->


        
        <section class="samoch-container">
        <!-- formularz wyszukiwania -->
         <form class="form-index" action="index.php" method="get">
            <h2>Filtruj</h2>
            <div class="select">
                <label for="">Typ:</label>
                <select name="typ-select" id="typ-select">
                    <option name="" value="">Wszystkie</option>
                    <option value="osobowy" <?= $szukany_typ == 'osobowy' ? 'selected' : '' // jeśli wyszukiwany typ jest 'osobowy', to option bezie zaznaczone (więc wyświetla sie na stronie)?>>Osobowe</option>
                    <option value="dostawczy" <?= $szukany_typ == 'dostawczy' ? 'selected' : '' ?>>Dostawcze</option>
                    <option value="skuter" <?= $szukany_typ == 'skuter' ? 'selected' : '' ?>>Skutery</option>
                </select>
            </div>

            <div class="select">
                <label for="">Rok produkcji</label>
                <select name="rok-select" id="">
                    <option value="">Wszystkie</option>
                    <option value="2018-2020" <?= $szukany_rok == '2018-2020' ? 'selected' : '' ?>>2018-2020</option>
                    <option value="2021-2022" <?= $szukany_rok == '2021-2022' ? 'selected' : '' ?>>2021-2022</option>
                </select>
            </div>

            <div class="select">
                <label for="">Cena</label>
                <select name="cena-select" id="">
                    <option value="">Wszystkie</option>
                    <option value="<100.00" <?= $szukana_cena == '<100.00' ? 'selected' : '' ?>>Mniej niż 100zł</option>
                    <option value="<200.00" <?= $szukana_cena == '<200.00' ? 'selected' : '' ?>>Mniej niz 200zł</option>
                    <option value=">=200.00" <?= $szukana_cena == '>=200.00' ? 'selected' : '' ?>>200zł i więcej</option>
                </select>
            </div>

            <input type="submit" value="Szukaj">
         </form>
         <!-- formularz wyszukiwania koniec -->



        <!-- opis samochodów -->
            <div class="karta-samoch">
                <?php if (!empty($wszystkie_samochody)) { // jeśli wyszukiwanie zwróciło jakiś wynik
                    foreach($wszystkie_samochody as $samochod) { ?>
                    <img src="img/<?= zastap_html($samochod['zdjecie']) ?>">
                    <h3><?= zastap_html($samochod['marka']) . ' ' . zastap_html($samochod['model']) ?></h3>
                    <p>Rok produkcji: <?= zastap_html($samochod['rok_produkcji']) ?></p>
                    <p>Cena za dzień: <?= zastap_html($samochod['cena_za_dzien']) ?></p>
                    <a href="rezerwacja.php">Rezerwuj</a>
                <?php } 
                } else { ?>
                    <h2>Brak wyników spełniających kryteria.</h2>
                <?php } ?>
            </div>
        </section>
        <!-- opis samochodów koniec -->
    </main>
 </body>

 </html>