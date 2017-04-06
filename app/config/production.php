<?php

return array(
	// Configura a aplicação
	'application' => array(
		'baseurl' => ""
	),
	
	// Configura o banco de dados
	'database' => array(
		'type' => "pdo",
		'driver' => "postgresql",
		'filename' => APPLICATION_PATH . "/data/database.sqlite"
	),
	
	// Rotas
	'routes' => array(
	
		/*
			URL: /obrigatorio{/opcional}{/opcional}
		*/
		// Rota padrão
		'default' => array(
			'pattern' => "/{module}{/controller}{/action}",
			'defaults' => array(
				'module' => "main",
				'controller' => "index",
				'action' => "index",
			)
		)
	),
);
