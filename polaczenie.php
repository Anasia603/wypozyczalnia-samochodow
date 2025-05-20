<?php
$typ        = 'mysql';                 // Typ bazy danych
$serwer     = 'localhost';             // Nazwa hosta
$db         = 'srv84416_wypozyczalnia';            // Nazwa bazy
$port       = '';                      // Typowe numery portu to 8889 w MAMP i 3306 w XAMPP
$kodowanie  = 'utf8mb4';               // 4-bajtowe kodowanie UTF-8

$uzytkownik = 'srv84416_anasia';                  // Wpisz SWOJĄ nazwe konta
$haslo      = '44994311';                      // Wpisz SWOJE hasło

$opcje      = [                        // Opcje PDO; dane zbierane w tablicę asocjacyjną
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];                                                                   // Konfiguracja PDO

// NIE ZMIENIAJ NICZEGO PONIŻEJ TEGO WIERSZA
$dsn = "$typ:host=$serwer;dbname=$db;port=$port;charset=$kodowanie"; // Tworzenie DSN
try {                                                                // Próba wykonania kodu
    $pdo = new PDO($dsn, $uzytkownik, $haslo, $opcje);               // Tworzenie obiektu PDO
} catch (PDOException $e) {                                          // W razie wyjatku
    throw new PDOException($e->getMessage(), $e->getCode());         // Wyrzuć go ponownie
}