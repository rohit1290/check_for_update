<?php 
$dbprefix = elgg_get_config('dbprefix');
$sql = "SELECT COUNT(*) as `cnt` FROM `{$dbprefix}check_for_update` WHERE (`github_tag_name`>`current_version` OR `github_manifest`>`current_version`) AND `check_update`='yes' AND `github_url` IS NOT NULL";
$dbrow = elgg()->db->getDataRow($sql);
if($dbrow->cnt > 0){
  echo elgg_view_message('error', 
    'New version for 3rd party plugin available, please update. <a href="'.elgg_get_site_url().'admin/administer_utilities/check_for_update_view">Click here</a> to know more.', 
    ['title' => 'Alert!']);
}

// Check for missing plugin entries
$plugins = elgg_get_plugins($status = 'all');
$dbplugin = array();
foreach($plugins as $plugin){
  $dbplugin[$plugin->getID()] = $plugin->getID();
}

$sql = "SELECT `plugin_id` FROM  `{$dbprefix}check_for_update`";
$data = elgg()->db->getData($sql);
foreach ($data as $dbrow) {
  unset($dbplugin[$dbrow->plugin_id]);
}

if(count($dbplugin) > 0){
  echo elgg_view_message('error', 
    'You need to map '.implode(", ",$dbplugin).' with github url for update check. <a href="'.elgg_get_site_url().'admin/administer_utilities/check_for_update_setting">Click here</a> to update list.', 
    ['title' => 'Alert!']);
}
 ?>