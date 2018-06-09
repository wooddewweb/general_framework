<?php
namespace General\Mvc;

use General\Application\Services;
/**
 * Classe que trata as requisições do MVC
 */
class Request
{
	/**
	 * Armazena os parametros
	 */
	protected $params = [];
	
	/**
	 * Armazena o caminho padrão da url
	 */
	protected $_basepath = [];
	
	/**
	 * Armazena o objeto da rota
	 */
	public $route;
	
	/**
	 * Construtor da classe
	 */
	public function __construct()
	{
		// Configura o basepath
		$this->setBasePath();
	}

	/**
	 * Faz o parse da URL para recuperar os parametros
	 */
	public function parseParams($uri = NULL)
	{
		// Verifica se possui URI, se não usa a do navegador
		if (! $uri) {
			$path_info = !empty($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : (!empty($_SERVER['ORIG_PATH_INFO']) ? $_SERVER['ORIG_PATH_INFO'] : '');
			$url_parsed = parse_url($path_info);
			$uri = $url_parsed['path'];
		}
		
		// Inicia a rota corrente
		$this->route = new \General\Mvc\Routes($uri);
		
		// Recupera os parametros da rota
		$this->params = $this->route->getParams();
	}
	
	/**
	 * Recupera o objeto da rota
	 */
	public function getRoute()
	{
		return $this->route;
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
	 * Recupera um parametro
	 * 
	 * @param $name String Nome do parametro
	 * @param $default String Valor padrão para o parametro
	 */
	public function getParam($name, $default=NULL)
	{
		// Verifica se o parametro existe
		if(isset($this->params[$name])) {
			return $this->params[$name];
		}
		elseif(isset($_GET[$name])) {
			return $_GET[$name];
		}
		elseif(isset($_POST[$name])) {
			return $_POST[$name];
		}
		
		return $default;
	}
	
	/**
	 * Recupera os parametros
	 */
	public function getParams()
	{
		return $this->params;
	}
	
	/**
	 * Seta o caminho base
	 * 
	 * @param string $basepath Caminho base
	 */
	public function setBasePath($basepath=NULL)
	{
		if($basepath === NULL) {
			$basepath = Services::getInstance()->getService("config")->application->baseurl;
		}
		
		// Verifica se possui barra no final do baseurl
		if($basepath[strlen($basepath) - 1] == "/") {
			$basepath = substr($basepath, 0, strlen($basepath) - 1);
		}
		
		$this->_basepath = $basepath;
	}
	
	/**
	 * Retorna o caminho base
	 *
	 * @return string
	 */
	public function getBasePath()
	{
		return ($this->_basepath != NULL) ? $this->_basepath : "";
	}
	
	/**
	 * Retorna um indice do _SERVER
	 *
	 * @param string $key Indice
	 * @param mixed $default Valor padrão caso não encontrado
	 * @return mixed Retorna null se não encontrado, o valor, ou todo o _SERVER se não tiver $key
	 */
	public function getServer($key = null, $default = null)
	{
		if (null === $key) {
			return $_SERVER;
		}
		
		return (isset($_SERVER[$key])) ? $_SERVER[$key] : $default;
	}
	
	/**
	 * Retorna o método de requisição
	 *
	 * @return string
	 */
	public function getMethod()
	{
		return $this->getServer("REQUEST_METHOD");
	}
	
	/**
	 * Verifica se é uma requisição POST
	 *
	 * @return boolean
	 */
	public function isPost()
	{
		return ($this->getMethod() === "POST");
	}
	
	/**
	 * Verifica se é uma requisição GET
	 *
	 * @return boolean
	 */
	public function isGet()
	{
		return ($this->getMethod() === "GET");
	}
	
	/**
	 * Verifica se é uma requisição PUT
	 *
	 * @return boolean
	 */
	public function isPut()
	{
		return ($this->getMethod() === "PUT");
	}
	
	/**
	 * Verifica se é uma requisição DELETE
	 *
	 * @return boolean
	 */
	public function isDelete()
	{
		return ($this->getMethod() === "DELETE");
	}
	
	/**
	 * Verifica se é uma requisição HEAD
	 *
	 * @return boolean
	 */
	public function isHead()
	{
		return ($this->getMethod() === "HEAD");
	}
	
	/**
	 * Verifica se é uma requisição OPTIONS
	 *
	 * @return boolean
	 */
	public function isOptions()
	{
		return ($this->getMethod() === "OPTIONS");
	}
	
	/**
	 * Verifica se é uma requisição ajax
	 *
	 * @return boolean
	 */
	public function isXmlAjax()
	{
		return ($this->getHeader("X_REQUESTED_WITH") === "XMLHttpRequest");
	}
	
	/**
	 * Retorna se é https
	 *
	 * @return boolean
	 */
	public function isSsl()
	{
		return ($this->getScheme() === "https");
	}
	
	/**
	 * Retorna os dados Raw
	 *
	 * @return string|false
	 */
	public function getRawBody()
	{
		$body = file_get_contents("php://input");
		
		if (strlen(trim($body)) > 0) {
			$this->_rawBody = $body;
		} else {
			$this->_rawBody = false;
		}
	}
	
	/**
	 * Efetua o redirect
	 * 
	 * @param string $url URL para dar o redirect
	 */
	public function redirect($url)
	{
		// Verifica o http
		if(strpos($url, "http://") !== FALSE) {
			header("Location: " . $url);
			exit();
		}
		
		// Verifica o https
		if(strpos($url, "https://") !== FALSE) {
			header("Location: " . $url);
			exit();
		}
		
		// Verifica se possui barra no inicio da url
		if($url[0] == "/") {
			$url = substr($url, 1);
		}
		
		header("Location: " . $this->getBasePath() . "/" . $url);
		exit();
	}
}