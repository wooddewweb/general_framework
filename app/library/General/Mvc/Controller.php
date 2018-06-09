<?php

namespace General\Mvc;

use General\Application\Services;

/**
 * Controlador MCV padrão do framework
 */
class Controller
{
	/**
	 * Armazena o request
	 */
	protected $_request;
	
	/**
	 * Armazena o view
	 */
	protected $_view = NULL;
	
	/**
	 * Construtor da classe
	 */
	public function __construct()
	{
		// Armazena o request
		$this->setRequest(Services::getInstance()->getService("request"));
		
		// Instancia o view
		$this->_view = new \General\Mvc\View();
		
		// Executa o hook de inicialização
		$this->configure();

		// Instancia o helper
		$this->helper = new ControllerHelper($this);
	}
	
	/**
	 * Seta o view
	 */
	public function setView($view)
	{
		$this->_view = $view;
	}
	
	/**
	 * Retorna o view
	 */
	public function view()
	{
		return $this->_view;
	}
	
	/**
	 * Método padrão para o hook
	 */
	public function configure()
	{
	}
	
	/**
	 * Set request
	 */
	public function setRequest(Request $request)
	{
		$this->_request = $request;
	}
	
	/**
	 * Recupera o request
	 */
	public function getRequest()
	{
		return $this->_request;
	}
	
	/**
	 * Recupera o parametro
	 */
	public function getParam($param, $default="")
	{
		return $this->_request->getParam($param, $default);
	}
	
	/**
	 * Faz o redirect 
	 * 
	 * @TODO Verificar metodos com _ no começo, e verificar se existe helpers
	 */
	public function _redirect($url)
	{
		header("Location: " . $url);
		exit(0);
	}
}