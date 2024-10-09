<?php

namespace KnowYourBooks;

/**
 * Classe UTILITY
 * 
 * Contiere metodi utili per il funzionamento del sito web Know Your Books
 * 
 * @author Lorenzo Marini
 * @license GPL
 * @copyright 2024, Lorenzo Marini
 */
class Utility
{
    /**
     * generateHead()
     * 
     * Genera stringa HTML <head>
     * 
     * @param string $title Titolo sito web
     * @param string|array $css Link a file css
     * @return string
     */
    public static function generateHead($title, $css)
    {
        $str = '';
        $str .= '<meta charset="UTF-8">';
        $str .= '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
        $str .= '<meta name="description" content="Know Your Books: Recensioni libri">';
        // FAVICON
        $str .= '<link rel="shortcut icon" href="./img/know-your-books-favicon-color.png" type="image/x-icon">';
        // FONTS
        $str .= '<link rel="preconnect" href="https://fonts.googleapis.com">';
        $str .= '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>';
        $str .= '<link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400..700&family=Gupter:wght@400;500;700&display=swap" rel="stylesheet">';
        $str .= '<link href="https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,300;0,400;0,700;0,900;1,300;1,400;1,700;1,900&display=swap" rel="stylesheet">';
        // TITLE
        $str .= sprintf('<title>%s</title>', $title);
        // CSS
        if (is_array($css)) {
            foreach ($css as $link) {
                $str .= sprintf('<link rel="stylesheet" href="%s" type="text/css">', $link);
            }
        } else {
            $str .= sprintf('<link rel="stylesheet" href="%s" type="text/css">', $css);
        }

        return $str;
    }
    // Fine metodo

    /**
     * generateHeader()
     * 
     * Genera stringa HTML <header>
     */
    public static function generateHeader()
    {
        $str = '';
        $str .= '<div class="logo">';
        $str .= '<img src="./img/logo-no-background.png" alt="Know Your Books Logo" title="Know Your Books">';
        $str .= '</div>';

        return $str;
    }
    // Fine metodo

    /**
     * generateMenu()
     * 
     * Genera stringa HTML <nav>
     * 
     * @param bool $hamburger Seleziona se stampare menu di tipo hamburger o normale
     */
    public static function generateMenu($hamburger = false)
    {
        $data = './data/navMenu.json';
        $menuData = json_decode(file_get_contents($data));
        $str = '';
        if ($hamburger) {
            $str .= '<div class="hamburger-menu">';
            $str .= '<input type="checkbox" title="controllo" id="controllo"><label for ="controllo" class="label-controllo"><span></span></label>';
        }
        $str .= '<ul class="menu">';
        foreach ($menuData as $link) {
            $str .= sprintf('<li><a href="%s" title="%s">%s</a></li>', $link->url, $link->title, $link->name);
        }
        $str .= ($hamburger) ? '</ul></div>' : '</ul>';


        return $str;
    }
    // Fine metodo

    /**
     * generateFooter()
     * 
     * Genera stringa HTML <footer>
     */
    public static function generateFooter()
    {
        $str = '';
        $str .= '<div class="left-col">';
        $str .= '<h3>Contatti:</h3>';
        $str .= '<address>Email: <a href ="mailto: info@kyb.io" title="Email">info@kyb.io</a><br>Phone: +9 338xxxxxxx</address>';
        $str .= '</div>';

        $str .= '<div class="center-col">';
        $str .= '<h3>Menu:</h3>';
        $str .= self::generateMenu();
        $str .= '</div>';

        $str .= '<div class="rigth-col">';
        $str .= '<h3>Info:</h3>';
        $str .= '<a href ="#" title="Privacy">Informativa Privacy</a>';
        $str .= '<a href ="#" title="Termini">Termini e condizioni</a>';
        $str .= '</div>';

        return $str;
    }
    // Fine metodo

    /**
     * request()
     * 
     * Elabora richiesta HTTP
     * 
     * @param string $str chiave richiesta HTTP
     * @return string|null
     */
    public static function request($str)
    {
        $rit = null;
        if ($str !== null) {
            if (isset($_POST[$str])) {
                $rit = $_POST[$str];
            } elseif (isset($_GET[$str])) {
                $rit = $_GET[$str];
            }
        }
        return $rit;
    }
    // Fine metodo

    /**
     * ctrlLunghezzaStringa
     * Controlla che la stringa abbia un numero di caratteri interno ad un dato range
     * @param string -- $str Stringa da controllare
     * @param int -- $min Valore di lunghezza minimo
     * @param int -- $max Valore di lunghezza massimo
     * @return bool
     */
    public static function ctrlLunghezzaStringa($str, $min = null, $max = null)
    {
        $rit = 0;
        $n = strlen($str);

        if ($min != null && $n < $min) {
            $rit++;
        }

        if ($max = ! null && $n > $max) {
            $rit++;
        }

        return ($rit == 0);
    }
    // Fine metodo

    /**
     * printReviewCard()
     * Stampa recensione sotto forma di card HTML
     * 
     * @param array|object $reviewData Dati recensione
     * @param bool $fromJson Imposta provenienza array da JSON (con array di StdClassObj) oppure da compilazione PHP (array semplice)
     * @return string
     */
    public static function printReviewCard($reviewData, $fromJson = false)
    {
        if (!$fromJson) {
            $str = '<div class="review-card">';
            $str .= '<div class="left-col">';
            $str .= sprintf('<div class="user-data"><p>%s</p></div>', $reviewData['userName']);
            $str .= sprintf('<div class="book-data"><h3>%s</h3><h4>%s</h4></div>', $reviewData['bookName'], $reviewData['authorName']);
            $str .= '</div><div class="right-col">';
            $str .= sprintf('<div class="review-title"><p>%s</p></div>', $reviewData['reviewTitle']);
            $str .= sprintf('<div class="review-message"><p>%s</p></div>', $reviewData['review']);
            $cont = 5 - $reviewData['valutation']; // Contatore per stampare stelle vuote
            $stars = str_repeat("&#9733;", $reviewData['valutation']) . str_repeat("&#9734;", $cont);
            $str .= sprintf('<div class="valutation"><p><span class="stars">%s</span></p></div>', $stars);
            $str .= '</div></div>';
        } else {
            $str = '<div class="review-card">';
            $str .= '<div class="left-col">';
            $str .= sprintf('<div class="user-data"><p>%s</p></div>', $reviewData->userName);
            $str .= sprintf('<div class="book-data"><h3>%s</h3><h4>%s</h4></div>', $reviewData->bookName, $reviewData->authorName);
            $str .= '</div><div class="right-col">';
            $str .= sprintf('<div class="review-title"><p>%s</p></div>', $reviewData->reviewTitle);
            $str .= sprintf('<div class="review-message"><p>%s</p></div>', $reviewData->review);
            $cont = 5 - $reviewData->valutation; // Contatore per stampare stelle vuote
            $stars = str_repeat("&#9733;", $reviewData->valutation) . str_repeat("&#9734;", $cont);
            $str .= sprintf('<div class="valutation"><p><span class="stars">%s</span></p></div>', $stars);
            $str .= '</div></div>';            
        }

        return $str;
    }
    // Fine metodo

    /**
     * Funzione fileInsert
     * Scrive la stringa ricevuta dal form
     * all'interno del file $file.
     * Se $file non esiste viene creato.
     * Metodo fopen "a"
     * 
     * @param string -- $file Nome file
     * @param string -- $text Testo da inserire
     * @param bool -- $commenta Scrive a video se l'operazione è andata a buon fine
     * @return bool
     * 
     */
    public static function fileInsert($file, $text, $commenta = false)
    {
        $rit = false;
        if (!$fp = fopen($file, 'a')) {
            echo "Errore di comunicazione con il server<br>";
        } else {
            if (is_writable($file) === false) {
                echo "Errore di scrittura nel server<br>";
            } else {
                if (!fwrite($fp, $text)) {
                    echo "Si è verificato un errore. Riprovare :(";
                } else {
                    if ($commenta) echo "Il tuo messaggio è stato inserito nel server!";
                    $rit = true;
                }
            }
        }
        fclose($fp);
        return $rit;
    }
    // Fine Metodo
}
