 <?php
echo "<table style='border: solid 1px black;'>\n";
echo "<tr><th>Source</th><th>Target</th><th>Status</th><th>Log</th><th>Time</th></tr>\n";

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
include "dbdetails.php";

try {
    $conn = new PDO("mysql:host=$dbserver", $dbuser, $dbpass);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->exec("use $db");

    /* For each combination of source and target */
    $stmt = $conn->prepare("SELECT DISTINCT source,target FROM $dbtable ORDER BY log_time DESC");
    $stmt->execute();
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $combination) {
	    $s = $combination["source"];
	    $t = $combination["target"];

	    /* Get latest status report */
	    $stmt = $conn->prepare("SELECT source,target,status,log_text,log_time FROM $dbtable WHERE source='$s' AND target='$t' AND status IS NOT NULL ORDER BY log_time DESC LIMIT 1");
	    $stmt->execute();
	    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	    if (count($rows) > 0) {
	       $log_time = $rows[0]['log_time'];
	       foreach(new TableRows(new RecursiveArrayIterator($rows)) as $k=>$v) {
	            echo $v;
	       }

	       /* And any subsequent log messages */
	       $stmt = $conn->prepare("SELECT source,target,status,log_text,log_time FROM $dbtable WHERE source='$s' AND target='$t' AND status IS NULL AND log_time >= '$log_time' ORDER BY log_time DESC");
	       $stmt->execute();
	       $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	       foreach(new TableRows(new RecursiveArrayIterator($rows)) as $k=>$v) {
	            echo $v;	    
	       }
	   } 
    }


/*    foreach(new TableRows(new RecursiveArrayIterator($stmt->fetchAll())) as $k=>$v) {
        echo $v;
    	}*/


    }
catch(PDOException $e)
    {
    echo "Connection failed: " . $e->getMessage();
    }

$conn = null;
echo "</table>";
?> 
