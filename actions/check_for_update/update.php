<?php
update_check_for_update_table();
update_check_for_update_elgg();
return elgg_ok_response('', elgg_echo('admin:administer_utilities:check_for_update:sucess'));