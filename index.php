<?php
ini_set('auto_detect_line_endings', true);

require_once './data/classUtility.php';

use KnowYourBooks\Utility as UT;

/**
 * @var string $title Titolo pagina WEB
 */
$title = 'Know Your Books';

/**
 * @var array $css Link file(s) CSS
 */
$css = array('./scss/css/style.min.css', './scss/css/homepage.min.css');

/**
 * @var string $bioFile Link file txt biografia
 */
$bioFile = './data/aboutUs.txt';

/**
 * @var string $bioFile Link file txt messaggio da affiancare a form
 */
$messageFile = './data/form-message.txt';

/**
 * @var string $contactFile Link file txt contatti
 */
$contactFile = './data/receivedContacts.txt';

// Validazione FORM
$inviato = UT::request('inviato');
$inviato = ($inviato == null) ? false : true;
$valido = 0;

if ($inviato) {
    // Richiamo delle variabili da array $_POST
    $fullName = UT::request('fullName');
    $email = UT::request('email');
    $argument = UT::request('argument');
    $messageTitle = UT::request('title');
    $message = UT::request('message');
    $privacy = (UT::request('privacy') == "on") ? true : false;
    // Definizione di classe campo errato
    $clsErrore = 'class="error"';

    // Validazione dei campi
    // Validazione NOME
    if (($fullName != "") && (strlen($fullName) <= 25)) {
        $clsErroreNome = "";
    } else {
        $valido++;
        $clsErroreNome = $clsErrore;
        $fullName = "";
    }

    // Validazione EMAIL
    if (($email != "") && UT::ctrlLunghezzaStringa($email, 10, 100) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $clsErroreEmail = "";
    } else {
        $valido++;
        $clsErroreEmail = $clsErrore;
        $email = "";
    }

    // Validazione ARGOMENTO
    if ($argument != "") {
        $clsErroreArgomento = "";
    } else {
        $valido++;
        $clsErroreArgomento = $clsErrore;
        $argument = "";
    }

    // Validazione TITOLO
    if (($messageTitle != "") && UT::ctrlLunghezzaStringa($messageTitle, 4, 100)) {
        $clsErroreTitolo = "";
    } else {
        $valido++;
        $clsErroreTitolo = $clsErrore;
        $messageTitle = "";
    }

    // Validazione MESSAGGIO
    if (($message != "") && UT::ctrlLunghezzaStringa($message, 10, 500)) {
        $clsErroreMessaggio = "";
    } else {
        $valido++;
        $clsErroreMessaggio = $clsErrore;
        $message = "";
    }

    // Validazione CHECKBOX PRIVACY
    if ($privacy) {
        $clsErrorePrivacy = "";
    } else {
        $valido++;
        $clsErrorePrivacy = $clsErrore;
    }

    $inviato = ($valido == 0) ? true : false;
} else {
    $fullName = "";
    $email = "";
    $argument = "";
    $messageTitle = "";
    $message = "";
    $privacy = "";

    $clsErroreNome = "";
    $clsErroreEmail = "";
    $clsErroreArgomento = "";
    $clsErroreTitolo = "";
    $clsErroreMessaggio = "";
    $clsErrorePrivacy = "";
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
            <section class="presentation">
                <blockquote cite="Irving Stone">Non ci sono amicizie più rapide di quelle tra persone che amano gli stessi libri.</blockquote>
                <p>Irving Stone</p>
            </section>

            <section class="bio" id="aboutUs">
                <div class="bio-card">
                    <h2>Chi Siamo</h2>
                    <p>
                        <?php
                        $bio = nl2br(file_get_contents($bioFile));
                        echo $bio;
                        ?>
                    </p>
                </div>
            </section>

            <section class="contacts" id="contatti">
                <h2>Contattaci!</h2>
                <div class="form-container">
                <?php
                if (!$inviato) {
                ?>
                    <form action="?inviato=1" class="contatti" method="post">
                        <fieldset>
                            <label for="fullName">Nome Completo</label>
                            <input type="text" name="fullName" id="fullName" <?php echo $clsErroreNome; ?> value="<?php echo $fullName; ?>">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" <?php echo $clsErroreEmail; ?> value="<?php echo $email; ?>">
                            <label for="argument">Argomento</label>
                            <select name="argument" id="argument" <?php echo $clsErroreArgomento; ?>>
                                <option value="" <?php echo ($argument == '') ? 'selected' : ''; ?> disabled>-- Seleziona un argomento --</option>
                                <option value="1" <?php echo ($argument == 1) ? 'selected' : ''; ?>>Informazioni</option>
                                <option value="2" <?php echo ($argument == 2) ? 'selected' : ''; ?>>Eliminazione recensioni</option>
                                <option value="3" <?php echo ($argument == 3) ? 'selected' : ''; ?>>Collaborazioni</option>
                                <option value="4" <?php echo ($argument == 4) ? 'selected' : ''; ?>>Altro</option>
                            </select>
                            <label for="title">Oggetto</label>
                            <input type="text" name="title" id="title" <?php echo $clsErroreTitolo; ?> value="<?php echo $messageTitle; ?>">
                            <label for="message">Inserisci messaggio</label>
                            <textarea name="message" id="message" <?php echo $clsErroreMessaggio; ?>><?php echo $message; ?></textarea>
                            <div id="privacy-box">
                                <input type="checkbox" name="privacy" id="privacy">
                                <label for="privacy">Ho letto ed accetto l'<a href="#" title="Privacy" <?php echo $clsErrorePrivacy; ?>>Informativa Privacy</a></label>
                            </div>

                            <div class="buttons">
                                <button type="submit" name="submit" id="submit">Invia messaggio</button>
                            </div>

                            <?php echo ($valido != 0) ? '<p style="color:red;">Compilare correttamente tutti i campi' : '' ?>
                        </fieldset>
                    </form>

                <?php
                } else {
                    if ($argument == 1) {
                        $argument = "Informazioni";
                    } elseif ($argument == 2) {
                        $argument = "Eliminazione recensioni";
                    } elseif ($argument == 3) {
                        $argument = "Collaborazioni";
                    } else {
                        $argument = "Altro";
                    }

                    $str = "<strong>Nome:</strong> %s<br>" .
                        "<strong>Email:</strong> %s<br>" .
                        "<strong>Argomento:</strong> %s<br>" .
                        "<strong>Titolo:</strong> %s<br>" .
                        "<strong>Messaggio:</strong> %s<br>";

                    $str = sprintf($str, $fullName, $email, $argument, $messageTitle, $message);

                    echo ("<div class=\"result-message\"><h2>Grazie per averci contattato</h2> Ecco il riepilogo del tuo messaggio: <br>" . $str);

                    $str = str_replace("<br>", chr(10), $str);
                    $str = str_replace("<strong>", "", $str);
                    $str = str_replace("</strong>", "", $str);

                    $str = str_repeat("-", 30) . chr(10) . $str . str_repeat("-", 30);

                    $rit = UT::fileInsert($contactFile, $str);

                    if ($rit) {
                        echo "<br>" . str_repeat("-", 30) . "<br>Modulo inviato correttamente";
                    } else {
                        echo "<br>" . str_repeat("-", 30) . "<br>Errore nell'invio del modulo";
                    }

                    echo '</div>';
                }
                ?>
                </div>

                <section class="contacts-message">
                    <div class="message">
                        <p>
                            <?php
                            $bio = nl2br(file_get_contents($messageFile));
                            echo $bio;
                            ?>
                        </p>
                    </div>
                </section>
            </section>
        </main>

        <footer>
            <?php
            echo UT::generateFooter();
            ?>
        </footer>
    </div>
</body>

</html>