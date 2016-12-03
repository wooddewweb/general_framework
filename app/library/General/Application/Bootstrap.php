<?php

namespace General\Application;

use General\Application\Services;
use General\Mvc\Request;
use General\Mvc\Front;

/**
 * Procedimentos de inicialização da aplicação
 */
class Bootstrap
{
	/**
	 * Inicializa a aplicação
	 */
	public function __construct()
	{
		// @todo Verificar qual o tipo de arquivo para enviar ao minify ou ao image cache
		
		// Inicia o autoloader
		spl_autoload_register(array($this, "__autoloader"));
		
		// Inicia o uso de sessões
		session_start();
		
		// Cria a configuração
		$configFile = APPLICATION_PATH . "/config/production.php";
		$config = Config::createInstance($configFile);
		
		// Verifica se está em outro ambiente
		if(APPLICATION_ENV != "production") {
			$envConfig = APPLICATION_PATH . "/config/" . APPLICATION_ENV . ".php";
			if(file_exists($envConfig)) {
				$config->mergeConfig($envConfig);
			}
		}
		
		// Adiciona a configuração ao serviços
		Services::getInstance()->addService("config", $config);
		
		// Cria o request e adiciona ao service manager
		$request = new Request();
		Services::getInstance()->addService("request", $request);
		$request->parseParams();
		
		// Cria o front controller
		$front = new Front();
		Services::getInstance()->addService("front", $front);
		$front->run();
	}
	
	/**
	 * Autoloader
	 */
	private function __autoloader($className)
	{
		
		// Percorre os paths para busca
		$paths = explode(":", ini_get('include_path'));
		foreach($paths as $path) {
			
			// Verifica se o arquivo existe
			$filename = $path . "/" . implode("/", explode("\\", $className)) . ".php";
			if (file_exists($filename)) {
				// Inclui o arquivo para o uso
				require_once ($filename);

				// Retorna para nao passar pelo Exception
				return TRUE;
			}
		}
		
		// @todo Adicionar tradução
		if(!class_exists($className)) {
			//throw new Exception("Classe $className ($filename) não encontrada");
		}
	}
}