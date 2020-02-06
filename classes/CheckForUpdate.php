<?php
use Elgg\DefaultPluginBootstrap;

class CheckForUpdate extends DefaultPluginBootstrap {

  public function init() {
    	elgg_register_menu_item('page', [
    		'name' => 'administer_utilities:check_for_update_view',
    		'text' => elgg_echo('admin:administer_utilities:check_for_update_view'),
    		'href' => 'admin/administer_utilities/check_for_update_view',
    		'section' => 'administer',
    		'parent_name' => 'administer_utilities',
    		'context' => 'admin',
    	]);

    	// Update data on daily basis
    	elgg_register_plugin_hook_handler('cron', 'daily', 'call_check_for_update_func');

    	elgg_extend_view("admin/dashboard", "check_for_update/alert_div", 1);
  }

  public function activate() {
    $path = dirname(dirname(__FILE__))."/db/check_for_update.sql";
    _elgg_services()->db->runSqlScript($path);
  }
  
  public function upgrade() {
  	$dbprefix = elgg_get_config('dbprefix');
  	$dbrows = elgg()->db->getData("SELECT * FROM `{$dbprefix}check_for_update` WHERE `check_update`='yes'");
  	foreach ($dbrows as $dbrow) {
  		$plugin_id = $dbrow->plugin_id;
  		$plugin = elgg_get_plugin_from_id($plugin_id);

  		if ($plugin == null) {
  			elgg()->db->updateData("UPDATE `{$dbprefix}check_for_update` SET `check_update`='no' WHERE `plugin_id`='$plugin_id'");
  			continue;
  		}

  		if (!is_dir($plugin->getPath())) {
  			elgg()->db->updateData("UPDATE `{$dbprefix}check_for_update` SET `check_update`='no' WHERE `plugin_id`='$plugin_id'");
  			continue;
  		}
    }
  }

}

 