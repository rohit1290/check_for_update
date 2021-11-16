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
    	elgg_register_plugin_hook_handler('cron', 'daily', 'update_check_for_update_table');

    	elgg_extend_view("admin/dashboard", "check_for_update/alert_div", 1);
  }
}

 