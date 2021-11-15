<?php
require_once(dirname(__FILE__) . '/lib/functions.php');

return [
	'plugin' => [
		'name' => 'Check for Plugin Update',
		'version' => '4.0',
		'dependencies' => [],
	],
	'bootstrap' => CheckForUpdate::class,
	'actions' => [
		'check_for_update/update' => ['access'=>'admin'],
	],
];
