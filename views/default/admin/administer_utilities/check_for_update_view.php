<?php
$update_href = elgg_generate_action_url("check_for_update/update?type=github");
echo elgg_view('output/url', [
	'text' => 'Update Github',
	'href' => $update_href,
	'class' => 'elgg-anchor elgg-menu-content elgg-button elgg-button-action',
]);
$update_href = elgg_generate_action_url("check_for_update/update?type=local");
echo elgg_view('output/url', [
	'text' => 'Update Local',
	'href' => $update_href,
	'class' => 'elgg-anchor elgg-menu-content elgg-button elgg-button-action',
]);
$update_href = elgg_generate_action_url("check_for_update/update?type=all");
echo elgg_view('output/url', [
	'text' => 'Update All',
	'href' => $update_href,
	'class' => 'elgg-anchor elgg-menu-content elgg-button elgg-button-action',
]);
echo elgg_view('output/url', [
	'text' => 'Settings',
	'href' => elgg_get_site_url()."admin/administer_utilities/check_for_update_setting",
	'class' => 'elgg-anchor elgg-menu-content elgg-button elgg-button-action',
]);
echo "<br><br>";
echo elgg_view_form("check_for_update/view");
