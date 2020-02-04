<style>
.label {
    display: inline;
    padding: .2em .6em .3em;
    font-size: 75%;
    font-weight: 700;
    line-height: 1;
    color: #fff;
    text-align: center;
    white-space: nowrap;
    vertical-align: baseline;
    border-radius: .25em;
}
.bg-green {
  background-color: #00a65a !important;
}
.bg-red {
  background-color: #dd4b39 !important;
}
</style>
<?php
$dbprefix = elgg_get_config('dbprefix');
$sql = "SELECT * FROM  `{$dbprefix}check_for_update`";
$data = elgg()->db->getData($sql);
foreach ($data as $dbrow) {
	$dbplugin[$dbrow->plugin_id]['github'] = $dbrow->github_url;
	$dbplugin[$dbrow->plugin_id]['check_update'] = $dbrow->check_update;
}

//fetch all plugin
$plugins = elgg_get_plugins('any');

$i = 0;
foreach ($plugins as $plugin) {
	if (!$plugin->getManifest()) {
		echo "<tr><td colspan='6'>$plugin->guid</td></tr>";
		continue;
	}

	$plugin_id = $plugin->getID();
	$author = $plugin->getManifest()->getAuthor();

	if (strpos(strtolower($author), strtolower('Core developers')) !== false && $dbplugin[$plugin_id]['check_update'] == "") {
		$dbplugin[$plugin_id]['check_update'] = "no";
	}

	$status = ($plugin->isActive() ? '<small class="label bg-green">Active</small>' : '<small class="label bg-red">Inactive</small>');
  //Show Name (plugin_id) status
	echo "<b>{$plugin->getManifest()->getName()}</b> ({$plugin->getID()} by $author) $status";
	echo "<input type='hidden' name='plugin_ids[$i]' value='{$plugin->getID()}'>";

	$url = $plugin->getManifest()->getWebsite();
  // Show link for github
	$github = "";
	if ($dbplugin[$plugin_id]['github'] == null) {
		if (strpos($url, 'github') !== false) {
			$github = $url;
		}
	} else {
		$github = $dbplugin[$plugin_id]['github'];
	}
	echo "<input type='text' name='github_urls[$i]' value='{$github}' placeholder='Enter Github Repo URL'>";

	echo elgg_view_field([
	 '#type' => 'select',
	 '#label' => "Check for Update",
	 'required' => true,
	 'name' => "check_update[$i]",
	 'options_values' => [
		'yes' => "Yes",
		'no' => "No",
	 ],
	 'value' => $dbplugin[$plugin_id]['check_update'],
	]);
	echo "<br>";

	$i++;
}
echo elgg_view('input/submit', [
		'value' => elgg_echo('submit'),
		'name' => 'submit',
		'class' => 'elgg-button-submit mls',
	]);
	