<style>
.label {
    display: inline;
    padding: .2em .6em .3em;
    font-size: 75%;
    font-weight: 700;
    line-height: 1;
    color: #fff;
    text-align: center;
    white-space: nowrap;
    vertical-align: baseline;
    border-radius: .25em;
}
.bg-green {
  background-color: #00a65a !important;
}
.bg-red {
  background-color: #dd4b39 !important;
}
.bg-yellow {
  background-color: #f39c12 !important;
}
.bg-gray {
    color: #000;
    background-color: #b5bbc8 !important;
}
</style>
<div style="font-size:small">
<?php
echo "<b>Local Update:</b> ". date("d M Y H:i:s",elgg_get_plugin_setting('local_update_time','check_for_update'))."<br>";
echo "<b>Github Update:</b> ". date("d M Y H:i:s",elgg_get_plugin_setting('github_update_time','check_for_update'))."<br>";
echo "<b>Current Time:</b> ". date("d M Y H:i:s")."<br>";
echo "<br>";
?>
</div>
<table class="elgg-list elgg-table elgg-newest-users" style="font-size:small">
  <thead>
    <tr>
      <th>Plugin ID</th>
      <th>Owner</th>
      <th>Github<br>Tag</th>
      <th>Github<br>Manifest</th>
      <th>Advance<br>Commits</th>
      <th>Installed</th>
      <th>Status</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
<?php 
$dbprefix = elgg_get_config('dbprefix');
$sql = "SELECT * FROM  `{$dbprefix}check_for_update` WHERE `check_update`='yes'";
$data = elgg()->db->getData($sql);
foreach ($data as $dbrow) {
  $plugin = elgg_get_plugin_from_id($dbrow->plugin_id);
  if($plugin == null){
    echo "<tr>";
    echo "<td><a href='".$dbrow->github_url."' target='_blank'>". $dbrow->plugin_id . "</a></td>";
    echo "<td>". $github_parts[3] ."</td>";
    echo "<td>". $dbrow->github_tag_name . "</td>";
    echo "<td>". $dbrow->github_manifest ."</td>";
    echo "<td>". $dbrow->github_adv_commit ."</td>";
  	echo "<td>". $dbrow->current_version . "</td>";
  	echo "<td><small class='label bg-gray'>Plugin Removed</small></td>";
  	echo "<td class='bg-gray'>Plugin Removed</td>";
  	echo "</tr>";
    continue;    
  }
  
  if(!is_dir( $plugin->getPath() )) {
    echo "<tr>";
    echo "<td><a href='".$dbrow->github_url."' target='_blank'>". $dbrow->plugin_id . "</a></td>";
    echo "<td>". $github_parts[3] ."</td>";
    echo "<td>". $dbrow->github_tag_name . "</td>";
    echo "<td>". $dbrow->github_manifest ."</td>";
    echo "<td>". $dbrow->github_adv_commit ."</td>";
  	echo "<td>". $dbrow->current_version . "</td>";
  	echo "<td><small class='label bg-gray'>Plugin Removed</small></td>";
  	echo "<td class='bg-gray'>Plugin Removed</td>";
  	echo "</tr>";
    continue;
  }
  $status = ($plugin->isActive() ? '<small class="label bg-green">Active</small>' : '<small class="label bg-red">Inactive</small>');
  if($dbrow->github_tag_name == $dbrow->current_version || $dbrow->github_manifest == $dbrow->current_version) {
    $action = "No action required";
    $class = "bg-green disabled";
  } else if($dbrow->github_tag_name > $dbrow->current_version || $dbrow->github_manifest > $dbrow->current_version){
    // if($dbrow->github_adv_commit > 0) {
    //   // button to update all commit
    // } else {
    //   // button to update till release
    // }
    $sync_url = elgg_generate_action_url('check_for_update/sync', [
  		'sync_type' => 'rel',
  		'plugin_id' => $dbrow->plugin_id,
  		'tag_name' => $dbrow->github_tag_name,
  	]);
    $action = elgg_view('output/url', [
    	'href' => $sync_url,
    	'text' => "Update release",
    	'title' => "Update release",
    	'is_trusted' => true,
    ]);
    $class = "bg-red disabled";
  } else if($dbrow->github_tag_name < $dbrow->current_version || $dbrow->github_manifest < $dbrow->current_version) {
    $action = "Updated plugin installed";
    $class = "bg-yellow disabled";
  }
  $github_parts = explode("/",$dbrow->github_url);
    echo "<tr>";
    echo "<td><a href='".$dbrow->github_url."' target='_blank'>". $dbrow->plugin_id . "</a></td>";
    echo "<td>". $github_parts[3] ."</td>";
    echo "<td>". $dbrow->github_tag_name . "</td>";
    echo "<td>". $dbrow->github_manifest ."</td>";
    echo "<td>". $dbrow->github_adv_commit ."</td>";
  	echo "<td>". $dbrow->current_version . "</td>";
  	echo "<td>". $status . "</td>";
  	echo "<td class='$class'>". $action . "</td>";
  	echo "</tr>";

}


 ?>
 </tbody>
 </table>
<hr>



<b>Follow Plugin are enabled for plugin update but does not have a github URL</b><br>
<table class="elgg-list elgg-table elgg-newest-users" style="font-size:small">
  <thead>
    <tr>
      <th>Plugin ID</th>
      <th>Owner</th>
      <th>Github<br>Tag</th>
      <th>Github<br>Manifest</th>
      <th>Advance<br>Commits</th>
      <th>Installed</th>
    </tr>
  </thead>
  <tbody>
<?php
$sql = "SELECT * FROM  `{$dbprefix}check_for_update` WHERE `check_update`='yes' AND `github_url` = ''";
$data = elgg()->db->getData($sql);
foreach ($data as $dbrow) {
    echo "<tr>";
    echo "<td>". $dbrow->plugin_id . "</td>";
    echo "<td></td>";
    echo "<td>". $dbrow->github_tag_name . "</td>";
    echo "<td>". $dbrow->github_manifest ."</td>";
    echo "<td>". $dbrow->github_adv_commit ."</td>";
  	echo "<td>". $dbrow->current_version . "</td>";
  	echo "</tr>";
}
?>
 </tbody>
 </table>
<hr>



<b>Follow Plugin are disabled for plugin update</b><br>
<table class="elgg-list elgg-table elgg-newest-users" style="font-size:small">
  <thead>
    <tr>
      <th>Plugin ID</th>
      <th>Author</th>
      <th>Github<br>Tag</th>
      <th>Github<br>Manifest</th>
      <th>Advance<br>Commits</th>
      <th>Installed</th>
    </tr>
  </thead>
  <tbody>
<?php
$sql = "SELECT * FROM  `{$dbprefix}check_for_update` WHERE `check_update`='no'";
$data = elgg()->db->getData($sql);
foreach ($data as $dbrow) {
    $plugin = elgg_get_plugin_from_id($dbrow->plugin_id);
      	
    if($plugin == null) {
        $author = "DELETED";
    } else {
      if(!is_dir($plugin->getPath())){
        $author = "DELETED";
      } else {
        $author = $plugin->getManifest()->getAuthor();
      }
    }
    
    $class = "";
    if (strpos(strtolower($author), strtolower('Core developers')) !== false) {
        $class = 'bg-yellow';
    } else if (strpos(strtolower($author), strtolower('Rohit')) !== false) {
        $class = 'bg-green';
    }
    
    echo "<tr>";
    echo "<td class='$class'>". $dbrow->plugin_id . "</td>";
    echo "<td class='$class'>$author</td>";
    echo "<td class='$class'>". $dbrow->github_tag_name . "</td>";
    echo "<td class='$class'>". $dbrow->github_manifest ."</td>";
    echo "<td class='$class'>". $dbrow->github_adv_commit ."</td>";
  	echo "<td class='$class'>". $dbrow->current_version . "</td>";
  	echo "</tr>";
}
?>
 </tbody>
 </table>
<hr>