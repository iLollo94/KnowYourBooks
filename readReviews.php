<?php
ini_set('auto_detect_line_endings', true);

require_once './data/classUtility.php';

use KnowYourBooks\Utility as UT;

/**
 * @var string $title Titolo pagina WEB
 */
$title = 'Know Your Books: Leggi Recensioni';

/**
 * @var array $css Link file(s) CSS
 */
$css = array('./scss/css/style.min.css', './scss/css/readReviews.min.css');

/**
 * @var string $reviews Link al file contenente le recensioni
 */
$reviews = './data/reviews.json';

// Controllo invio modulo e validazione dati
$inviato = UT::request('inviato');
$inviato = ($inviato == null) ? false : true;
$validation = 0;

// Validazione campi
if ($inviato && isset($_POST)) {
    $bookName = UT::request('bookName');
    $authorName = UT::request('authorName');

    // Definizione classe campo errato
    $clsErrore = 'class="error"';

    // Validazione bookName
    if (UT::ctrlLunghezzaStringa($bookName, 0, 64)) {
        $clsErroreBook = '';
    } else {
        $bookName = '';
        $clsErroreBook = $clsErrore;
        $validation++;
    }

    // Validazione authorName
    if (UT::ctrlLunghezzaStringa($authorName, 0, 64)) {
        $clsErroreAuthor = '';
    } else {
        $authorName = '';
        $clsErroreAuthor = $clsErrore;
        $validation++;
    }

    $inviato = ($validation > 1) ? false : true;
} else {
    $bookName = '';
    $authorName = '';

    $clsErroreBook = '';
    $clsErroreAuthor = '';
}
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <?php
    echo UT::generateHead($title, $css);
    ?>
</head>

<body>
    <div class="page-container">
        <header>
            <?php
            echo UT::generateHeader();
            ?>
        </header>

        <nav>
            <?php
            echo UT::generateMenu(true);
            ?>
        </nav>

        <main>
            <div class="container">
                <h2>Ricerca recensioni:</h2>
                <?php
                if (!$inviato) {
                ?>
                    <form action="?inviato=1" class="searchReviews" method="get">
                        <fieldset>
                            <input type="text" name="bookName" id="bookName" placeholder="Libro" <?php echo $clsErroreBook; ?> value="<?php echo $bookName ?>">
                            <input type="text" name="authorName" id="authorName" placeholder="Autore" <?php echo $clsErroreAuthor ?> value="<?php echo $authorName ?>">
                            <div class="buttons">
                                <button type="submit" name="inviato" id="submit" value="1">Invia</button>
                            </div>
                        </fieldset>
                    </form>
                    <?php
                    // Stampa di tutte le recensioni
                    // Estrapolo array dal file JSON e aggiungo la recensione
                    $reviewsArray = (array)json_decode(file_get_contents($reviews), true);
                    
                    if (count($reviewsArray) > 10) {
                        for ($i = 0; $i < 10; $i++) {
                            echo UT::printReviewCard($reviewsArray[$i]);
                        }
                    } else {
                        foreach ($reviewsArray as $review) {
                            echo UT::printReviewCard($review);
                        }
                    }
                } else {
                    ?>
                    <form action="?inviato=1" class="searchReviews" method="get">
                        <fieldset>
                            <input type="text" name="bookName" id="bookName" placeholder="Libro" <?php echo $clsErroreBook; ?> value="<?php echo $bookName ?>">
                            <input type="text" name="authorName" id="authorName" placeholder="Autore" <?php echo $clsErroreAuthor ?> value="<?php echo $authorName ?>">
                            <div class="buttons">
                                <button type="submit" name="submit" id="submit" value="1">Invia</button>
                            </div>
                        </fieldset>
                    </form>
                <?php
                    // Ricerca
                    // Estrapolo array dal file JSON e aggiungo la recensione
                    $reviewsArray = (array)json_decode(file_get_contents($reviews), true);

                    $found = 0; // Numero di risultati trovati
                    $searchStr = '';
                    foreach ($reviewsArray as $review) {
                        // Ricerca con entrambi i campi
                        if (($bookName != '') && ($authorName != '')) {
                            if (is_int(stripos($review['bookName'], $bookName)) && is_int(stripos($review['authorName'], $authorName))) {
                                $found++;
                                $searchStr .= UT::printReviewCard($review);
                            }
                            // Ricerca solo tramite nome libro
                        } elseif (($bookName != '') && ($authorName == '')) {
                            if (is_int(stripos($review['bookName'], $bookName))) {
                                $found++;
                                $searchStr .= UT::printReviewCard($review);
                            }
                            // Ricerca solo tramite nome autore
                        } elseif (($bookName == '') && ($authorName != '')) {
                            if (is_int(stripos($review['authorName'], $authorName))) {
                                $found++;
                                $searchStr .= UT::printReviewCard($review);
                            }
                        }
                    }

                    // Se sono stati trovati risultati, li stampa
                    if ($found == 0) {
                        echo '<h3>Nessun risultato trovato</h3>';

                        if (count($reviewsArray) > 10) {
                            for ($i = 0; $i < 10; $i++) {
                                echo UT::printReviewCard($reviewsArray[$i]);
                            }
                        } else {
                            foreach ($reviewsArray as $review) {
                                echo UT::printReviewCard($review);
                            }
                        }
                    } else {
                        echo $searchStr;
                    }
                }
                ?>
            </div>
        </main>

        <footer>
            <?php
            echo UT::generateFooter();
            ?>
        </footer>
    </div>
</body>

</html>