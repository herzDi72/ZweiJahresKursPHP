<?php
/**
 * DokuWiki Plugin projekt (Syntax Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Frank Schiebel <frank@ua25.de>
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) {
    die();
}

// Klassendateien einbinden
require("mysqldb.php");

class syntax_plugin_projekt extends DokuWiki_Syntax_Plugin
{
    /**
     * @return string Syntax mode type
     */
    public function getType()
    {
        return 'substition';
    }

    /**
     * @return string Paragraph type
     */
    public function getPType()
    {
        return 'block';
    }

    /**
     * @return int Sort order - Low numbers go before high numbers
     */
    public function getSort()
    {
        return 222;
    }

    public function printform() {
 
        // Erzeuge ein neues "Form" Objekt
        $form = new dokuwiki\Form\Form();
 
        // Füge ein <input> Feld mit dem 'name'-Attribut 'inputName' und dem Label 'Dein Name' hinzu
        $form->addTextInput('inputName', 'Dein Name');
 
        // Füge einen "submit" Button hinzu
        $form->addButton('submit', 'Senden');
 
        // Erzeuge den HTML-Code zum Formular
        return $form->toHTML();
    }

    public function erzeugeFormularErfassen() {

        $form = new dokuwiki\Form\Form();

        //Info zu Gang (Backen / Kochen) einlesen
        $form->addHTML('<br><hr><br>');
        $form->addHTML('<b>Bitte wählen Sie aus: </b><br>');
        $form->addRadioButton('gang', 'Kochen');
        $form->addHTML('<br>');
        $form->addRadioButton('gang', 'Backen');
        $form->addHTML('<br><hr><br>');

        //Info zur Herkunft einlesen
        $optionsArray = array("Schwäbisch", "Italienisch", "Indisch", "Britisch");
        $form->addHTML('<b>Bitte wählen Sie die passende Region aus: </b>');
        $form->addDropdown('DropdownName', $optionsArray);
        $form->addHTML('<br><hr><br>');

        //Namen des Gerichtes einlsen
        $form->addHTML('<br><hr><br>');
        $form->addHTML('<b>Bitte geben Sie den Namen für Ihr Rezept ein: </b><br>');
        $form->addTextInput('gerichtName', 'Name des Rezeptes: ');
        $form->addHTML('<br><hr><br>');

        //Liste der Zutaten
        $form->addHTML('<br><hr><br>');
        $form->addHTML('<b>Bitte geben Sie die Liste der Zutaten ein: </b><br>');
        $form->addTextInput('listeZutaten', 'Liste der Zutaten: ');
        $form->addHTML('<br><hr><br>');

        //Liste der Zutaten
        $form->addHTML('<br><hr><br>');
        $form->addHTML('<b>Bitte geben Sie die Beschreibung der Zubereitung ein: </b><br>');
        $form->addTextInput('zubereitung', 'Beschreibung der Zubereitung: ');
        $form->addHTML('<br><hr><br>');

         // Eingabe abschliessen
         $form->addButton('submit', 'Eingabe abschließen');





        $form->addHTML('<br>');

        return $form->toHTML();
    }

    /**
     * Connect lookup pattern to lexer.
     *
     * @param string $mode Parser mode
     */
    public function connectTo($mode)
    {  
        // Wenn das Pattern gematcht wird springt das Plugin zur Methode "handle" 
        // ("Handle Match"), dabei wird er gematchte String als $match übergeben und kann weiter 
        // verarbeitet werden - s.u. 
        $this->Lexer->addSpecialPattern('\{\{rezepte>.*?\}\}', $mode, 'plugin_projekt');
        // $this->Lexer->addSpecialPattern('\{\{pliste>.*?\}\}', $mode, 'plugin_projekt');
    }

    /**
     * Handle matches of the projekt syntax
     *
     * @param string       $match   The match of the syntax
     * @param int          $state   The state of the handler
     * @param int          $pos     The position in the document
     * @param Doku_Handler $handler The handler
     *
     * @return array Data for the renderer
     */
    public function handle($match, $state, $pos, Doku_Handler $handler)
    {
        // Geschweifte Klammern entfernen
        $match = substr($match, 2, -2);
        // Splitten am ">". Erster Teil geht nach "command"
        list($command, $match) = explode('>', $match, 2);
        
        // Weitere Optionen können übergeben werden, wenn Bedarf ist
        // list($input, $options) = split('#', $match, 2);

        // Wir geben als "options" jetzt einfach mal den Teil nach dem ">"
        // an die render Methode weiter:
        $options = $match;

        // Das Array, das hier zurückgegeben wird, landet als $data 
        // in der render-Methode, s.u. 
        return array($command, $options);
    }

    /**
     * Render xhtml output or metadata
     *
     * @param string        $mode     Renderer mode (supported modes: xhtml)
     * @param Doku_Renderer $renderer The renderer
     * @param array         $data     The data from the handler() function
     *
     * @return bool If rendering was successful.
     */
    public function render($mode, Doku_Renderer $renderer, $data)
    {
        // Wir rendern nur HTML, sonst nix!
        if ($mode !== 'xhtml') {
            return false;
        }

        // Die handle Methode liefert in $data ein Array mit 2 Elementen (s.o.)
        // das klamüsern wir jetzt mal auseinander:
        $command = $data[0];
        $options = $data[1];

        // Erzeugen einer Verbindung zur Datenbank
        // Zugangsdaten aus den Einstellungen des
        // DokuWiki abrufen
        $dbuser = $this->getConf('dbusername');
        $dbpasswd = $this->getConf('dbpasswd'); 
        $dbname = $this->getConf('dbname');
        $dbhost = $this->getConf('dbhost');

        //Konstruktor von mysqldb aufrufen um
        //Vergindung herzustellen
        $dbhandle = new mysqldb($dbuser, $dbpasswd, $dbname, $dbhost);


        // Alles was mit dem Verkettungs-Operator "."
        // an $renderer->doc "angehängt" wird, 
        // wird als Ersetzung des gematche Patterns in der 
        // Wiki-Seite eingefügt. Der Browser will HTML sehen.
        // rufe die Methode "printform" auf - diese liefert HTML zurück
        // das kann direkt an das renderer Attribut angehängt werden.
        // $renderer->doc .= $this->printform();

        // Das gibt den Wert des mit POST übergebenen Eingabefelds
        // aus. Das sollte schöner mit HTML fomatiert werden und nur 
        // dann ausgegeben werden, wenn es auch gesetzt ist...

        if ($options== 'erfassen') {
            $renderer->doc .= $this->erzeugeFormularErfassen();
        }

        if ($options== 'suchen') {
	        $renderer->doc .="<ul'>";
            $renderer->doc .="<li>command . $command . </li>";
            $renderer->doc .="<li>option . $options . </li>";
            $renderer->doc .="</ul>";
        }

        // Alles gut: 
        return true;
    }

    
}

