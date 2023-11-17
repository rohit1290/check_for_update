<?php
require_once(dirname(__FILE__) . '/lib/functions.php');

return [
	'plugin' => [
		'name' => 'Check for Plugin Update',
		'version' => '5.1',
		'dependencies' => [],
	],
	'bootstrap' => CheckForUpdate::class,
	'actions' => [
		'check_for_update/update' => ['access'=>'admin'],
	],
	'events' => [
		'register' => [
				'menu:admin_header' => [
					'Elgg\CheckForUpdate\Menus\AdminHeader::register' => [],
				],
			],
		],
];
