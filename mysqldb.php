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
}
 
?>