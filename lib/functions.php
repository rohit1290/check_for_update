<?php

function pluginGetList() {
  return elgg_call(ELGG_IGNORE_ACCESS | ELGG_SHOW_DISABLED_ENTITIES, function() {
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
      $final[$id]['github_composer'] = elgg_get_plugin_setting('github_composer', $id);
      $final[$id]['github_tag_name'] = elgg_get_plugin_setting('github_tag_name', $id);
      $final[$id]['github_adv_commit'] = elgg_get_plugin_setting('github_adv_commit', $id);
      $final[$id]['current_version'] = $plugin->getVersion();
      $final[$id]['owner'] = $github_parts[3];
      
      $pluginGetVersion = $plugin->getVersion();
      $versions = [elgg_get_plugin_setting('github_tag_name', $id), elgg_get_plugin_setting('github_composer', $id)];
      usort($versions, 'version_compare');
      $maxVersion = end($versions);
      $pluginNoVer = "0.1";
      
      if(!$plugin->isActive()) {
        $final[$id]['action'] = "Plugin not active";
        $final[$id]['class'] = "bg-gray disabled";
      } else if (GitCompare($pluginGetVersion, $pluginNoVer, "==")) {
        $final[$id]['action'] = "Plugin has no version";
        $final[$id]['class'] = "bg-gray disabled";
      } else if (GitCompare($pluginGetVersion, $maxVersion, "<")) {
        $final[$id]['action'] = "Plugin requires update";
        $final[$id]['class'] = "bg-red disabled";
      } else if (GitCompare($pluginGetVersion, $maxVersion, ">")) {
        $final[$id]['action'] = "Updated plugin installed";
        $final[$id]['class'] = "bg-yellow disabled";
      } else if (GitCompare($pluginGetVersion, $maxVersion, "==")) {
        $final[$id]['action'] = "No Action required";
        $final[$id]['class'] = "bg-green disabled";
      } else {
        $final[$id]['action'] = "Action Unknown";
        $final[$id]['class'] = "bg-blue disabled";
      }
    }
    // var_dump($final);
    return $final;
  });
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

function GitCompare($a, $b, $op) {
  return version_compare($a, $b, $op);
}