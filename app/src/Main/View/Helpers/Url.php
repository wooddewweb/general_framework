<?php

namespace Main\View\Helpers;

use General\Mvc\ViewHelpers\Helper;
use General\Application\Services;
use General\Application\Exception;

/**
 * Classe abstrata para os viewhelpers
 *
 * @author Bruno P. Gonçalves
 */
class Url extends Helper
{
	/**
	 * Executa o helper
	 * 
	 * @return string
	 */
	public function call($params, $route_name)
	{
		// Recupera as rotas e configurações
		$this->config = Services::getInstance()->getService("config");
		$routes = $this->config->routes;
		$baseurl = $this->config->application->baseurl;

		// Armazena os parametros para adicionar o restante nos adicionais
		$params_additionals = $params;

		// Remove a ultima barra caso houver
		if($baseurl[strlen($baseurl) - 1] == "/") {
			$baseurl = substr($baseurl, 0, strlen($baseurl) - 1);
		}
		
		// Tenta recuperar os dados da rota
		try {
			$route = $routes->$route_name;
		}
		catch(Exception $e) {
			// @todo Adicionar tradução
			throw new Exception("Rota default não encontrada");
		}
		
		// Armazena o pattern
		$uri = $route->pattern;
		
		// Faz a busca pelas chaves { e } no pattern
		preg_match_all("/\{(\/)?([\w|\-|\.]+)\}/i", $uri, $param_names);
		
		foreach ($param_names[0] as $index => $pattern_name) {
			
			$param_name = substr($pattern_name, 1, strlen($pattern_name)-2);
			$divisor = "";
			if($param_name[0] == "/") {
				$param_name = substr($param_name, 1);
				$divisor = "/";
			}
			
			// Verifica se possui parametro
			if(isset($params[$param_name])) {
				$param_value = $params[$param_name];
				unset($params_additionals[$param_name]);
			}
			
			// Verifica se possui parametro opcional
			else {
			
				try {
					$param_value = $route->defaults->$param_name;
				}
				catch(Exception $e) {
					// @todo Adicionar tradução
					throw new Exception("Parametro $param_name obrigatório");
				}
				
			}
			$uri = str_replace($pattern_name, $divisor . $param_value, $uri);
		}

		// Adiciona os parametros adicionais
		foreach($params_additionals as $param => $value) {
			$uri .= "/" . $param . "/" . $value;
		}
		
		// Retorna o uri
		return $baseurl . $uri;
	}
}