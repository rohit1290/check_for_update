<?php
// APIS to update the database 
$type = get_input('type'); // all, github, local
update_check_for_update_table($type);
system_message("List updated sucessfully");
forward(REFERRER);

	?>
