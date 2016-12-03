<?php
namespace General\Mvc;

use General\Application\Services;
use General\Mvc\View;
use General\Application\Exception;

/**
 * Classe que trata as primeiras execuções do MVC
 */
class Front
{

	/**
	 * Armazena o request
	 */
	protected $_request;
	
	/**
	 * Armazena o module
	 */
	protected $_module;
	
	/**
	 * Armazena o controller
	 */
	protected $_controller;
	
	/**
	 * Armazena o action
	 */
	protected $_action;
	
	/**
	 * Armazena o view
	 */
	protected $_view;
	
	/**
	 * Construtor da classe
	 */
	public function __construct()
	{
		// Recupera o request
		$this->_request = Services::getInstance()->getService("request");
		
		// Instancia o view e configura o controller
		$view = new View();
		$this->setView($view);
	}

	/**
	 * Inicia o processamento do MVC
	 */
	public function run()
	{
		// Recupera os dados do MVC
		$this->setModule($this->_request->getParam("module"));
		$this->setController($this->_request->getParam("controller"));
		$this->setAction($this->_request->getParam("action"));
		
		// Instancia o bootstrap do modulo
		$moduleBootstrapName = "\\" . ucfirst($this->getModule()) . "\\Bootstrap";
		try {
			$moduleBootstrap = new $moduleBootstrapName();
		}
		catch(Exception $e) {
			die("Modulo não encontrado");
		}
		
		
		// Instancia o controlador
		$controllerName = "\\" . ucfirst($this->getModule()) . "\\Controller\\" . ucfirst($this->getController() . "Controller");
		try {
			$controller = new $controllerName();
		}
		catch(Exception $e) {
			// @todo Adicionar tradução
			throw new Exception("Não foi possivel encontrar o controlador $controllerName");
		}
		
		// Verifica se o método existe
		$actionName = $this->getAction() . "Action";
		if(!method_exists($controller, $actionName)) {
			// @todo Adicionar tradução
			throw new Exception("Não foi possivel disparar a ação $actionName do controller $controllerName");
		}
		
		// Instancia o view e configura o controller
		$view = $this->getView();
		$view->setTemplateDir(APPLICATION_PATH . "/src/" . ucfirst($this->getModule()) . "/View");
		$view->setTemplate("template.tpl");
		$view->setLayout($this->getController() . "/" . $this->getAction() . ".tpl");
		$controller->setView($view);
		
		// Executa o método
		$controller->$actionName();
		
		// Retorna o output de todo processamento
		$output = $view->execute();
		$view->display($output);
	}
	
	/**
	 * Seta o view
	 */
	public function setView($view)
	{
		$this->_view = $view;
	}
	
	/**
	 * Recupera o view
	 */
	public function getView()
	{
		return $this->_view;
	}
	
	/**
	 * Seta o nome do modulo
	 * 
	 * @param $name String Nome do módulo
	 */
	public function setModule($name)
	{
		$this->_module = $name;
	}
	
	/**
	 * Recupera o nome do modulo
	 */
	public function getModule()
	{
		return $this->_module;
	}
	
	/**
	 * Seta o nome do controlador
	 *
	 * @param $name String Nome do controlador
	 */
	public function setController($name)
	{
		$this->_controller = $name;
	}
	
	/**
	 * Recupera o nome do controlador
	 */
	public function getController()
	{
		return $this->_controller;
	}
	
	/**
	 * Seta o nome da ação
	 *
	 * @param $name String Nome da ação
	 */
	public function setAction($name)
	{
		$this->_action = $name;
	}
	
	/**
	 * Recupera o nome da ação
	 */
	public function getAction()
	{
		return $this->_action;
	}
}