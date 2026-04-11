<?php

namespace CheckForUpdate\Menus;

class AdminHeader {
	
	public static function register(\Elgg\Event $event) {
    if (!elgg_in_context('admin') || !elgg_is_admin_logged_in()) {
			return;
		}
	
		$return = $event->getValue();
		$return[] = \ElggMenuItem::factory([
      'name' => 'administer_utilities:check_for_update_view',
      'text' => elgg_echo('admin:administer_utilities:check_for_update_view'),
      'href' => 'admin/administer_utilities/check_for_update_view',
			'parent_name' => 'utilities',
		]);
    
		return $return;
	}
}