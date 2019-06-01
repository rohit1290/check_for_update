<?php 
$id = get_input('id');
$dbprefix = elgg_get_config('dbprefix');
elgg()->db->deleteData("DELETE FROM `{$dbprefix}check_for_update` WHERE `id`={$id}");
system_message("Entry deleted sucessfully!");
forward(REFERRER);
 ?>