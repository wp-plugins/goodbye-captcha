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

/**
 * Description of GdbcPublic
 *
 * @author Mihai Chelaru
 */
final class GdbcPublic extends GdbcBasePublicPlugin
{
	protected function __construct(array $arrPluginInfo)
	{
		parent::__construct($arrPluginInfo);

		
		if($this->ModulesController->isModuleRegistered(GdbcModulesController::MODULE_DEFAULT))
		{
			/**
			*
			* @var \GdbcDefaultPublicModule 
			*/
			$defaultModuleInstance = $this->ModulesController->getModuleInstance(GdbcModulesController::MODULE_DEFAULT, MchWpModule::MODULE_TYPE_PUBLIC);
			if(null !== $this->ModulesController->getModuleSettingOption(GdbcModulesController::MODULE_DEFAULT, GdbcDefaultAdminModule::OPTION_COMMENTS_FORM_ACTIVATED))
			{
				$defaultModuleInstance->activateCommentsActions();
			}

			if(null !== $this->ModulesController->getModuleSettingOption(GdbcModulesController::MODULE_DEFAULT, GdbcDefaultAdminModule::OPTION_LOGIN_FORM_ACTIVATED))
			{
				add_action('login_enqueue_scripts', array($this,'enqueuePublicScriptsAndStyles') );
				$defaultModuleInstance->activateLoginActions();
			}
			
			if(null !== $this->ModulesController->getModuleSettingOption(GdbcModulesController::MODULE_DEFAULT, GdbcDefaultAdminModule::OPTION_REGISTRATION_FORM_ACTIVATED))
			{
				$defaultModuleInstance->activateRegisterActions();
			}
			
			if(null !== $this->ModulesController->getModuleSettingOption(GdbcModulesController::MODULE_DEFAULT, GdbcDefaultAdminModule::OPTION_LOST_PASSWORD_FORM_ACTIVATED))
			{
				$defaultModuleInstance->activateLostPasswordActions();			
			}
			
			unset($defaultModuleInstance);
		}
		
		/**
		 * GoodBye Captcha JetPack integration - comments and ontact form
		 */ 
		if($this->ModulesController->isModuleRegistered(GdbcModulesController::MODULE_JETPACK))
		{
			$jetPackModuleInstance = $this->ModulesController->getModuleInstance(GdbcModulesController::MODULE_JETPACK, MchWpModule::MODULE_TYPE_PUBLIC);

			if(null !== $this->ModulesController->getModuleSettingOption(GdbcModulesController::MODULE_JETPACK, GdbcJetPackAdminModule::OPTION_COMMENTS_FORM_ACTIVATED))
			{
				$jetPackModuleInstance->activateCommentsFormActions();
			}
			
			if(null !== $this->ModulesController->getModuleSettingOption(GdbcModulesController::MODULE_JETPACK, GdbcJetPackAdminModule::OPTION_CONTACT_FORM_ACTIVATED))
			{
				$jetPackModuleInstance->activateContactFormActions();
			}
			
			unset($jetPackModuleInstance);
		}

		
		/**
		 * GoodBye BuddyPress integration - comments and ontact form
		 */ 
		if($this->ModulesController->isModuleRegistered(GdbcModulesController::MODULE_BUDDY_PRESS))
		{
			$buddyPressModuleInstance = $this->ModulesController->getModuleInstance(GdbcModulesController::MODULE_BUDDY_PRESS, MchWpModule::MODULE_TYPE_PUBLIC);

			if(null !== $this->ModulesController->getModuleSettingOption(GdbcModulesController::MODULE_BUDDY_PRESS, GdbcBuddyPressAdminModule::OPTION_REGISTRATION_FORM_ACTIVATED))
			{
				$buddyPressModuleInstance->activateRegistrationFormActions();
			}
			
			if(null !== $this->ModulesController->getModuleSettingOption(GdbcModulesController::MODULE_BUDDY_PRESS, GdbcBuddyPressAdminModule::OPTION_LOGIN_FORM_ACTIVATED))
			{
				$buddyPressModuleInstance->activateLoginFormActions();
			}
			
			unset($buddyPressModuleInstance);
		}
		
		
		/**
		 * GoodBye Captcha Forms integration - Gravity Forms, Contact Form 7, Ninja Forms, Formidable Forms,  Fast Secure Contact Form
		 */
		if($this->ModulesController->isModuleRegistered(GdbcModulesController::MODULE_POPULAR_FORMS))
		{
			$popularFormsModuleInstance = $this->ModulesController->getModuleInstance(GdbcModulesController::MODULE_POPULAR_FORMS, MchWpModule::MODULE_TYPE_PUBLIC);
			
			#Contact Form 7
			if(null !== $this->ModulesController->getModuleSettingOption(GdbcModulesController::MODULE_POPULAR_FORMS, GdbcPopularFormsAdminModule::OPTION_CONTACT_FORM_7_ACTIVATED))
			{
				$popularFormsModuleInstance->activateContactForm7Actions();
			}

			#Formidable Forms - Free and Pro
			if(null !== $this->ModulesController->getModuleSettingOption(GdbcModulesController::MODULE_POPULAR_FORMS, GdbcPopularFormsAdminModule::OPTION_FORMIDABLE_FORMS_ACTIVATED))
			{
				$popularFormsModuleInstance->activateFormidableFormsActions();
			}

			#Fast Secure Form
			if(null !== $this->ModulesController->getModuleSettingOption(GdbcModulesController::MODULE_POPULAR_FORMS, GdbcPopularFormsAdminModule::OPTION_FAST_SECURE_FORM_ACTIVATED))
			{
				$popularFormsModuleInstance->activateFastSecureFormActions();
			}
			
			unset($popularFormsModuleInstance);
		}
		
		add_action('wp_ajax_nopriv_' . 'retrieveToken', array( GdbcTokenController::getInstance(), 'retrieveEncryptedToken' ) );
		add_action('wp_ajax_'        . 'retrieveToken', array( GdbcTokenController::getInstance(), 'retrieveEncryptedToken' ) );
		
	}

	public function initPlugin()
	{
		parent::initPlugin();
	}
	
	/**
	 * 
	 * @staticvar array $arrInstances
	 * @param array $arrPluginInfo
	 * @return \GdbcPublic
	 */
	public static function getInstance(array $arrPluginInfo)
	{
		static $arrInstances = array();
		$instanceKey         = implode('', $arrPluginInfo);
		return isset($arrInstances[$instanceKey]) ? $arrInstances[$instanceKey] : $arrInstances[$instanceKey] = new self($arrPluginInfo);
	}
	
}