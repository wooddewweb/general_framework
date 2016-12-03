<?php

namespace General\Mvc;

// Inclui o objeto smarty
require_once (APPLICATION_PATH . "/library/vendors/Smarty/Smarty.class.php");

use General\Mvc\Request;
use General\Application\Services;

/**
 * Classe que as ações do view
 */
class View
{
	/**
	 * Armazena o objeto smarty
	 */
	protected $_smarty = NULL;
	
	/**
	 * Armazena o nome do arquivo layout
	 */
	protected $_layoutName = "";
	
	/**
	 * Armazena o nome do arquivo template
	 */
	protected $_templateName = "";
	
	/**
	 * Armazena o conteudo do layout
	 */
	public $layoutContent;
	
	/**
	 * Armazena o request
	 */
	protected $_request;
	
	/**
	 * View Helper
	 */
	public $helper;
	
	/**
	 * Construtor da classe
	 */
	public function __construct()
	{
		// Armazena o request
		$this->_request = Services::getInstance()->getService("request");
		
		// Cria o objeto smarty
		$this->_smarty = new \Smarty();
		
		// @todo Buscar configuração do arquivo de configuração
		$this->_smarty->force_compile = false;
		$this->_smarty->debugging = false;
		$this->_smarty->caching = false;
		$this->_smarty->cache_lifetime = 600;
		$this->_smarty->compile_check = true;
		$this->_smarty->setTemplateDir(APPLICATION_PATH . "/src/Main/View");
		$this->_smarty->setCompileDir(APPLICATION_PATH . "/data/templates/compiled");
		$this->_smarty->setCacheDir(APPLICATION_PATH . "/data/templates/cached");
		
		// Instancia o helper
		$this->helper = new ViewHelper($this);
		
		// Assina o proprio view
		$this->assign("this", $this);
	}
	
	/**
	 * Recupera o baseUrl, chamado de basePath para manter compatibilidade
	 */
	public function basePath()
	{
		return $this->_request->getBasePath();
	}
	
	/**
	 * Recupera o baseDomain
	 */
	public function baseDomain()
	{
		return Services::getInstance()->getService("config")->application->basedomain;
	}
	 
	
	/**
	 * Seta o arquivo de layout
	 */
	public function setLayout($filename)
	{
		$this->_layoutName = $filename;
	}
	
	/**
	 * Recupera o arquivo de layout
	 */
	public function getLayout()
	{
		return $this->_layoutName;
	}
	
	/**
	 * Desabilita o template
	 */
	public function disableTemplate()
	{
		$this->_templateName = FALSE;
	}
	
	/**
	 * Seta o arquivo de template
	 */
	public function setTemplate($filename)
	{
		$this->_templateName = $filename;
	}
	
	/**
	 * Recupera o arquivo de template
	 */
	public function getTemplate()
	{
		return $this->_TemplateName;
	}
	
	/**
	 * Recupera o request
	 */
	public function getRequest()
	{
		return $this->_request;
	}
	
	/**
	 * Faz o parse do template
	 */
	public function execute()
	{
		// Verifica se possui layout
		if($this->_templateName !== FALSE) {
			$this->layoutContent = $this->render($this->_layoutName);
			return $this->render($this->_templateName);
		}
		else {
			return $this->render($this->_layoutName);
		}
	}
	
	// @todo Remover toda essa parte para um objeto smarty proprio, removendo ele do View
	
	/**
	 * Método que assina as variaveis para o template
	 */
	public function assign($spec, $value = NULL)
	{
		if (is_array($spec)) {
			$this->_smarty->assign($spec);
			return;
		}
		$this->_smarty->assign($spec, $value);
	}
	
	/**
	 * Verifica se o template está em cache
	 */
	public function isCached($template)
	{
		// Verifica se o template está em cache
		if ($this->_smarty->is_cached($template)) {
			return TRUE;
		}
		return FALSE;
	}
	
	/**
	 * Seta se deve usar cache
	 */
	public function setCaching($caching)
	{
		// Configura o smarty
		$this->_smarty->caching = $caching;
	}
	
	/**
	 * Retorna o engine para o zend
	 */
	public function getEngine()
	{
		return $this->_smarty;
	}
	
	/**
	 * Método magico que seta valores nas propriedades
	 */
	public function __set($key, $val)
	{
		$this->_smarty->assign($key, $val);
	}
	
	/**
	 * Método magico que retorna valores nas propriedades
	 */
	public function __get($key)
	{
		return $this->_smarty->getTemplateVars($key);
	}
	
	/**
	 * Método que retorna o conteudo do template
	 */
	public function fetch($template)
	{
		return $this->_smarty->fetch($template);
	}
	
	/**
	 * Método magico que verifica se a variavel existe
	 */
	public function __isset($key)
	{
		return $this->_smarty->getTemplateVars($key) != NULL;
	}
	
	/**
	 * Método magico que remove a assinatura de uma variavel
	 */
	public function __unset($key)
	{
		$this->_smarty->clear_assign($key);
	}
	
	/**
	 * Seta o diretório onde deve procurar pelo template
	 */
	public function setTemplateDir($dir)
	{
		$this->_smarty->template_dir = array(
			$dir
		);
	}
	
	/**
	 * Renderiza o tempalte
	 */
	public function render($name)
	{
// 		// Percorre os caminhos onde podem ter templates
// 		foreach ($this->_smarty->template_dir as $path) {
// 			// Verifica se existe / no final do path
// 			if ($path[strlen($path) - 1] != "/") {
// 				$path .= "/";
// 			}
			
// 			// Verifica se o arquivo do template existe
// 			if (! file_exists($name)) {
// 				// Monta o novo nome do template
// 				$filename = basename($name);
// 				$name = $path . $filename;
// 			} else {
// 				// Finaliza a busca
// 				break;
// 			}
// 		}
		
		// Retorna o template
		return $this->_smarty->fetch($name);
	}
	
	/**
	 * Renderiza o arquivo setado
	 */
	public function display($output)
	{
		// Mostra o conteudo
		echo $output;
	}
}