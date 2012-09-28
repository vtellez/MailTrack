<?php

include('../OpenSSO.php');

$o = new OpenSSO();

$res1 = $o->check_and_force_sso();

echo "<h1>check_and_forcesso()</h1>";
echo "<pre>";
var_dump($res1);

echo "\nErr? : " . $o->check_error();
echo "</pre>";

if ($res1 === TRUE) {
	echo '<pre>';
	$class = $o->attribute('objectClass');
	var_dump($class);
	echo '</pre>';
}
?>
