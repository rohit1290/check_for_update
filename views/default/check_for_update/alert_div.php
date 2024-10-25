<?php

$plugins = pluginGetList();
$flag = 0;
foreach ($plugins as $plugin_id => $plugin) {
	if($plugin['action'] == "Plugin requires update") {
		$flag = 1;
	}
}

if ($flag == 1) {
	echo elgg_view_message('error',
	'New version for 3rd party plugin available, please update. <a href="'.elgg_get_site_url().'admin/administer_utilities/check_for_update_view">Click here</a> to know more.',
	['title' => 'Alert!']);
}

$current = elgg_get_release();
$latest = elgg_get_plugin_from_id('check_for_update')->getSetting('latest_release');
if ($current != $latest && $latest != null) {
	echo elgg_view_message('error',
	'New version of Elgg (v '.$latest.') available.',
	['title' => 'Alert!']);
}
