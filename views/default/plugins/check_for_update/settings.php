<?php
$token_string = elgg_echo('Github Token');
$token_view = elgg_view('input/text', [
	'name' => 'params[token]',
	'value' => $vars['entity']->token,
	'class' => 'text_input',
]);

$username_string = elgg_echo('Github Username [Example: user@domain.com]');
$username_view = elgg_view('input/text', [
	'name' => 'params[git_username]',
	'value' => $vars['entity']->git_username,
	'class' => 'text_input',
]);

$settings = <<<__HTML
<!-- <div>$client_id_string $client_id_view</div> -->
<!-- <div>$client_secret_string $client_secret_view</div> -->
<div>$token_string $token_view</div>
<div>$username_string $username_view</div>
__HTML;

echo $settings;
