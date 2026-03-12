<?php

$plugins = pluginGetList();
$flag = 0;
foreach ($plugins as $plugin_id => $plugin) {
	if($plugin['action'] == "Plugin requires update") {
		$flag++;
	}
}

if ($flag > 0) {
	echo elgg_view_message('error',
	'Updates available for '.$flag.' non-core plugins. <a href="'.elgg_get_site_url().'admin/administer_utilities/check_for_update_view">Click here</a> to know more.',
	['title' => 'Alert!']);
}

$current = elgg_get_release();
$latest = elgg_get_plugin_setting('latest_release', 'check_for_update');
if (version_compare($current, $latest, "<")) {
	echo elgg_view_message('error',
	'New version of Elgg (v '.$latest.') available.',
	['title' => 'Alert!']);
}
