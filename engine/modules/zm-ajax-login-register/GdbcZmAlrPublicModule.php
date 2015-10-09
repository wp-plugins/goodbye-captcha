<?php

/* 
 * Copyright (C) 2014 Mihai Chelaru
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

final class GdbcZmAlrPublicModule extends GdbcBasePublicModule
{
	private $isZmAlrActivated       = false;
	private $arrStatusLoginError    = null;
	private $arrStatusRegisterError = null;


	protected function __construct(array $arrPluginInfo)
	{
		parent::__construct($arrPluginInfo);
		$this->isZmAlrActivated = GdbcPluginUtils::isZmAlrActivated();

		$this->arrStatusLoginError = array('gdbc-login-error' => array(
			'description' => __('Invalid username or password!', GoodByeCaptcha::PLUGIN_SLUG),
			'cssClass'    => 'error-container',
			'code'        => 'show_notice'
		));

		$this->arrStatusRegisterError = array('gdbc-register-error' => array(
			'description' => __('An error occurred while registering your account!', GoodByeCaptcha::PLUGIN_SLUG),
			'cssClass'    => 'error-container',
			'code'        => 'show_notice'
		));

		add_filter('zm_alr_status_codes', array($this, 'registerGdbcStatusCode'), 10, 1);


		if(GdbcModulesController::getInstance($arrPluginInfo)->getModuleSettingOption(GdbcModulesController::MODULE_ZM_ALR, GdbcZmAlrAdminModule::ZM_ALR_LOGIN_FORM))
			$this->activateLoginActions();

		if(GdbcModulesController::getInstance($arrPluginInfo)->getModuleSettingOption(GdbcModulesController::MODULE_ZM_ALR, GdbcZmAlrAdminModule::ZM_ALR_REGISTER_FORM))
			$this->activateRegisterActions();

	}

	public function registerGdbcStatusCode($arrStatusCode)
	{
		$arrStatusCode = (array)$arrStatusCode;
		$arrStatusCode[key($this->arrStatusLoginError)]    = reset($this->arrStatusLoginError);
		$arrStatusCode[key($this->arrStatusRegisterError)] = reset($this->arrStatusRegisterError);

		return $arrStatusCode;
	}

	public function activateLoginActions()
	{
		if(!$this->isZmAlrActivated)
			return;

		add_filter('zm_alr_login_above_fields', array($this, 'renderHiddenFieldIntoForm'), 99, 1);
		add_filter('zm_alr_login_submit_pre_status_error' , array($this, 'validateLoginRequest'), 1, 1);
	}

	public function activateRegisterActions()
	{
		if(!$this->isZmAlrActivated)
			return;

		add_filter('zm_alr_register_above_fields', array($this, 'renderHiddenFieldIntoForm'), 99, 1);
		add_filter('zm_alr_register_submit_pre_status_error' , array($this, 'validateRegisterRequest'), 1, 1);
	}

	public function validateLoginRequest($preStatus)
	{
		//print_r('IN ' . __METHOD__);
		if(GdbcRequest::isValid(array('module' => GdbcModulesController::MODULE_ZM_ALR, 'section' => GdbcZmAlrAdminModule::ZM_ALR_LOGIN_FORM)))
			return $preStatus;

		reset($this->arrStatusLoginError);
		return key($this->arrStatusLoginError);
	}

	public function validateRegisterRequest($preStatus)
	{
		if(GdbcRequest::isValid(array('module' => GdbcModulesController::MODULE_ZM_ALR, 'section' => GdbcZmAlrAdminModule::ZM_ALR_REGISTER_FORM)))
			return $preStatus;

		reset($this->arrStatusRegisterError);
		return key($this->arrStatusRegisterError);
	}



	public function renderHiddenFieldIntoForm($aboveFieldsHtml)
	{

		$aboveFieldsHtml .= GdbcTokenController::getInstance()->getTokenInputField();

		if( ! $this->isAjaxRequest() )
			return $aboveFieldsHtml;

		return '<script type="text/javascript">(new jQuery.GdbcClient()).requestTokens();</script>' . $aboveFieldsHtml;

	}


	public static function getInstance(array $arrPluginInfo)
	{				
		static $arrInstances = array();
		$instanceKey         = implode('', $arrPluginInfo);
		return isset($arrInstances[$instanceKey]) ? $arrInstances[$instanceKey] : $arrInstances[$instanceKey] = new self($arrPluginInfo);
	}

}
