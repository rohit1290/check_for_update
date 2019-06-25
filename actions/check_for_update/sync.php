<?php 

$sync_type = get_input('sync_type', 'rel');
$change_type = get_input('change_type', null);
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
  register_error("Git not defined for plugin $plugin_id"); // Git is defined in save.php action file
  forward(REFERRER);
}

// Check if remote address is defined
$dbprefix = elgg_get_config('dbprefix');
$dbrow = elgg()->db->getDataRow("SELECT `github_url` FROM `{$dbprefix}check_for_update` WHERE `plugin_id` = '$plugin_id'");
$repo = $dbrow->github_url;

$o_remote_url = exec("git remote get-url origin");

if($o_remote_url == "" || $o_remote_url == null) {
  register_error("Origin url not defined for plugin $plugin_id"); // Origin url is defined in save.php action file
  forward(REFERRER);
}

if($o_remote_url != $repo) {
  register_error("Origin url not same as DB url for plugin $plugin_id");
  forward(REFERRER);
}

exec("git fetch origin master");
$output = exec('git rev-list --left-right --count origin/master...master');
if($output == "" || $output == null) {
  register_error("Cannot get commit count for plugin $plugin_id");
  forward(REFERRER);
}

// $output = explode("	", $output);
// $commit_behind = trim($output[0]); // local changes
// $commit_ahead = trim($output[1]); // remote changes
// if($commit_behind > 0) {
//   register_error("There are local changes for plugin $plugin_id. Plese fix them manually.");
//   forward(REFERRER);
// }

// if($commit_ahead == 0) {
//   register_error("Nothing to update for plugin $plugin_id");
//   forward(REFERRER);
// }

if($change_type == 'clean') {
  exec("git clean -f -d");
  exec("git stash");
}

if($sync_type == 'rel') {
  exec("git pull origin tag v$tag_name --no-tags --allow-unrelated-histories");
  exec("git reset --hard v$tag_name");
  exec("git pull origin tag $tag_name --no-tags --allow-unrelated-histories");
  exec("git reset --hard $tag_name");
} else {
  $message = exec("git pull origin master");
  system_message($message);
}

update_check_for_update_table('local');
system_message("Plugin $plugin_id updated sucessfully");
forward(REFERRER);

?>
