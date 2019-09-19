<?php

return [
	// Configura a aplicação
	'application' => [
		'baseurl' => "",
		'basedomain' => "localhost",
		'errors' => [
			'module' => "main",
			'controller' => "errors",
			'display_errors' => TRUE
		],
	],
	
	// Configura o banco de dados
	'database' => [
		'type' => "pdo",
		'driver' => "postgresql",
		'host' => "",
		'database' => "",
		'username' => "",
		'password' => "",
	],
	
	// Rotas
	'routes' => [
	
		/*
			URL: /obrigatorio{/opcional}{/opcional}
		*/

		// Rota padrão
		'default' => [
			'pattern' => "/{module}{/controller}{/action}",
			'defaults' => [
				'module' => "main",
				'controller' => "index",
				'action' => "index",
			]
		],


	],

];
