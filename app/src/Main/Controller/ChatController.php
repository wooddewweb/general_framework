<?php

namespace Main\Controller;

use Main\Model;
use General\Application\Sessions;

/**
 * Controlador padrão
 *
 * @author Bruno P. Gonçalves
 */
class ChatController extends \General\Mvc\Controller
{
	/**
	 */
	public function configure()
	{
		$this->usuario = new Sessions("usuario");
	}
	
	/**
	 * Abre uma nova sessão de chat
	 */
	public function openAction()
	{
		$this->view()->disableTemplate();
		
		// Recupera o app_key
		$app_key = $this->_request->getParam("app_key");
		
		// Recupera o dominio a partir do app_key
		$model = new Model\Dominios();
		$dominio = $model->fetchRow(array('app_key = ?' => $app_key));
		if(!$dominio) {
			$this->_redirect($this->view()->basePath() . "/site/denied/" . $app_key);
		}
		
		// Verifica se ja tem sessão aberta
		if($this->usuario->idsessao > 0) {
			// Redireciona
 			$this->_redirect($this->view()->basePath() . "/site/chat/" . $app_key);
		}
		
		// Verifica se possui dados do post
		if($this->_request->isPost()) {
			// Busca a quantidade de usuários em espera
			$model = new Model\Sessoes();
			$data = array(
				'iddominio' => $dominio['iddominio'],
				'nome' => $this->getParam("nome", ""),
				'email' => $this->getParam("email", ""),
				'ativa' => TRUE,
				'ultima_atualizacao' => date("Y-m-d H:i:s"),
				'data_criacao' => date("Y-m-d H:i:s"),
			);
			$idsessao = $model->insert($data);
		
			// Adiciona a sessão
			$this->usuario->idsessao = $idsessao;
			$this->usuario->iddominio = $data['iddominio'];
			$this->usuario->nome = $data['nome'];
			$this->usuario->email = $data['email'];
			
			// Redireciona
			$this->_redirect($this->view()->basePath() . "/site/chat/" . $app_key);
		}
		
		// Assina as variaveis
		$this->view()->app_key = $app_key;
	}
	
	/**
	 * Ação do frame principal
	 */
	public function siteAction()
	{
		$this->view()->disableTemplate();
		
		// Recupera o app_key
		$app_key = $this->_request->getParam("app_key");
		
		// Verifica se ja tem sessão aberta
		if(!($this->usuario->idsessao > 0)) {
			// Redireciona
 			$this->_redirect($this->view()->basePath() . "/site/open/" . $app_key);
		}
		
		// Recupera o dominio a partir do app_key
		$model = new Model\Dominios();
		$dominio = $model->fetchRow(array('app_key = ?' => $app_key));
		if(!$dominio) {
			$this->_redirect($this->view()->basePath() . "/site/denied/" . $app_key);
		}
		
		// Busca a quantidade de usuários em espera
		$model = new Model\Sessoes();
		$sessoes = $model->fetchAll(array('iddominio = ?' => $dominio['iddominio'], 'idsessao < ?' => $this->usuario->idsessao, "ativa = TRUE"), "data_criacao DESC");
		
		// Busca se a sessão é valida
		$sessao = $model->fetchRow(array('idsessao = ?' => $this->usuario->idsessao, "ativa = TRUE"));
		if(!$sessao) {
			$this->usuario->idsessao = 0;
			$this->_redirect($this->view()->basePath() . "/site/open/" . $app_key);
		}
		
		// Busca as mensagens da sessão
		$model = new Model\Sessoesmensagens();
		$select = $model->select()
			->from("sessoes_mensagens", array())
			->columns(array(
				"sessoes_mensagens.*",
				'atendimento' => "COALESCE(usuarios.nome, sessoes.nome)"
			))
			->join("sessoes", "sessoes.idsessao = sessoes_mensagens.idsessao", array())
			->joinLeft("usuarios", "usuarios.idusuario = sessoes_mensagens.idusuario", array())
			->where("sessoes.idsessao = ?", $this->usuario->idsessao)
			->order("data_envio ASC");
			
		$mensagens = $model->fetchAll($select);
		
		// Assina as variaveis
		$this->view()->app_key = $app_key;
		$this->view()->mensagens = $mensagens;
		$this->view()->sessoes = $sessoes;
	}
	
	public function sairAction()
	{
		// Recupera o app_key
		$key = $this->_request->getParam("app_key");
		
		// Finaliza a sessão
		$model = new Model\Sessoes();
		$data = array(
			'ativa' => "FALSE"
		);
		$model->update($data, array('idsessao = ?' => $this->usuario->idsessao));
		
		// Finaliza a sessão
		$this->usuario->destroy();
		
		// Redireciona
		$this->_redirect($this->view->basePath . "/site/chat/" . $key);
	}
	
}