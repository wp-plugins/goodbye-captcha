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
 * Description of GdbcAdmin
 *
 * @author Patty
 */

final class GdbcAdmin extends GdbcBaseAdminPlugin
{

	protected function __construct(array $arrPluginInfo)
	{
		
		if( ! self::isAdminLoggedIn() )
		{
			return ;
		}

		parent::__construct($arrPluginInfo);
		
		add_action('admin_menu', array($this, 'addAdminMenu'));
		add_action('admin_init', array($this, 'checkForUpdate'));
		
	}
	
	public function checkForUpdate()
	{
		if(GoodByeCaptcha::isFreeVersion())
			return;
	
		$defaultModuleInstance = GdbcModulesController::getInstance($this->ArrPluginInfo)->getModuleInstance(GdbcModulesController::MODULE_DEFAULT, MchWpModule::MODULE_TYPE_ADMIN);

		if(!$defaultModuleInstance->getModuleSetting()->getSettingOption(GdbcDefaultAdminModule::OPTION_LICENSE_ACTIVATED))
			return;
		
		new GdbcPluginUpdater(GoodByeCaptcha::PLUGIN_SITE_URL, 
								$this->PLUGIN_MAIN_FILE, array( 
									'version' 	=> $this->PLUGIN_VERSION,
									'license' 	=> $defaultModuleInstance->getModuleSetting()->getSettingOption(GdbcDefaultAdminModule::OPTION_LICENSE_KEY),
									'item_name' => 'GoodBye Captcha Pro',
									'author' 	=> 'Mihai Chelaru'
									)
								);
	}
	
	public function addAdminMenu() 
	{
		$this->AdminSettingsPageHook = add_options_page( __('GoodByeCaptha Settings', $this->PLUGIN_SLUG), 
														 'GoodByeCaptha', 
														 'manage_options', 
														 $this->PLUGIN_SLUG, 
														 array( $this, 'renderPluginAdminPage' ));
	}
		

	
	/**
	 * Fired when the plugin is activated.
	 *
	 *
	 * @param    boolean    $isForNetwork    True if WPMU superadmin uses
	 *                                       "Network Activate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       activated on an individual blog.
	 */
	public static function activatePlugin(array $arrPluginInfo, $isForNetwork) 
	{
		if( !self::isMultiSite() || !$isForNetwork )
			return self::singleSiteActivate($arrPluginInfo);
	
		foreach ( self::getAllBlogIds() as $blogId )
		{
			switch_to_blog( $blogId );
			
			self::singleSiteActivate($arrPluginInfo);

			restore_current_blog();
		}		
	}
	
	
	private static function singleSiteActivate(array $arrPluginInfo)
	{
		/**
		*
		* @var \GdbcDefaultPublicModule 
		*/
		$defaultModuleInstance = GdbcModulesController::getInstance($arrPluginInfo)->getModuleInstance(GdbcModulesController::MODULE_DEFAULT, MchWpModule::MODULE_TYPE_ADMIN);
		$defaultModuleInstance->getModuleSetting()->setSettingOption(GdbcDefaultAdminModule::OPTION_HIDDEN_INPUT_NAME, MchWpUtil::replaceNonAlphaCharacters(MchCrypt::getRandomString(20), '-'));
		$defaultModuleInstance->getModuleSetting()->setSettingOption(GdbcDefaultAdminModule::OPTION_TOKEN_SECRET_KEY, MchCrypt::getRandomString(MchCrypt::getCipherKeySize()));
			
		
	}
	
	public static function deactivatePlugin(array $arrPluginInfo, $isForNetwork) 
	{
		if( !self::isMultiSite() || !$isForNetwork )
			return self::singleSiteDeactivate($arrPluginInfo);
	
		foreach ( self::getAllBlogIds() as $blogId )
		{
			switch_to_blog( $blogId );
			
			self::singleSiteDeactivate($arrPluginInfo);

			restore_current_blog();
		}		
	
	}

	private static function singleSiteDeactivate($arrPluginInfo)
	{}

	public function activateForNewSite($blogId)
	{
		if ( 1 !== did_action('wpmu_new_blog') ) 
			return;

		switch_to_blog($blogId);
		
		self::singleSiteActivate(GoodByeCaptcha::getPluginInfo());
		
		restore_current_blog();
		
	}	
	
	public function initPlugin()
	{
		parent::initPlugin();
	}
	
	
	public static function getInstance(array $arrPluginInfo)
	{
		static $arrInstances = array();
		$instanceKey         = implode('', $arrPluginInfo);
		return isset($arrInstances[$instanceKey]) ? $arrInstances[$instanceKey] : $arrInstances[$instanceKey] = new self($arrPluginInfo);
	}

}
