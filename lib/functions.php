<?php

function pluginGetList() {
  $plugins = elgg_get_plugins('any');
  $final = [];

  foreach ($plugins as $plugin) {
  	if ($plugin === null || !is_dir( $plugin->getPath())) {
  		continue;
  	}
    $gitURL = $plugin->getRepositoryURL();
    if($gitURL != "") {
      $github_parts = explode("/", $gitURL);
    } else {
      $github_parts = [
        1 => "",
        2 => "",
        3 => ""
      ];
    }
    if($github_parts[3] == "elgg") {
      continue;
    }

    $id = $plugin->getID();
    $final[$id]['github_url'] = "";
    if($github_parts[2] == "github.com") {
      $final[$id]['github_url'] = $plugin->getRepositoryURL();
    }
    $final[$id]['id'] = $id;
    $final[$id]['status'] = ($plugin->isActive() ? '<p class="label bg-green small">Active</p>' : '<p class="label bg-red small">Inactive</p>');
    $final[$id]['github_composer'] = $plugin->getSetting('github_composer');
    $final[$id]['github_tag_name'] = $plugin->getSetting('github_tag_name');
    $final[$id]['github_adv_commit'] = $plugin->getSetting('github_adv_commit');
    $final[$id]['current_version'] = $plugin->getVersion();
    $final[$id]['owner'] = $github_parts[3];
    
    $pluginGetVersion = $plugin->getVersion();
    $pluginGetGitTagName = $plugin->getSetting('github_tag_name');
    $pluginGetGitComp = $plugin->getSetting('github_composer');
    $pluginNoVer = "0.1";
    
    if (GitCompare($pluginGetVersion, $pluginNoVer, "==")) {
      $final[$id]['action'] = "Plugin does not have a version";
      $final[$id]['class'] = "bg-gray disabled";
    } else if (GitCompare($pluginGetGitTagName, $pluginGetVersion, "==") || GitCompare($pluginGetGitComp, $pluginGetVersion, "==")) {
      $final[$id]['action'] = "No action required";
      $final[$id]['class'] = "bg-green disabled";
    } else if (GitCompare($pluginGetGitTagName, $pluginGetVersion, ">")  && GitCompare($pluginGetGitComp, $pluginGetVersion, ">")) {
      $final[$id]['action'] = "Plugin requires update";
      $final[$id]['class'] = "bg-red disabled";
    } else if (GitCompare($pluginGetGitTagName, $pluginGetVersion, "<")  && GitCompare($pluginGetGitComp, $pluginGetVersion, "<")) {
      $final[$id]['action'] = "Updated plugin installed";
      $final[$id]['class'] = "bg-yellow disabled";
    }
    
  }
  return $final;
}

function getGithubComposerVer($user, $repo) {
  
  $url = "https://api.github.com/repos/{$user}/{$repo}/contents/elgg-plugin.php";
  $data = getGitProperty($url);
  if (!array_key_exists('download_url', $data) || trim($data['download_url']) === '') {
    return "-";
  }
  $lines = file($data['download_url']);
  
  if($lines !== false) {
    foreach ($lines as $lineNumber => $line) {
      if (preg_match("/['\"]version['\"]\s*=>\s*['\"]([^'\"]+)['\"]/", $line, $matches)) {
        return trim($matches[1]);
      }
    }
  }
    return "-";
}

function getGitProperty($url) {
	$username = elgg_get_plugin_setting('git_username', 'check_for_update');
	$token = elgg_get_plugin_setting('token', 'check_for_update');
	$c = curl_init();
	curl_setopt($c, CURLOPT_USERPWD, "{$username}:{$token}");
	curl_setopt($c, CURLOPT_URL, $url);
	curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($c, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
	$content = curl_exec($c);
	curl_close($c);
	$result = json_decode($content, true);
  return is_array($result) ? $result : ['download_url' => "", "tag_name" => "", "name" => ""];
}

function update_check_for_update_table() {
  elgg_call(ELGG_IGNORE_ACCESS, function() {
  	echo "Plugin update check started\n";

    $plugins = pluginGetList();
    foreach ($plugins as $plugin_id => $plugin) {
      if($plugin['github_url'] == "") { continue; }
      
  			// Github version
  			$github_parts = explode("/", $plugin['github_url']);
  			$github_owner = $github_parts[3];
  			$github_repo = $github_parts[4];
        
        // Github Composer Version
        $github_composer = getGithubComposerVer($github_owner, $github_repo);
        
  			// Github Tag Name
  			$github_rel = getGitProperty("https://api.github.com/repos/$github_owner/$github_repo/releases/latest");
  			$latest_tag_name = $github_rel['tag_name'] ?? '-';

  			// Github SHA ID
  			$github_tags = getGitProperty("https://api.github.com/repos/$github_owner/$github_repo/tags");
  			if (count($github_tags) > 0) {
  				if ($latest_tag_name == "" || $latest_tag_name == null) {
  					$latest_tag_name = $github_tags[0]['name'];
  				}

  				// foreach ($github_tags as $github_tag) {
  				// 	if ($github_tag['name'] == $latest_tag_name) {
  				// 		$github_sha = $github_tag['commit']['sha'];
  				// 		break;
  				// 	}
  				// }
  			}
        
        $path = elgg_get_plugin_from_id($plugin_id)->getPath();
        $adv_commit = "-";
        if(file_exists("{$path}.git")) {
          $nowpath = getcwd();
          chdir($path);
          $github_sha = exec("git rev-parse HEAD");
          chdir($nowpath);
          
          if(strlen($github_sha) == 40) {
            // # of advance commit
            $adv_commit = 0;
            $github_commits = getGitProperty("https://api.github.com/repos/$github_owner/$github_repo/commits");
            if(count($github_commits) > 0) {
              foreach ($github_commits as $github_commit) {
                if(array_key_exists('sha', $github_commit)) {
                  if ($github_commit['sha'] == $github_sha) {
                    break;
                  }
                }
                $adv_commit++;
              }
            }
          }
        }

  			$latest_tag_name = str_replace(["v"," "], "", $latest_tag_name);
        $latest_tag_name = trim($latest_tag_name);
  			if ($latest_tag_name != $plugin['github_tag_name']) {
  				elgg_get_plugin_from_id($plugin_id)->setSetting('github_tag_name', $latest_tag_name);
  			}
        $github_composer = trim($github_composer);
  			if ($github_composer != $plugin['github_composer']) {
  				elgg_get_plugin_from_id($plugin_id)->setSetting('github_composer', $github_composer);
  			}
        $adv_commit = trim($adv_commit);
  			if ($adv_commit != (int) $plugin['github_adv_commit']) {
  			  elgg_get_plugin_from_id($plugin_id)->setSetting('github_adv_commit', $adv_commit);
  			}
  	}

  	$time = time();
    elgg_get_plugin_from_id('check_for_update')->setSetting('github_update_time', $time);
  	echo "Plugin update check completed";
  });
}

function GitCompare($a, $b, $op) {
  return version_compare($a, $b, $op);
}

function update_check_for_update_elgg() {
  $url = "https://api.github.com/repos/elgg/elgg/releases";
  $c = curl_init();
  curl_setopt($c, CURLOPT_URL, $url);
  curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($c, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
  $content = curl_exec($c);
  curl_close($c);
  $result = json_decode($content, true);
  $current = elgg_get_release();
  $tag_name = 0;
  foreach ($result as $key => $v) {
    if ($key >= 5) break;
    if(GitCompare($current, $v['tag_name'], "<=")) {
      $tag_name = $v['tag_name'];
      break;
    }
  }
  if($tag_name != 0) {
    elgg_get_plugin_from_id('check_for_update')->setSetting('latest_release', $tag_name);
  }
}