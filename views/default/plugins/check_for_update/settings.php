<?php
$client_id_string = elgg_echo('Github Client ID');
$client_id_view = elgg_view('input/text', [
	'name' => 'params[client_id]',
	'value' => $vars['entity']->client_id,
	'class' => 'text_input',
]);

$client_secret_string = elgg_echo('Github Client Secret');
$client_secret_view = elgg_view('input/text', [
	'name' => 'params[client_secret]',
	'value' => $vars['entity']->client_secret,
	'class' => 'text_input',
]);

$token_string = elgg_echo('Github Token');
$token_view = elgg_view('input/text', [
	'name' => 'params[token]',
	'value' => $vars['entity']->token,
	'class' => 'text_input',
]);

$settings = <<<__HTML
<div>$client_id_string $client_id_view</div>
<div>$client_secret_string $client_secret_view</div>
<div>$token_string $token_view</div>
__HTML;

echo $settings;
