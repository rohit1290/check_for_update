<?php
echo elgg_view('output/url', [
	'text' => 'Update Github',
	'href' => elgg_generate_action_url("check_for_update/update"),
	'class' => 'elgg-anchor elgg-menu-content elgg-button elgg-button-action',
]);
echo "<br><br>";
echo elgg_view_form("check_for_update/view");
