<?php
ini_set('auto_detect_line_endings', true);

require_once './data/classUtility.php';

use KnowYourBooks\Utility as UT;

/**
 * @var string $title Titolo pagina WEB
 */
$title = 'Know Your Books: Scrivi Recensione';

/**
 * @var array $css Link file(s) CSS
 */
$css = array('./scss/css/style.min.css', './scss/css/writeReviews.min.css');

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
    $userName = UT::request('userName');
    $bookName = UT::request('bookName');
    $authorName = UT::request('authorName');
    $valutation = UT::request('valutation');
    $reviewTitle = UT::request('reviewTitle');
    $review = UT::request('review');

    // Memoria del button valutazione selezionato
    $selezionato = null;

    // Definizione classe campo errato
    $clsErrore = 'class="error"';

    // Validazione userName
    if (($userName != '') && UT::ctrlLunghezzaStringa($userName, 5, 16)) {
        $clsErroreUser = '';
    } else {
        $userName = '';
        $clsErroreUser = $clsErrore;
        $validation++;
    }

    // Validazione bookName
    if (($bookName != '') && UT::ctrlLunghezzaStringa($bookName, 2, 64)) {
        $clsErroreBook = '';
    } else {
        $bookName = '';
        $clsErroreBook = $clsErrore;
        $validation++;
    }

    // Validazione authorName
    if (($authorName != '') && UT::ctrlLunghezzaStringa($authorName, 2, 64)) {
        $clsErroreAuthor = '';
    } else {
        $authorName = '';
        $clsErroreAuthor = $clsErrore;
        $validation++;
    }

    // Validazione reviewtitle
    if (($reviewTitle != '') && UT::ctrlLunghezzaStringa($reviewTitle, 5, 64)) {
        $clsErroreTitle = '';
    } else {
        $reviewTitle = '';
        $clsErroreTitle = $clsErrore;
        $validation++;
    }

    // Validazione review
    if (($review != '') && UT::ctrlLunghezzaStringa($review, 10, 300)) {
        $clsErroreReview = '';
    } else {
        $review = '';
        $clsErroreReview = $clsErrore;
        $validation++;
    }

    // Validazione valutation
    if ($valutation != null) {
        $selezionato = $valutation;
        $clsErroreValutation = '';
    } else {
        $clsErroreValutation = $clsErrore;
        $validation++;
    }

    $inviato = ($validation == 0) ? true : false;
} else {
    $userName = '';
    $bookName = '';
    $authorName = '';
    $valutation = null;
    $reviewTitle = '';
    $review = '';

    $clsErroreUser = '';
    $clsErroreBook = '';
    $clsErroreAuthor = '';
    $clsErroreValutation = '';
    $clsErroreTitle = '';
    $clsErroreReview = '';
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
                <h2>Scrivi la tua recensione</h2>
                <?php
                if (!$inviato) {
                ?>
                    <form action="?inviato=1" class="writeReviews" method="post">
                        <fieldset>
                            <input type="text" name="userName" id="userName" placeholder="Nome Utente" <?php echo $clsErroreUser; ?> value="<?php echo $userName ?>">
                            <input type="text" name="bookName" id="bookName" placeholder="Libro" <?php echo $clsErroreBook; ?> value="<?php echo $bookName ?>">
                            <input type="text" name="authorName" id="authorName" placeholder="Autore" <?php echo $clsErroreAuthor ?> value="<?php echo $authorName ?>">
                            <div class="valutation">
                                <?php
                                for ($i = 1; $i <= 5; $i++) {
                                    $cont = 5 - $i; // Contatore per stampare stelle vuote
                                    $stars = str_repeat("&#9733;", $i) . str_repeat("&#9734;", $cont);
                                    if (isset($selezionato) && ($i == $selezionato)) {
                                        echo sprintf('<div><input type="radio" name="valutation" id="value_%u" value="%u" checked>', $i, $i);
                                    } else {
                                        echo sprintf('<div><input type="radio" name="valutation" id="value_%u" value="%u">', $i, $i);
                                    }
                                    echo sprintf('<label for="value_%u" %s><span class="stars">%s</span></label></div>', $i, $clsErroreValutation, $stars);
                                }
                                ?>
                            </div>
                            <input type="text" name="reviewTitle" id="reviewTitle" placeholder="Titolo Recensione" <?php echo $clsErroreTitle; ?> value="<?php echo $reviewTitle; ?>">
                            <textarea name="review" id="review" placeholder="Scrivi qui la tua recensione" <?php echo $clsErroreReview; ?>><?php echo $review ?></textarea>
                            <div class="buttons">
                                <button type="submit" name="submit" id="submit">Invia</button>
                            </div>
                        </fieldset>
                    </form>
                <?php
                    // Stampa conferma inserimento recensione
                } else {
                    $reviewData = array(
                        "userName" => $userName,
                        "bookName" => $bookName,
                        "authorName" => $authorName,
                        "valutation" => $valutation,
                        "reviewTitle" => $reviewTitle,
                        "review" => $review
                    );

                    // Estrapolo array dal file JSON e aggiungo la recensione
                    $reviewsArray = (array)json_decode(file_get_contents($reviews), true);
                    array_push($reviewsArray, $reviewData);

                    // Riscrivo le recensioni nel file JSON
                    $inserito = file_put_contents($reviews, json_encode($reviewsArray, JSON_PRETTY_PRINT));

                    if ($inviato && $inserito != false) {
                        $confirm = '<h3>Recensione inserita con successo!</h3>';
                        $confirm .= UT::printReviewCard($reviewData);
                        $confirm .= '<a href="writeReviews.php" title="Ricarica">Torna alla pagina di inserimento recensioni.</a>';

                        echo $confirm;
                    } else {
                        $confirm = '<h2>Si è verificato un errore. Riprovare.</h2>';
                        $confirm .= '<p>Se il problema persiste, contattaci tramite la pagina <a href="index.php#contatti">Contatti</a>.</p>';
                        $confirm .= '<a href="writeReviews.php" title="Ricarica">Torna alla pagina di inserimento recensioni.</a>';

                        echo $confirm;
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