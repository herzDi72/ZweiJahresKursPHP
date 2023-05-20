<?php
 
class mysqldb {
 
    /**
     * Constructor: Connect to db, return handle
     *
     * @param string        $dbusername   DB username
     * @param string        $dbpassword   DB password
     * @param string        $dbname       Database to connect to
     * @param string        $host         Database host to connect to (optional)
     *
     * @return object       DB-Handle 
     */
    function __construct($dbusername, $dbpassword, $dbname, $host="localhost" ) {
 
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", "$dbusername", "$dbpassword");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 
    } catch ( PDOException $e ) {
        echo 'Verbindung zur Datenbank fehlgeschlagen: ' . $e->getMessage();
        return FALSE;
    }
 
    $this->connection=$pdo;
 
    }

    /**
    * Getter-Methode, um die DB-Verbindung auch in anderen
    * Klassen nutzen zu koennen
    */
    function getConnection() {
        return $this->connection;
    }

    /**
    * Setter-Methode um die gewuenschte Tabelle in
    * der DB auszwaehlen
    *
    * @param string        $tables   Tables
    */
    function setQueryTable($tables) {
        $this->qtable = $tables;
    }

    /**
     * Eine Abfrage aus der DB machen:
     * Nach einem Nachnamen suchen
     *
     * @param string        $searchname  Name to search
     *
     * @return array        Query result 
     */
    function searchName($searchname) {
        $statement= $this->connection->prepare("SELECT * FROM $this->qtable WHERE Nachname LIKE :nachname");
        //$statement->execute(array($table,$searchname)); 
        $statement->execute(array('nachname' => "%$searchname%")); 
 
        // Besonderheit, weil wir ein Array wollen - Kapselung!
        // $statement->fetch() liefert das Ergebnis der Abfrage zeilenweise
        // https://www.php.net/manual/de/pdostatement.fetch.php
        // Für uns ist es meist besser ein assoziatives Array 
        // mit den Ergebnissen zurückzugeben das dann gerendert werden
        // kann
        // Leeres Array definieren
        $resultArray = array();
        while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            // Die Zeilen an das Array anhängen (Push)
            $resultArray[] = $row;
        }
 
        // For Debugging
        //print_r($resultArray);
 
        // Ergebnisarray zurückgeben
        return $resultArray;
    }
}
 
?>