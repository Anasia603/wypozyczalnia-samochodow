<?php
    // funkcja dla wykonania zapytań
    function wykonaj_zapytanie(PDO $pdo, string $sql, array $argumenty = null) {
        $instrukcja = $pdo->prepare($sql);
        $instrukcja->execute($argumenty);
        return $instrukcja;
    }

    // funkcja formatująca tekst
    function zastap_html($tekst): string {
        $tekst = $tekst ?? '';
        return htmlspecialchars($tekst, ENT_QUOTES, 'UTF-8', false); // zwróć przetworzony tekst
    }
?>