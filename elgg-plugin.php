<?php
require_once(dirname(__FILE__) . '/lib/functions.php');

return [
	'bootstrap' => CheckForUpdate::class,
	'actions' => [
		'check_for_update/save' => ['access'=>'admin'],
		'check_for_update/delete' => ['access'=>'admin'],
		'check_for_update/update' => ['access'=>'admin'],
		// 'check_for_update/sync' => ['access'=>'admin'],
	],
];
