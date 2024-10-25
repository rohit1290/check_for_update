<?php
$token_view = elgg_view_field([
	'#label' => elgg_echo('Github Token'),
	'#type' => 'text',
	'name' => 'params[token]',
	'value' => $vars['entity']->token,
	'class' => 'text_input',
]);

$username_view = elgg_view_field([
	'#label' => elgg_echo('Github Username [Example: user@domain.com]'),
	'#type' => 'text',
	'name' => 'params[git_username]',
	'value' => $vars['entity']->git_username,
	'class' => 'text_input',
]);

$settings = <<<__HTML
<div>$token_view</div>
<div>$username_view</div>
__HTML;

echo $settings;
