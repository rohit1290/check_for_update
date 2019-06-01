<?php
echo elgg_view('output/url', [
	'text' => 'View Mode',
	'href' => elgg_get_site_url()."admin/administer_utilities/check_for_update_view",
	'class' => 'elgg-anchor elgg-menu-content elgg-button elgg-button-action',
]);
echo "<br><br>";
echo  elgg_view_form("check_for_update/save");
?>