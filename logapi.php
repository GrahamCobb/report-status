<?php
if (!isset($_SERVER['PHP_AUTH_USER'])) {
    header('WWW-Authenticate: Basic realm="Report Status"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Text to send if user hits Cancel button';
    exit;
} elseif ($_SERVER['PHP_AUTH_USER']!='user' || $_SERVER['PHP_AUTH_PW']!='pass') {
    header('HTTP/1.0 403 Forbidden');
    echo 'Forbidden';
    exit;
}
include $CONTEXT_DOCUMENT_ROOT."php-crud-api/api.php";

function auth_command($cmd,$db,$tab) {
	 // Read, list - always allowed
	 if ($cmd=='read' || $cmd=='list') return true;
	 
	 // Create allowed if username is provided
	 if ($cmd=='create') return isset($_SERVER['REMOTE_USER']) || isset($_SERVER['PHP_AUTH_USER']);
	 
	 // Otherwise, check that username begins "full-" to permit access
	 if (isset($_SERVER['PHP_AUTH_USER'])) { $remote_user = $_SERVER['PHP_AUTH_USER']; }
	 elseif (isset($_SERVER['REMOTE_USER'])) { $remote_user = $_SERVER['REMOTE_USER']; }
	 else $remote_user = null;
	 return substr($remote_user, 0, 5) == "full-" ;
}

function tenancy_username($cmd,$db,$tab,$col) {
	 if ($col!='username') return null;
	 
	 // Update commands must set the username
	 if ($cmd=='read' || $cmd=='list') return null;
	 if (isset($_SERVER['PHP_AUTH_USER'])) {
	    return $_SERVER['PHP_AUTH_USER'];
	 } else {
	    return $_SERVER['REMOTE_USER'];
	 }
}

function auth_cols($cmd,$db,$tab,$col) {
	 // Updates cannot change id or log_time
	 return $cmd=='read' || $cmd=='list' || ($col!='id' && $col!='log_time');
}

$api = new PHP_CRUD_API(array(
		'dbengine'=>'mysql',
		'hostname'=>'localhost',
		'username'=>'statusreports',
		'password'=>'G9EI389uu4',
		'database'=>'statusreportsdb',
		'table_authorizer'=>'auth_command',
		'tenancy_function'=>'tenancy_username',
		'column_authorizer'=>'auth_cols'
));
$api->executeCommand();
