<?php
$plugin_ids = get_input('plugin_ids');
$github_urls = get_input('github_urls');
$check_update = get_input('check_update');
$dbprefix = elgg_get_config('dbprefix');

$i = 0;
foreach ($plugin_ids as $plugin_id) {
  unset($repo);
  if($check_update[$i] == "yes") {
    $path = elgg_get_plugins_path()."$plugin_id";
    chdir($path);
    $repo = rtrim($github_urls[$i], '/');
    $o_remote_url = exec("git remote get-url origin");
    
    if(!file_exists(".git")) {
        exec("git init");
        exec("git remote add origin $repo");
        exec("git remote set-url origin $repo");
    } else if($o_remote_url == "" || $o_remote_url == null || $o_remote_url != $repo) {
    		exec("git remote add origin $repo");
        exec("git remote set-url origin $repo");
    }
  }
  
  $id = elgg()->db->getDataRow("SELECT `id` FROM `{$dbprefix}check_for_update` WHERE `plugin_id`='{$plugin_ids[$i]}'");
  $id = $id->id;
  if($id != ""){
    //Update
    elgg()->db->updateData("UPDATE `{$dbprefix}check_for_update` SET `github_url`='$repo', `check_update`='{$check_update[$i]}' WHERE `id`=$id");
  } else {
    //Create
    elgg()->db->insertData("INSERT INTO `{$dbprefix}check_for_update` (`plugin_id`, `github_url`, `check_update`) VALUES ('{$plugin_ids[$i]}', '$repo', '{$check_update[$i]}')");
  }
  $i++;
}

system_message("Entry saved sucessfully!");

forward(REFERRER);
 ?>
