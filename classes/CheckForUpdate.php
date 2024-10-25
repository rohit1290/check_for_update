<?php
use Elgg\DefaultPluginBootstrap;

class CheckForUpdate extends DefaultPluginBootstrap {

  public function init() {
    	// Update data on daily basis
    	elgg_register_event_handler('cron', 'daily', 'update_check_for_update_table');
    	elgg_register_event_handler('cron', 'daily', 'update_check_for_update_elgg');

    	elgg_extend_view("admin/dashboard", "check_for_update/alert_div", 1);
  }
}

 