<?php

namespace General\Helpers;

use General\Application;

/**
 * Classe de mensagens do sistema
 */
class Messages
{
	// Armazena as mensagens
	private static $_messages = [];
	
	// Informa se está inicializado
	private static $initialized = FALSE;

	/**
	 * Inicializa a classe estatica
	 */
	private static function initialize()
	{
		if (self::$initialized) {
			return;
		}

		self::$_messages = new Application\Sessions("messages");
		self::$initialized = TRUE;
	}

	/**
	 * Método que envia mensagens de sucesso
	 */
	static public function success($message)
	{
		self::initialize();
		
		// Adiciona mensagens de erro
		$success = self::$_messages->success;
		if(!is_array($success)) {
			$success = [];
		}
		$success[] = $message;
		self::$_messages->success = $success;
	}

	/**
	 * Método que envia mensagens de erro
	 */
	static public function error($message)
	{
		self::initialize();
		

		// Adiciona mensagens de erro
		$error = self::$_messages->error;
		if(!is_array($error)) {
			$error = [];
		}
		$error[] = $message;
		self::$_messages->error = $error;
	}
	
	/**
	 * Método que envia mensagens de alerta
	 */
	static public function alert($message)
	{
		self::initialize();

		// Adiciona mensagens de erro
		$alert = self::$_messages->alert;
		if(!is_array($alert)) {
			$alert = [];
		}
		$alert[] = $message;
		self::$_messages->alert = $alert;
	}
	
	/**
	 * Método que envia mensagens de informação
	 */
	static public function info($message)
	{
		self::initialize();
		
		// Adiciona mensagens de erro
		$info = self::$_messages->info;
		if(!is_array($info)) {
			$info = [];
		}
		$info[] = $message;
		self::$_messages->info = $info;
	}

	/**
	 * Método que recupera as mensagens
	 */
	static public function getMessages()
	{
		self::initialize();
		
		// Retorna as mensagens
		return self::$_messages;
	}
}
