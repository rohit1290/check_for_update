<?php

$list = pluginGetList();
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