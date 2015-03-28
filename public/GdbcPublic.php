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

		if($this->ModulesController->isModuleRegistered(GdbcModulesController::MODULE_WORDPRESS))
		{
			/**
			*
			* @var \GdbcWordpressPublicModule
			*/
			$wordpressModuleInstance = $this->ModulesController->getPublicModuleInstance(GdbcModulesController::MODULE_WORDPRESS);
			$loginScriptsEnqueued = false;

			if(null !== $this->ModulesController->getModuleSettingOption(GdbcModulesController::MODULE_WORDPRESS, GdbcWordpressAdminModule::COMMENTS_FORM))
			{
				$wordpressModuleInstance->activateCommentsActions();
			}

			if(null !== $this->ModulesController->getModuleSettingOption(GdbcModulesController::MODULE_WORDPRESS, GdbcWordpressAdminModule::LOGIN_FORM))
			{
				(!$loginScriptsEnqueued) ? $loginScriptsEnqueued = add_action('login_enqueue_scripts', array($this, 'enqueuePublicScriptsAndStyles')) : null;
				$wordpressModuleInstance->activateLoginActions();
			}
			
			if(null !== $this->ModulesController->getModuleSettingOption(GdbcModulesController::MODULE_WORDPRESS, GdbcWordpressAdminModule::REGISTRATION_FORM))
			{
				(!$loginScriptsEnqueued) ? $loginScriptsEnqueued = add_action('login_enqueue_scripts', array($this, 'enqueuePublicScriptsAndStyles')) : null;
				$wordpressModuleInstance->activateRegisterActions();
			}
			
			if(null !== $this->ModulesController->getModuleSettingOption(GdbcModulesController::MODULE_WORDPRESS, GdbcWordpressAdminModule::LOST_PASSWORD_FORM))
			{
				(!$loginScriptsEnqueued) ? $loginScriptsEnqueued = add_action('login_enqueue_scripts', array($this, 'enqueuePublicScriptsAndStyles')) : null;
				$wordpressModuleInstance->activateLostPasswordActions();
			}

			if(null !== $this->ModulesController->getModuleSettingOption(GdbcModulesController::MODULE_WORDPRESS, GdbcWordpressAdminModule::COMMENTS_FORM_WEBSITE_FIELD))
			{
			    $wordpressModuleInstance->activateFormDefaultFieldsActions();
			}

			unset($wordpressModuleInstance, $loginScriptsEnqueued);
		}
		
		/**
		 * GoodBye Captcha JetPack integration - comments and ontact form
		 */ 
		if($this->ModulesController->isModuleRegistered(GdbcModulesController::MODULE_JETPACK))
		{

			$jetPackModuleInstance = $this->ModulesController->getModuleInstance(GdbcModulesController::MODULE_JETPACK, MchWpModule::MODULE_TYPE_PUBLIC);

//			if(null !== $this->ModulesController->getModuleSettingOption(GdbcModulesController::MODULE_JETPACK, GdbcJetPackAdminModule::COMMENTS_FORM))
//			{
//				$jetPackModuleInstance->activateCommentsFormActions();
//			}
			
			if(null !== $this->ModulesController->getModuleSettingOption(GdbcModulesController::MODULE_JETPACK, GdbcJetPackAdminModule::CONTACT_FORM))
			{
				$jetPackModuleInstance->activateContactFormActions();
			}
			
			unset($jetPackModuleInstance);
		}

		
		/**
		 * GoodBye BuddyPress integration
		 */ 
		if($this->ModulesController->isModuleRegistered(GdbcModulesController::MODULE_BUDDY_PRESS))
		{
			$buddyPressModuleInstance = $this->ModulesController->getModuleInstance(GdbcModulesController::MODULE_BUDDY_PRESS, MchWpModule::MODULE_TYPE_PUBLIC);

			if(null !== $this->ModulesController->getModuleSettingOption(GdbcModulesController::MODULE_BUDDY_PRESS, GdbcBuddyPressAdminModule::OPTION_REGISTRATION_FORM))
			{
				$buddyPressModuleInstance->activateRegistrationFormActions();
			}

			if(null !== $this->ModulesController->getModuleSettingOption(GdbcModulesController::MODULE_BUDDY_PRESS, GdbcBuddyPressAdminModule::OPTION_LOGIN_FORM))
			{
				$buddyPressModuleInstance->activateLoginFormActions();
			}

			unset($buddyPressModuleInstance);
		}

		if($this->ModulesController->isModuleRegistered(GdbcModulesController::MODULE_ULTIMATE_MEMBER))
		{
			if(null !== $this->ModulesController->getModuleSettingOption(GdbcModulesController::MODULE_ULTIMATE_MEMBER, GdbcUltimateMemberAdminModule::ULTIMATE_MEMBER_LOGIN_FORM))
			{
				$this->ModulesController->getPublicModuleInstance(GdbcModulesController::MODULE_ULTIMATE_MEMBER)->activateLoginActions();
			}
			if(null !== $this->ModulesController->getModuleSettingOption(GdbcModulesController::MODULE_ULTIMATE_MEMBER, GdbcUltimateMemberAdminModule::ULTIMATE_MEMBER_REGISTER_FORM))
			{
				$this->ModulesController->getPublicModuleInstance(GdbcModulesController::MODULE_ULTIMATE_MEMBER)->activateRegisterActions();
			}
			if(null !== $this->ModulesController->getModuleSettingOption(GdbcModulesController::MODULE_ULTIMATE_MEMBER, GdbcUltimateMemberAdminModule::ULTIMATE_MEMBER_LOST_PASSWORD_FORM))
			{
				$this->ModulesController->getPublicModuleInstance(GdbcModulesController::MODULE_ULTIMATE_MEMBER)->activateLostPasswordActions();
			}

		}

		/**
		 * GoodBye Captcha - Popular Forms integration
		 *	Gravity Forms, 
		 *	Contact Form 7, 
		 *	Ninja Forms, 
		 *	Formidable Forms,  
		 *	Fast Secure Contact Form
		 */
		if($this->ModulesController->isModuleRegistered(GdbcModulesController::MODULE_POPULAR_FORMS))
		{
			#Formidable Forms - Free and Pro
			if(null !== $this->ModulesController->getModuleSettingOption(GdbcModulesController::MODULE_POPULAR_FORMS, GdbcPopularFormsAdminModule::FORMIDABLE_FORMS))
			{
				if($this->ModulesController->isModuleRegistered(GdbcModulesController::MODULE_FORMIDABLE_FORMS))
				{
					$this->ModulesController->getPublicModuleInstance(GdbcModulesController::MODULE_FORMIDABLE_FORMS)->activateFormidableFormsActions();
				}
			}

			#Fast Secure Form
			if(null !== $this->ModulesController->getModuleSettingOption(GdbcModulesController::MODULE_POPULAR_FORMS, GdbcPopularFormsAdminModule::FAST_SECURE_FORM))
			{
				if($this->ModulesController->isModuleRegistered(GdbcModulesController::MODULE_FAST_SECURE_FORM))
				{
					$this->ModulesController->getPublicModuleInstance(GdbcModulesController::MODULE_FAST_SECURE_FORM)->activateFastSecureFormActions();
				}
			}
			
			#Gravity Forms
			if(null !== $this->ModulesController->getModuleSettingOption(GdbcModulesController::MODULE_POPULAR_FORMS, GdbcPopularFormsAdminModule::GRAVITY_FORMS))
			{
				if($this->ModulesController->isModuleRegistered(GdbcModulesController::MODULE_GRAVITY_FORMS))
				{
					$this->ModulesController->getPublicModuleInstance(GdbcModulesController::MODULE_GRAVITY_FORMS)->activateGravityFormsActions();
				}
			}

			#Contact Form 7
			if(null !== $this->ModulesController->getModuleSettingOption(GdbcModulesController::MODULE_POPULAR_FORMS, GdbcPopularFormsAdminModule::CONTACT_FORM_7))
			{
				if($this->ModulesController->isModuleRegistered(GdbcModulesController::MODULE_CONTACT_FORM_7))
				{
					$this->ModulesController->getPublicModuleInstance(GdbcModulesController::MODULE_CONTACT_FORM_7)->activateContactForm7Actions();
				}
			}

			#Ninja Forms
			if(null !== $this->ModulesController->getModuleSettingOption(GdbcModulesController::MODULE_POPULAR_FORMS, GdbcPopularFormsAdminModule::NINJA_FORMS))
			{
				if($this->ModulesController->isModuleRegistered(GdbcModulesController::MODULE_NINJA_FORMS))
				{
					$this->ModulesController->getPublicModuleInstance(GdbcModulesController::MODULE_NINJA_FORMS)->activateNinjaFormsActions();
				}
			}
			
		}

		//Newsletter Subscriptions Plugins
		if($this->ModulesController->isModuleRegistered(GdbcModulesController::MODULE_SUBSCRIPTIONS))
		{
			// MailChimp for WP Lite
			if(null !== $this->ModulesController->getModuleSettingOption(GdbcModulesController::MODULE_SUBSCRIPTIONS, GdbcSubscriptionsAdminModule::MAIL_CHIMP_LITE_ACTIVATED))
			{
				$this->ModulesController->getPublicModuleInstance(GdbcModulesController::MODULE_MAIL_CHIMP_LITE)->activateActions();
			}

			//Uji CountDown
			if(null !== $this->ModulesController->getModuleSettingOption(GdbcModulesController::MODULE_SUBSCRIPTIONS, GdbcSubscriptionsAdminModule::UJI_COUNTDOWN_ACTIVATED))
			{
				$this->ModulesController->getPublicModuleInstance(GdbcModulesController::MODULE_UJI_COUNTDOWN)->activateActions();
			}
		}

		if($this->ModulesController->isModuleRegistered(GdbcModulesController::MODULE_POPULAR_PLUGINS))
		{
			if(null !== $this->ModulesController->getModuleSettingOption(GdbcModulesController::MODULE_POPULAR_PLUGINS, GdbcPopularPluginsAdminModule::USER_PRO_LOGIN_FORM))
			{
				$this->ModulesController->getPublicModuleInstance(GdbcModulesController::MODULE_POPULAR_PLUGINS)->activateUserProLoginActions();
			}

			if(null !== $this->ModulesController->getModuleSettingOption(GdbcModulesController::MODULE_POPULAR_PLUGINS, GdbcPopularPluginsAdminModule::USER_PRO_REGISTER_FORM))
			{
				$this->ModulesController->getPublicModuleInstance(GdbcModulesController::MODULE_POPULAR_PLUGINS)->activateUserProRegisterActions();
			}
		}
	}

	public function initPlugin()
	{
		parent::initPlugin();
	}

	public function addAfterSetupThemeActions()
	{
		if(!$this->ModulesController->isModuleRegistered(GdbcModulesController::MODULE_WORDPRESS))
			return;

		$wordpressModuleInstance = $this->ModulesController->getPublicModuleInstance(GdbcModulesController::MODULE_WORDPRESS);

		if(null !== $this->ModulesController->getModuleSettingOption(GdbcModulesController::MODULE_WORDPRESS, GdbcWordpressAdminModule::COMMENTS_FORM_NOTES_FIELDS))
			$wordpressModuleInstance->activateCommentsFormNotesActions();

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