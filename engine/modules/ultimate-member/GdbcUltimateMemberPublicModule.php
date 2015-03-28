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

final class GdbcUltimateMemberPublicModule extends GdbcBasePublicModule
{
	private $isUltimateMemberActivated = false;

	protected function __construct(array $arrPluginInfo)
	{
		parent::__construct($arrPluginInfo);

		if($this->isUltimateMemberActivated = GdbcPluginUtils::isUltimateMemberActivated())
		{
			add_action('um_submit_form_errors_hook', array($this, 'validateFormEncryptedToken'));
		}
	}


	public function activateLoginActions()
	{
		if(!$this->isUltimateMemberActivated)
			return;

		add_action('um_after_login_fields', array($this, 'renderHiddenFieldIntoForm'), 10);
	}

	public function activateRegisterActions()
	{
		if(!$this->isUltimateMemberActivated)
			return;

		add_action('um_after_register_fields', array($this, 'renderHiddenFieldIntoForm'), 10);
	}

	public function activateLostPasswordActions()
	{
		if(!$this->isUltimateMemberActivated)
			return;

		add_action('um_reset_password_page_hidden_fields', array($this, 'renderHiddenFieldIntoForm'), 10);
		add_action('um_reset_password_errors_hook',  array($this, 'validateFormEncryptedToken'), 10);
	}

	public function renderHiddenFieldIntoForm()
	{
		echo GdbcTokenController::getInstance()->getTokenInputField();
	}

	public function validateFormEncryptedToken($arrRequestInfo)
	{
		$umSection = !empty($arrRequestInfo['_um_password_reset']) ?  GdbcUltimateMemberAdminModule::ULTIMATE_MEMBER_LOST_PASSWORD_FORM : null;
		if(null === $umSection && !empty($arrRequestInfo['mode']))
		{
			('login' === $arrRequestInfo['mode']) ? $umSection = GdbcUltimateMemberAdminModule::ULTIMATE_MEMBER_LOGIN_FORM : ('register' === $arrRequestInfo['mode'] ? $umSection =  GdbcUltimateMemberAdminModule::ULTIMATE_MEMBER_REGISTER_FORM : null);
		}

		global $ultimatemember;
		if(null === $umSection || !isset($ultimatemember->form) || !(class_exists('UM_Form', false)) || !($ultimatemember->form instanceof UM_Form))
		{
			wp_redirect(home_url());
			exit;
		}

		if(GdbcRequest::isValid(array('module' => GdbcModulesController::MODULE_ULTIMATE_MEMBER, 'section' => $umSection)))
		{
			return;
		}

		$ultimatemember->form->add_error($this->PLUGIN_SLUG,  __('Please enter a valid username!', $this->PLUGIN_SLUG));

	}

	public static function getInstance(array $arrPluginInfo)
	{				
		static $arrInstances = array();
		$instanceKey         = implode('', $arrPluginInfo);
		return isset($arrInstances[$instanceKey]) ? $arrInstances[$instanceKey] : $arrInstances[$instanceKey] = new self($arrPluginInfo);
	}

}
