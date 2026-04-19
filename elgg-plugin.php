<?php
require_once __DIR__ . '/lib/functions.php';

return [
	'plugin' => [
		'name' => 'Check for Plugin Update',
		'version' => '7.0',
	],
	'actions' => [
		'check_for_update/update' => [
			'access' => 'admin',
			'controller' => \CheckForUpdate\Actions\UpdateController::class,
		],
	],
	'events' => [
		'register' => [
			'menu:admin_header' => [
				'\CheckForUpdate\Menus\AdminHeader::register' => [],
			],
		],
		'cron' => [
			'weekly' => [
				'\CheckForUpdate\Events\CheckUpdate::Table' => [],
				'\CheckForUpdate\Events\CheckUpdate::Elgg' => [],
			],
		],
	],
	'view_extensions' => [
		'admin/dashboard' => [
			'check_for_update/alert_div' => [
				'priority' => 1
			],
		],
	],
];