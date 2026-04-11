<?php
echo elgg_view_field([
	'#label' => elgg_echo('check_for_update:settings:token'),
	'#type' => 'text',
	'name' => 'params[token]',
	'value' => $vars['entity']->token,
	'class' => 'text_input',
]);

echo elgg_view_field([
	'#label' => elgg_echo('check_for_update:settings:username'),
	'#type' => 'text',
	'name' => 'params[git_username]',
	'value' => $vars['entity']->git_username,
	'class' => 'text_input',
]);