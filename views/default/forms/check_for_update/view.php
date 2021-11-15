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
echo "<b>Github Update:</b> ". date("d M Y H:i:s", elgg_get_plugin_setting('github_update_time', 'check_for_update'))."<br>";
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
	  <th>Github<br>Composer</th>
	  <th>Advance<br>Commits</th>
	  <th>Installed</th>
	  <th>Status</th>
	  <th>Action</th>
	</tr>
  </thead>
  <tbody>
    <?php
    $plugins = pluginGetList();
    foreach ($plugins as $plugin_id => $plugin) {
    	echo "<tr>";
      if ($plugin['github_url'] == "") {
        echo "<td>". $plugin['id'] . "</td>";
      } else {
        echo "<td><a href='".$plugin['github_url']."' target='_blank'>". $plugin['id'] . "</a></td>";
      }
    	echo "<td>". $plugin['owner'] ."</td>";
    	echo "<td>". $plugin['github_tag_name'] . "</td>";
    	echo "<td>". $plugin['github_composer'] . "</td>";
    	echo "<td>". $plugin['github_adv_commit'] ."</td>";
    	  echo "<td>". $plugin['current_version'] . "</td>";
    	  echo "<td>". $plugin['status'] . "</td>";
    	  echo "<td class='{$plugin['class']}'>". $plugin['action'] . "</td>";
    	  echo "</tr>";
    }
	  ?>
 </tbody>
 </table>
<hr>
