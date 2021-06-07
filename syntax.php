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
        $this->Lexer->addSpecialPattern('\{\{projekt>.+?\}\}', $mode, 'plugin_projekt');
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


        // Alles was mit dem Verkettungs-Operator "."
        // an $renderer->doc "angehängt" wird, 
        // wird als Ersetzung des gematche Patterns in der 
        // Wiki-Seite eingefügt. Der Browser will HTML sehen.
        $renderer->doc .= "<h2>Diese Überschrift kommt aus dem Plugin projekt</h2>";
        $renderer->doc .= "<ul>";
        $renderer->doc .= "<li>Als <tt>command</tt> wurde " . $command . " übergeben.</li>";
        $renderer->doc .= "<li>Als <tt>options</tt> wurde " . $options . " übergeben.</li>";
        $renderer->doc .= "</ul>";

        // Alles gut: 
        return true;
    }
}

