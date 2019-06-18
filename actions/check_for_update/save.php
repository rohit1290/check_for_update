<?php
$plugin_ids = get_input('plugin_ids');
$github_urls = get_input('github_urls');
$check_update = get_input('check_update');
$dbprefix = elgg_get_config('dbprefix');

$i = 0;
foreach ($plugin_ids as $plugin_id) {
  $id = elgg()->db->getDataRow("SELECT `id` FROM `{$dbprefix}check_for_update` WHERE `plugin_id`='{$plugin_ids[$i]}'");
  $id = $id->id;
  if($id != ""){
    //Update
    elgg()->db->updateData("UPDATE `{$dbprefix}check_for_update` SET `github_url`='{$github_urls[$i]}', `check_update`='{$check_update[$i]}' WHERE `id`=$id");
  } else {
    //Create
    elgg()->db->insertData("INSERT INTO `{$dbprefix}check_for_update` (`plugin_id`, `github_url`, `check_update`) VALUES ('{$plugin_ids[$i]}', '{$github_urls[$i]}', '{$check_update[$i]}')");
  }
  $i++;
}

system_message("Entry saved sucessfully!");

forward(REFERRER);
 ?>
