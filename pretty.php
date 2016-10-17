<html>
<!-- Table layout and CSS inspired by Luke Peters (http://codepen.io/lukepeters/pen/bfFur) -->

<head>
 <title>Status Report</title>
 <style>
 .table {
   display: table;
 }
 .row {
   display: table-row;
 }
 .header {
   color: white;
   background: blue;
 }
 .cell {
   display: block;
   padding: 2px 12px;
 }
 .good {
   background: lime;
 }
 .bad {
   background: red;
 }
 .middling {
   background: grey;
 }
 .log {
   color: grey;
   background: white;
 }
 </style>
</head>
<body>

<?php
echo "<div class=\"table\">\n";
echo " <div class=\"row header\">\n";
echo "  <div class=\"cell\">Source</div>\n";
echo "  <div class=\"cell\">Target</div>\n";
echo "  <div class=\"cell\">Status</div>\n";
echo "  <div class=\"cell\">Log</div>\n";
echo "  <div class=\"cell\">Time</div>\n";
echo " </div>\n";

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
	       foreach ($rows as $row) {
	       	       echo " <div class=\"row\">\n";
		       echo "  <div class=\"cell\">".$row['source']."</div>\n";
		       echo "  <div class=\"cell\">".$row['target']."</div>\n";
		       echo "  <div class=\"cell\">".$row['status']."</div>\n";
		       echo "  <div class=\"cell\">".$row['log_text']."</div>\n";
		       echo "  <div class=\"cell\">".$row['log_time']."</div>\n";
		       echo " </div>\n";
	       }

	       /* And any subsequent log messages */
	       $stmt = $conn->prepare("SELECT source,target,status,log_text,log_time FROM $dbtable WHERE source='$s' AND target='$t' AND status IS NULL AND log_time >= '$log_time' ORDER BY log_time DESC");
	       $stmt->execute();
	       foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
	       	       echo " <div class=\"row log\">\n";
		       echo "  <div class=\"cell\">".$row['source']."</div>\n";
		       echo "  <div class=\"cell\">".$row['target']."</div>\n";
		       echo "  <div class=\"cell\">".$row['status']."</div>\n";
		       echo "  <div class=\"cell\">".$row['log_text']."</div>\n";
		       echo "  <div class=\"cell\">".$row['log_time']."</div>\n";
		       echo " </div>\n";
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
echo "</div>";
?>

</body>
</html>