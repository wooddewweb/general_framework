<?php
namespace General\Mvc;

use General\Application\Services;
use General\Application\Exception;

/**
 * Classe que trata as rotas
 */
class Routes
{

	/**
	 * Armazena a configuração
	 */
	protected $config;

	/**
	 * Armazena as rotas
	 */
	protected $routes;

	/**
	 * Armazena os parametros
	 */
	protected $params = [];

	/**
	 * Construtor da classe
	 * 
	 * @param string $uri URI para testar a rota
	 */
	public function __construct($uri)
	{
		$this->config = Services::getInstance()->getService("config");
		$this->routes = $this->config->routes;
		
		// Percorre as rotas configuradas
		foreach ($this->getRoutes() as $route_name => $route) {
			$pattern = $route['pattern'];
			
			// Testa o pattern para ver se a URI pertence à alguma rota
			$test = $this->testExpression($pattern, $uri);
			if ($test) {
				break;
			}
		}
		
		// Se nao tem roda, utiliza a rota padrão
		if (! $test) {
			$route_name = "default";
		}
		
		
		// Tenta recuperar os dados da rota
		try {
			$route = $this->getRoutes()->$route_name;
		}
		catch(Exception $e) {
			// @todo Adicionar tradução
			throw new Exception("Rota default não encontrada");
		}
		
		
		// Verifica os parametros encontrados
		foreach ($route->defaults as $param => $default) {
			if (! $this->params[$param]) {
				$this->params[$param] = $default;
			}
		}
	}

	/**
	 * Recupera as rotas
	 */
	public function getRoutes()
	{
		return $this->routes;
	}

	/**
	 * Recupera a tora
	 */
	public function getRoute($name)
	{
		try {
			return $this->routes->$name;
		}
		catch (Exception $e) {
			// @todo Adicionar tradução
			throw new Exception("Route {$name} not found");
		}
	}
	
	/**
	 * Monta uma url com base na rota
	 */
	public function getUrlFromRoute($route_name, $params)
	{
		$route =  $this->getRoute($route_name);
		$url = $route_expression = $route->pattern;
		
		// Faz a busca pelas chaves { e }
		preg_match_all("/\{(\/)?([\w|\-|\.]+)\}/i", $route_expression, $param_names);
		foreach ($param_names[0] as $index => $param_name) {
				
			$outparam = $param_name = str_replace("{", "", str_replace("}", "", $param_name));
				
			// Verifica se possui parametro opcional, para colocar a barra dentro da condição,
			//		fazendo o teste passar sem barra na frente
			$optional = "";
			if($param_name[0] == "/") {
				$optional = "/";
		
				// Remove o / do inicio do parametro
				$outparam = substr($param_name, 1);
			}
			
			$param_value = $params[$outparam];
			if(!$param_value) {
				try {
					$param_value = $route->defaults->$outparam;
				}
				catch(Exception $e) {
					// @todo Adicionar tradução
					throw new Exception("Parametro {$outparam} da rota {$route_name} não definido");
				}
			}
			
			// Substitui o marcador pelo valor
			$url = str_replace("{" . $param_name . "}", $optional . $param_value, $url);
		}
		
		// Adiciona o basepath
		$url = $this->config->application->baseurl . $url;
		
		return $url;
	}

	/**
	 * Recupera os parametros
	 * 
	 * @return array
	 */
	public function getParams()
	{
		return $this->params;
	}
	
	/**
	 * Seta um parametro
	 *
	 * @param $name String Nome do parametro
	 * @param $value String Valor para o parametro
	 */
	public function setParam($name, $value)
	{
		$this->params[$name] = $value;
	}

	/**
	 * Converte a expressão da rota para expressão regular
	 */
	public function testExpression($route_expression, $uri)
	{
		$params = NULL;
		
		// Faz a busca pelas chaves { e }
		preg_match_all("/\{(\/)?([\w|\-|\.]+)\}/i", $route_expression, $param_names);
		foreach ($param_names[0] as $index => $param_name) {
			
			$outparam = $param_name = str_replace("{", "", str_replace("}", "", $param_name));
			
			// Verifica se possui parametro opcional, para colocar a barra dentro da condição, 
			//		fazendo o teste passar sem barra na frente
			$optional = "";
			if($param_name[0] == "/") {
				$optional = "/";
				
				// Remove o / do inicio do parametro
				$outparam = substr($param_name, 1);
			}
			
			// Substitui o marcador por uma expressão regular
			$route_expression = str_replace("{" . $param_name . "}", "(" . $optional . "(?<" . $outparam . ">[\w|\-|\.]+))?", $route_expression);
		}
		
		// Adiciona a busca pelo restante de parametros
		// @todo Adicionar esta parte somente com {*} na expressão
		$route_expression = str_replace("/", "\/", $route_expression) . "(?<params>.*)?";
		
		// Busca os valores da URL com base na expressão montada
		$test = preg_match("/" . $route_expression . "/", $uri, $matches);
		
		
		if ($test) {
			$params = [];
			
			foreach ($param_names[0] as $param) {
				
				$outparam = $param_name = str_replace("{", "", str_replace("}", "", $param));
				
				// Verifica se possui parametro opcional
				if($param_name[0] == "/") {
					// Remove o / do inicio do parametro
					$outparam = substr($param_name, 1);
				}
				
				// Adiciona o parametro
				$this->setParam($outparam, $matches[$outparam]);
			}
			
			// Percorre o restante dos parametros
			preg_match_all("/\/(?<param>[\w|\-|\.]+)\/(?<value>[\w|\-|\.]+)/", $matches['params'], $matches);
			foreach ($matches['param'] as $index => $param) {
				// Adiciona o parametro
				$this->setParam($param, $matches['value'][$index]);
			}
		}
		
		// Retorna se satisfez alguma rota
		return $test;
	}
}