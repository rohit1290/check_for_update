<?php

function getGitProperty($url) {
	$username = elgg_get_plugin_setting('git_username', 'check_for_update');
	$token = elgg_get_plugin_setting('token', 'check_for_update');
	// $client_id = elgg_get_plugin_setting('client_id', 'check_for_update');
	// $client_secret = elgg_get_plugin_setting('client_secret', 'check_for_update');
	// $url = $url."?access_token=$token&token=$token&client_id=$client_id&client_secret=$client_secret";
	$c = curl_init();
	curl_setopt($c, CURLOPT_USERPWD, "{$username}:{$token}");
	curl_setopt($c, CURLOPT_URL, $url);
	curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($c, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
	$content = curl_exec($c);
	curl_close($c);
	return json_decode($content, true);
}

function call_check_for_update_func() {
	update_check_for_update_table('all');
}

// $update_type - all, github, local
function update_check_for_update_table($update_type = 'all') {
	echo "Plugin update check started for $update_type\n";

	$dbprefix = elgg_get_config('dbprefix');
	$dbrows = elgg()->db->getData("SELECT * FROM `{$dbprefix}check_for_update` WHERE `check_update`='yes' AND `github_url` <> ''");
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

		$sql_set_string = [];

		if ($update_type == "all" || $update_type == "local") {
			// Plugin Version
			$plugin_version = $plugin->getManifest()->getVersion();
			if ($plugin_version != $dbrow->current_version) {
				$sql_set_string[] = "`current_version`='$plugin_version'";
			}
		}

		if ($update_type == "all" || $update_type == "github") {
			// Github version
			$github_parts = explode("/", $dbrow->github_url);
			$github_owner = $github_parts[3];
			$github_repo = $github_parts[4];

			// Github Tag Name
			$github_rel = getGitProperty("https://api.github.com/repos/$github_owner/$github_repo/releases/latest");
			$latest_tag_name = $github_rel['tag_name'];

			// Github SHA ID
			$github_tags = getGitProperty("https://api.github.com/repos/$github_owner/$github_repo/tags");
			if (count($github_tags) > 0) {
				if ($latest_tag_name == "" || $latest_tag_name == null) {
					$latest_tag_name = $github_tags[0]['name'];
				}

				foreach ($github_tags as $github_tag) {
					if ($github_tag['name'] == $latest_tag_name) {
						$github_sha = $github_tag['commit']['sha'];
						break;
					}
				}
			}

			// # of advance commit
			$adv_commit = 0;
			$github_commits = getGitProperty("https://api.github.com/repos/$github_owner/$github_repo/commits");
			foreach ($github_commits as $github_commit) {
				if ($github_commit['sha'] == $github_sha) {
					break;
				}
				$adv_commit++;
			}

			$adv_commit = (int) $adv_commit;

			// Manifest version
			$context  = stream_context_create(['http' => [
				'method'  => 'GET',
				'header' => 'Authorization: Bearer '. elgg_get_plugin_setting('token', 'check_for_update')
			]]);
			$github_manifest = json_decode(json_encode(simplexml_load_string(file_get_contents("https://raw.githubusercontent.com/$github_owner/$github_repo/master/manifest.xml", false, $context))), true);
			$github_manifest = $github_manifest['version'];

			$latest_tag_name = str_replace("v", "", $latest_tag_name);

			if ($latest_tag_name != $dbrow->github_tag_name) {
				$sql_set_string[] = "`github_tag_name`='$latest_tag_name'";
			}
			if ($github_manifest != $dbrow->github_manifest) {
				$sql_set_string[] = "`github_manifest`='$github_manifest'";
			}
			if ($adv_commit != (int) $dbrow->github_adv_commit) {
				$sql_set_string[] = "`github_adv_commit`='$adv_commit'";
			}
		}

		if (count($sql_set_string) > 0) {
			$set_string = implode(",", $sql_set_string);
			elgg()->db->updateData("UPDATE `{$dbprefix}check_for_update` SET $set_string WHERE `plugin_id`='$plugin_id'");
		}
	}

	$time = time();
	if ($update_type == "all" || $update_type == "local") {
		elgg_set_plugin_setting('local_update_time', $time, 'check_for_update');
	}
	if ($update_type == "all" || $update_type == "github") {
		elgg_set_plugin_setting('github_update_time', $time, 'check_for_update');
	}
	echo "Plugin update check completed";
}

 