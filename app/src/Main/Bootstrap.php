<?php

namespace Main;

use General\Helpers;
use General\Application;

/**
 * Bootstrap do modulo
 *
 * Aqui todo método iniciado por ini será executado pelo framework
 *
 */
class Bootstrap extends \General\Mvc\Bootstrap
{
	/**
	 * Registra as mensagens de sessão
	 */
	public function iniMessages()
	{
		$messages = Helpers\Messages::getMessages();

		$front = Application\Services::getInstance()->getService("front");
		
		$errors = $messages->error;
		$alerts = $messages->alert;
		$success = $messages->success;
		$infos = $messages->info;

		$front->getView()->_erros = $errors;
		$front->getView()->_alerts = $alerts;
		$front->getView()->_success = $success;
		$front->getView()->_infos = $infos;

		$messages->destroy();
	}

	/**
	 * Registra os plugins de controllers
	 */
	public function iniControllerPlugins()
	{
		
	}
	
	/**
	 * Registra os helpers dos views
	 */
	public function iniViewHelpers()
	{
		
	}
}
