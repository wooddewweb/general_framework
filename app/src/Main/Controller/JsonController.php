<?php

namespace Main\Controller;

use Main\Model;
use General\Application\Sessions;

/**
 * Controlador de jsons
 *
 * @author Bruno P. Gonçalves
 */
class JsonController extends \General\Mvc\Controller
{
	/**
	 */
	public function configure()
	{
		$this->usuario = new Sessions("usuario");
		
		// Desativa as sessões inativas
		$model = new Model\Sessoes();
		$model->update(array('ativa' => "FALSE"), array("ativa = TRUE", "ultima_atualizacao + INTERVAL '1 min' < NOW()"));
	}
	
	/**
	 * Recupera a fila de espera do cliente 
	 */
	public function getfilaesperaAction() 
	{
	
		// Recupera o app_key
		$key = $this->getParam("app_key");
		
		// Verifica se ja tem sessão aberta
		if(!($this->usuario->idsessao > 0)) {
 			// Retorna o json
			die(json_encode(array('error' => "0004", 'message' => "Sessão inválida")));
		}
		
		// Recupera o dominio a partir do app_key
		$model = new Model\Dominios();
		$dominio = $model->fetchRow(array('app_key = ?' => $key));
		if(!$dominio) {
			// Retorna o json
			die(json_encode(array('error' => "0001", 'message' => "Dominio invalido")));
		}
		
		// Busca a quantidade de usuários em espera
		$model = new Model\Sessoes();
		$sessoes = $model->fetchAll(array('iddominio = ?' => $dominio['iddominio'], 'idsessao < ?' => $this->usuario->idsessao, "ativa = TRUE"), "data_criacao DESC");
		
		// Atualiza a sessão
		$model = new Model\Sessoes();
		$data = array('ultima_atualizacao' => date("Y-m-d H:i:s"));
		$model->update($data, array('idsessao = ?' => $this->usuario->idsessao));
		
		// Retorna o json das sessões
		die(json_encode(array('count' => count($sessoes))));
	}
	
	/**
	 * Envia mensagens ao chat
	 */
	public function sendmessageAction()
	{
		// Recupera o app_key
		$key = $this->getParam("app_key");
		$idsessao = $this->getParam("idsessao", $this->usuario->idsessao);
		
		// Verifica se ja tem sessão aberta
		if(!($idsessao > 0)) {
 			// Retorna o json
			die(json_encode(array('error' => "0004", 'message' => "Sessão inválida")));
		}
		
		// Recupera o dominio a partir do app_key
		$model = new Model\Dominios();
		$dominio = $model->fetchRow(array('app_key = ?' => $key));
		if(!$dominio) {
			// Retorna o json
			die(json_encode(array('error' => "0001", 'message' => "Dominio invalido")));
		}
		
		// Verifica se a sessão é do dominio
		if($idsessao > 0) {
			$model = new Model\Sessoes();
			$sessao = $model->fetchRow(array('iddominio = ?' => $dominio['iddominio'], 'idsessao = ?' => $idsessao));
			if(!$sessao) {
				// Retorna o json
				die(json_encode(array('error' => "0004", 'message' => "Sessão inválida")));
			}
		}
		
		// Grava a mensagem
		$model = new Model\Sessoesmensagens();
		$data = array(
			'idsessao' => $idsessao,
			'data_envio' => date("Y-m-d H:i:s"),
			'mensagem' => $this->getParam("mensagem", "")
		);
		
		if($this->usuario->idusuario > 0) {
			$data['idusuario'] = $this->usuario->idusuario;
		}
		
		try {
			$model->insert($data);
			
			// Atualiza a sessão
			$model = new Model\Sessoes();
			$data = array('ultima_atualizacao' => date("Y-m-d H:i:s"));
			$model->update($data, array('idsessao = ?' => $this->usuario->idsessao));
			
			// Retorna o json
			die(json_encode(array('result' => TRUE)));
		}
		catch(Exception $e) {
			// Retorna o json
			die(json_encode(array('error' => "0005", 'message' => "Sua mensagem não foi entregue")));
		}
	}
	
	/**
	 * Recupera as mensagens da sessão
	 */
	public function getmessageAction()
	{
		// Recupera o app_key
		$key = $this->getParam("app_key");
		$idsessao = $this->getParam("idsessao", $this->usuario->idsessao);
		
		// Verifica se ja tem sessão aberta
		if(!($idsessao > 0)) {
 			// Retorna o json
			die(json_encode(array('error' => "0004", 'message' => "Sessão inválida")));
		}
		
		// Recupera o dominio a partir do app_key
		$model = new Model\Dominios();
		$dominio = $model->fetchRow(array('app_key = ?' => $key));
		if(!$dominio) {
			// Retorna o json
			die(json_encode(array('error' => "0001", 'message' => "Dominio invalido")));
		}
		
		// Recupera a mensagem
		$ids = $this->getParam("ids", "");
		$ids = explode(",",$ids);
		$model = new Model\Sessoesmensagens();
		$select = $model->select()
			->from("sessoes_mensagens", array())
			->join("sessoes", "sessoes.idsessao = sessoes_mensagens.idsessao", array())
			->joinLeft("usuarios", "usuarios.idusuario = sessoes_mensagens.idusuario", array())
			->columns(array(
				"sessoes_mensagens.*",
				'atendimento' => "COALESCE(usuarios.nome, sessoes.nome)"
			))
			->where("sessoes.idsessao = ?", $idsessao)
			->where("NOT idsessao_mensagem in (" . implode(",", array_fill(0, count($ids), "?")) . ")", $ids)
			->order("data_envio ASC");
		
		$mensagens = $model->fetchAll($select);
		
		// Verifica se é pelo admin
		if(!$this->usuario->idusuario) {
			// Atualiza a sessão
			$model = new Model\Sessoes();
			$data = array('ultima_atualizacao' => date("Y-m-d H:i:s"));
			$model->update($data, array('idsessao = ?' => $idsessao));
		}
		else {
			// Verifica se a sessão é valida
			$model = new Model\Sessoes();
			$sessao = $model->fetchRow(array('iddominio = ?' => $dominio['iddominio'], 'idsessao = ?' => $idsessao, "ativa = TRUE"));
			if(!$sessao) {
				// Retorna o json
				die(json_encode(array('error' => "0004", 'message' => "Sessão inválida")));
			}
		}
		
		
		die(json_encode($mensagens));
	}
	
	/**
	 * Recupera as sessões
	 */
	public function getsessoesAction()
	{
		$this->view()->disableTemplate();
		
		// Recupera o app_key
		$app_key = $this->_request->getParam("app_key");
		
		// Recupera o dominio a partir do app_key
		$model = new Model\Dominios();
		$dominio = $model->fetchRow(array('app_key = ?' => $app_key));
		if(!$dominio) {
			die(json_encode(array('error'=>"0001", 'message'=>"Dominio inválido")));
		}
		
		// Verifica se o usuário está logado
		if(!($this->usuario->idusuario > 0)) {
			die(json_encode(array('error'=>"0004", 'message'=>"Sessão inválida")));
		}
		
		// Busca as sessões
		$model = new Model\Sessoes();
		$sessoes = $model->fetchAll(array('iddominio = ?' => $dominio['iddominio'], "ativa = TRUE"));
		
		// Retorna o json das sessões
		die(json_encode($sessoes));
	}
}