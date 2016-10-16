 <?php
echo "<table style='border: solid 1px black;'>\n";
echo "<tr><th>Id</th><th>User</th><th>Source</th><th>Target</th><th>Status</th><th>Log</th><th>Time</th></tr>\n";

class TableRows extends RecursiveIteratorIterator {
    function __construct($it) {
        parent::__construct($it, self::LEAVES_ONLY);
    }

    function current() {
        return "<td style='width:150px;border:1px solid black;'>" . parent::current(). "</td>";
    }

    function beginChildren() {
        echo "<tr>";
    }

    function endChildren() {
        echo "</tr>" . "\n";
    }
}

$dbserver = "localhost";
$dbuser = "statusreports";
$dbpass = "password";
$db = "statusreportsdb";
$dbtable = "statuslog";
include $_SERVER['CONTEXT_DOCUMENT_ROOT']."dbdetails";

try {
    $conn = new PDO("mysql:host=$dbserver", $dbuser, $dbpass);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->exec("use $db");

    $stmt = $conn->prepare("SELECT * FROM $dbtable ORDER BY log_time DESC");
    $stmt->execute();
    // set the resulting array to associative
    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
    foreach(new TableRows(new RecursiveArrayIterator($stmt->fetchAll())) as $k=>$v) {
        echo $v;
    	}    
    }
catch(PDOException $e)
    {
    echo "Connection failed: " . $e->getMessage();
    }

$conn = null;
echo "</table>";
?> 
