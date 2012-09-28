<?php
require_once('../OpenSSO.php');

$o = new OpenSSO();

if ($o->check_sso()) {
	$o->logout();
	echo "<p>Logout!</p>";
	echo "<p>Current status:</p>";
	$n = $o->check_sso();
	var_dump($n);
}
?>
