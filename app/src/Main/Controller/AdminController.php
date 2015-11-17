<?php

namespace Main\Controller;

use Main\Model;
use General\Application\Sessions;

/**
 * Controlador padrão
 *
 * @author Bruno P. Gonçalves
 */
class AdminController extends \General\Mvc\Controller
{
	/**
	 */
	public function configure()
	{
		$this->usuario = new Sessions("usuario");
	}
	
	/**
	 * Ação do frame do chat
	 */
	public function chatAction()
	{
		$this->view()->disableTemplate();
		
		// Recupera o app_key
		$app_key = $this->_request->getParam("app_key");
		$idsessao = $this->_request->getParam("idsessao", 0);
		
		// Verifica se ja tem sessão aberta
		if(!($this->usuario->idusuario > 0)) {
			// Redireciona
 			$this->_redirect($this->view()->basePath() . "/admin/login/" . $app_key);
		}
		
		// Recupera o dominio a partir do app_key
		$model = new Model\Dominios();
		$dominio = $model->fetchRow(array('app_key = ?' => $app_key));
		if(!$dominio) {
			$this->_redirect($this->view()->basePath() . "/admin/denied/" . $app_key);
		}
		
		// Busca as mensagens da sessão
		$model = new Model\Sessoesmensagens();
		$select = $model->select()
			->from("sessoes_mensagens", array())
			->join("sessoes", "sessoes.idsessao = sessoes_mensagens.idsessao", array())
			->join("dominios", "dominios.iddominio = sessoes.iddominio", array())
			->joinLeft("usuarios", "usuarios.idusuario = sessoes_mensagens.idusuario", array())
			->columns(array(
				"sessoes_mensagens.*",
				'atendimento' => "COALESCE(usuarios.nome, sessoes.nome)"
			))
			->where("sessoes.idsessao = ?", $idsessao)
			->where("app_key = ?", $app_key)
			->order("data_envio ASC");
		$mensagens = $model->fetchAll($select);
		
		// Assina as variaveis
		$this->view()->app_key = $app_key;
		$this->view()->idsessao = $idsessao;
		$this->view()->mensagens = $mensagens;
	}
	
	/**
	 * Ação do frame principal
	 */
	public function sessoesAction()
	{
		$this->view()->disableTemplate();
		
		// Recupera o app_key
		$app_key = $this->_request->getParam("app_key");
		
		// Verifica se ja tem sessão aberta
		if(!($this->usuario->idusuario > 0)) {
			// Redireciona
 			$this->_redirect($this->view()->basePath() . "/admin/login/" . $app_key);
		}
		
		// Recupera o dominio a partir do app_key
		$model = new Model\Dominios();
		$dominio = $model->fetchRow(array('app_key = ?' => $app_key));
		if(!$dominio) {
			$this->_redirect($this->view()->basePath() . "/admin/denied/" . $app_key);
		}
		
		// Busca os clientes em espera
		$model = new Model\Sessoes();
		$sessoes = $model->fetchAll(array('iddominio = ?' => $dominio['iddominio'], "ativa = TRUE"), "data_criacao DESC");
		
		// Assina as variaveis
		$this->view()->app_key = $app_key;
		$this->view()->sessoes = $sessoes;
	}
	
	/**
	 * Ação do denied
	 */
	public function deniedAction() {}
	
	/**
	 * Ação do login
	 */
	public function loginAction()
	{
		$this->view()->disableTemplate();
		
		// Recupera os parametros
		$app_key = $this->_request->getParam("app_key");
		
		// Verifica se ja tem sessão aberta
		if($this->usuario->idusuario > 0) {
			// Redireciona
 			$this->_redirect($this->view()->basePath() . "/admin/sessoes/" . $app_key);
		}
		
		// Verifica se tem dados no post
		if($this->_request->isPost()) {
			// Recupera os parametros de login
			$email = $this->_request->getParam("email");
			$senha = $this->_request->getParam("senha");
		
			// Recupera o dominio a partir do app_key
			$model = new Model\Dominios();
			$dominio = $model->fetchRow(array('app_key = ?' => $app_key));
			if(!$dominio) {
				// Redireciona
 				$this->_redirect($this->view()->basePath() . "/denied/" . $app_key);
			}
		
			// Efetua o login
			$model = new Model\Usuarios();
			$usuario = $model->fetchRow(array('email = ?' => $email, 'senha = ?' => md5($senha)));
			if(!$usuario) {
				die(json_encode(array('error'=>'0002', 'message'=>"Usuário ou senha inválido")));
			}
		
			// Cria a sessão do usuário
			$this->usuario->logado = TRUE;
			$this->usuario->idusuario = $usuario['idusuario'];
			$this->usuario->nome = $usuario['nome'];
			
			
			die(json_encode(array('result' => TRUE)));
		}
		
		// Assina as variaveis
 		$this->view()->app_key = $app_key;
	}
}