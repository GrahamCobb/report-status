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
include "php-crud-api/api.php";

function tenancy_username($cmd,$db,$tab,$col) {
	 if ($cmd=='read' || $cmd=='list' || $col<>'username') return null;
	 return $_SERVER['PHP_AUTH_USER'];
}

function auth_cols($cmd,$db,$tab,$col) {
	 return $cmd=='read' || $cmd=='list' || ($col<>'id' && $col<>'log_time');
}

$api = new PHP_CRUD_API(array(
		'dbengine'=>'mysql',
		'hostname'=>'localhost',
		'username'=>'testuser',
		'password'=>'test123test!',
		'database'=>'testdb',
		'tenancy_function'=>'tenancy_username',
		'column_authorizer'=>'auth_cols'
));
$api->executeCommand();
