<?php

namespace Main\View\Helpers;

use General\Mvc\ViewHelpers\Helper;
use General\Application\Services;

/**
 * Classe abstrata para os viewhelpers
 *
 * @author Bruno P. Gonçalves
 */
class Resources extends Helper
{
	/**
	 * Executa o helper
	 * 
	 * @return string
	 */
	public function call($type)
	{
		$output = "";
		
		// Recupera o request
		$request = Services::getInstance()->getService("request");
		
		// Recupera o config
		$this->config = Services::getInstance()->getService("config");
		$baseurl = $this->config->application->baseurl;
		
		// Recupera o path do mvc
		$module = $request->getParam("module");
		$controller = $request->getParam("controller");
		$action = $request->getParam("action");
		
		// Verifica se é javascript
		if($type == "javascript") {
			// Cria o caminho local
			$path = "public/js/" . $controller . "/" . $action . ".js";
			$local_path = APPLICATION_PATH . "/../public_html/" . $path;
			if(file_exists($local_path)) {
				$output = "<script type=\"text/javascript\" src=\"" . $baseurl . $path . "\"></script>";
			}
		}
		elseif($type == "css") {
			// Cria o caminho local
			$path = "public/css/" . $controller . "/" . $action . ".css";
			$local_path = APPLICATION_PATH . "/../public_html/" . $path;
			if(file_exists($local_path)) {
				$output = "<link rel=\"stylesheet\" href=\"" . $baseurl . $path . "\">";
			}
			
		}
		
		return $output;
	}
}