<?php 

$sync_type = get_input('sync_type','rel');
$plugin_id = get_input('plugin_id', null);
$tag_name = get_input('tag_name', null);

if($plugin_id == null) {
  register_error("Unknown plugin");
  forward(REFERRER);
}
if($tag_name == null) {
  register_error("Unknown tag");
  forward(REFERRER);
}

$path = elgg_get_plugins_path()."$plugin_id";
if(!file_exists($path)) {
  register_error("Unknown path");
  forward(REFERRER);
}

// change working directory
chdir($path);

// check if git is initialize
if(!file_exists(".git")) {
  exec("git init");
}

// Check if remote address is defined
$dbprefix = elgg_get_config('dbprefix');
$dbrow = elgg()->db->getDataRow("SELECT `github_url` FROM `{$dbprefix}check_for_update` WHERE `plugin_id` = '$plugin_id'");
$repo = $dbrow->github_url;

$o_remote_url = exec("git remote get-url origin");
$u_remote_url = exec("git remote get-url upstream");

$pull_from  = '';

if($o_remote_url == $repo){
  $pull_from  = 'origin';
} else if($u_remote_url == $repo){
  $pull_from  = 'upstream';
} else {
  $pull_from  = 'origin';
  exec("git remote set-url $pull_from $repo");
}

if($sync_type == 'rel'){
//   exec("git clean -f -d");
//   exec("git stash");
//   exec("git pull $pull_from tag v$tag_name --no-tags --allow-unrelated-histories");
//   exec("git reset --hard v$tag_name");
//   exec("git pull $pull_from tag $tag_name --no-tags --allow-unrelated-histories");
//   exec("git reset --hard $tag_name");
} else {
//   exec("git fetch $pull_from");
  exec("git pull $pull_from master");
}

update_check_for_update_table('local');
system_message("Plugin $plugin_id updated sucessfully");
forward(REFERRER);

?>
