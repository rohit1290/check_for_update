<?php
namespace CheckForUpdate\Actions;

use Elgg\Http\OkResponse;
use CheckForUpdate\Events\CheckUpdate;

class UpdateController extends \Elgg\Controllers\GenericAction {
  protected function validate(): void {
     elgg_admin_gatekeeper();
  }
  
  protected function execute(): void {
    CheckUpdate::Table();
    CheckUpdate::Elgg();
  }
  
  protected function success(): OkResponse {
    return elgg_ok_response('', elgg_echo('admin:administer_utilities:check_for_update:sucess'));
  }
  
}
 ?>