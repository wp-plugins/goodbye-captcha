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
		parent::__construct($arrPluginInfo);

		add_action('admin_menu', array($this, 'addAdminMenu'));
		//add_action('admin_init', array($this, 'checkForUpdate'));

		add_action('wp_ajax_nopriv_' . 'retrieveToken', array( GdbcTokenController::getInstance(), 'retrieveEncryptedToken' ) );
		add_action('wp_ajax_'        . 'retrieveToken', array( GdbcTokenController::getInstance(), 'retrieveEncryptedToken' ) );

		add_action('wp_ajax_'        . 'retrieveInitialDashboardData', array(GdbcReportsAdminModule::getInstance($arrPluginInfo), 'retrieveInitialDashboardData'));
		add_action('wp_ajax_'        . 'getDisplayableAttemptsArray', array(GdbcReportsAdminModule::getInstance($arrPluginInfo), 'getDisplayableAttemptsArray'));
		add_action('wp_ajax_'        . 'getModuleData', array(GdbcReportsAdminModule::getInstance($arrPluginInfo), 'getModuleData'));
		add_action('wp_ajax_'        . 'getModuleStatsPercentage', array(GdbcReportsAdminModule::getInstance($arrPluginInfo), 'getModuleStatsPercentage'));
		add_action('wp_ajax_'        . 'getTotalAttemptsPerModule', array(GdbcReportsAdminModule::getInstance($arrPluginInfo), 'getTotalAttemptsPerModule'));
		add_action('wp_ajax_'        . 'manageIp', array(GdbcReportsAdminModule::getInstance($arrPluginInfo), 'manageIp'));
		add_action('wp_ajax_'        . 'retrieveLatestAttemptsTable', array(GdbcReportsAdminModule::getInstance($arrPluginInfo), 'retrieveLatestAttemptsTable'));


		if(MchWp::isAjaxRequest())
		{
			add_filter('nocache_headers', array($this, 'rewriteAjaxNoCacheHeaders'));
		}

		if(!MchWp::isAjaxRequest())
		{
			GdbcPluginUpdater::updateToCurrentVersion();

			$arrTrustedIps = $this->ModulesController->getModuleSettingOption(GdbcModulesController::MODULE_SETTINGS, GdbcSettingsAdminModule::OPTION_TRUSTED_IPS);
			if(empty($arrTrustedIps))
			{
				if(null !== $this->ModulesController->getModuleSettingOption(GdbcModulesController::MODULE_WORDPRESS, GdbcWordpressAdminModule::LOGIN_FORM))
					add_action('admin_notices', array($this, 'showEmptyTrustedIpNotice'));
			}

			$isTestModeActivated = (bool)$this->ModulesController->getModuleSettingOption(GdbcModulesController::MODULE_SETTINGS, GdbcSettingsAdminModule::OPTION_TEST_MODE_ACTIVATED);
			if($isTestModeActivated)
			{
				add_action('admin_notices', array($this, 'showTestModeActivatedNotice'));
			}
		}

		if(MchWp::isAjaxRequest() && GdbcPluginUtils::isMailChimpLiteActivated())
		{
			if($this->ModulesController->isModuleRegistered(GdbcModulesController::MODULE_SUBSCRIPTIONS))
			{
				if (null !== $this->ModulesController->getModuleSettingOption(GdbcModulesController::MODULE_SUBSCRIPTIONS, GdbcSubscriptionsAdminModule::MAIL_CHIMP_LITE_ACTIVATED)) {
					add_filter('mc4wp_valid_form_request', create_function('$isFormValid', 'return GdbcRequest::isValid(array("module" => GdbcModulesController::MODULE_MAIL_CHIMP_LITE));'));
				}
			}
		}

		if(MchWp::isAjaxRequest() && GdbcPluginUtils::isUserProPluginActivated())
		{
			if($this->ModulesController->isModuleRegistered(GdbcModulesController::MODULE_POPULAR_PLUGINS))
			{
				if (null !== $this->ModulesController->getModuleSettingOption(GdbcModulesController::MODULE_POPULAR_PLUGINS, GdbcPopularPluginsAdminModule::USER_PRO_LOGIN_FORM))
				{
					add_filter('userpro_login_validation', array($this, 'userProValidateToken'));
				}
				if (null !== $this->ModulesController->getModuleSettingOption(GdbcModulesController::MODULE_POPULAR_PLUGINS, GdbcPopularPluginsAdminModule::USER_PRO_REGISTER_FORM))
				{
					add_action('userpro_before_fields', array($this, 'userProRegisterBeforeFields'));

					add_filter('userpro_register_validation', array($this, 'userProValidateToken'));
				}
			}
		}

	}

	public function rewriteAjaxNoCacheHeaders($arrHeaders)
	{
		empty($arrHeaders) ? $arrHeaders = array() : null;

		$arrHeaders['Cache-Control'] = 'no-store, no-cache, must-revalidate, max-age=0';

		return $arrHeaders;
	}


	public function userProValidateToken()
	{
		return !GdbcRequest::isValid(array("module" => GdbcModulesController::MODULE_POPULAR_PLUGINS));
	}

	public function userProRegisterBeforeFields()
	{
		echo GdbcTokenController::getInstance()->getTokenInputField() . '<script>(new jQuery.GdbcClient()).requestTokens();</script>';
	}

	public function showEmptyTrustedIpNotice()
	{
		$screenId = get_current_screen();
		if( empty($screenId->id) || $this->AdminSettingsPageHook !== $screenId->id )
			return;

		echo '<div class="update-nag" style="border-color:#dd3d36;"><span>' . _('<b>Don\'t Lock Yourself Out!</b> Whitelist your IP Address in <a href = "'.admin_url('admin.php?page=goodbye-captcha').'">GoodBye Captcha Settings</a>' ) . '</span></div>';
	}

	public function showTestModeActivatedNotice()
	{
		$screenId = get_current_screen();
		if( empty($screenId->id) || $this->AdminSettingsPageHook !== $screenId->id )
			return;

		echo '<div class="update-nag" style="border-color:#dd3d36;"><span>' . _('GoodBye Captcha was switched to <b>Test Mode</b>!') . '</span></div>';
	}

	public function addAdminMenu()
	{
		$this->AdminSettingsPageHook = add_menu_page(
			'GoodByeCaptcha Settings',
			'GoodByeCaptcha',
			'manage_options',
			$this->PLUGIN_SLUG,
			array( $this, 'renderPluginAdminPage' ),
			'dashicons-shield', //  for menu icon
			'53.8394'
		);

//		$this->AdminSettingsPageHook = add_options_page( __('GoodByeCaptcha Settings', $this->PLUGIN_SLUG),
//														 'GoodByeCaptcha',
//														 'manage_options',
//														 $this->PLUGIN_SLUG,
//														 array( $this, 'renderPluginAdminPage' ));
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

		GdbcPluginUpdater::updateToCurrentVersion();

//		$settingsModuleInstance = GdbcModulesController::getInstance($arrPluginInfo)->getAdminModuleInstance(GdbcModulesController::MODULE_SETTINGS);
//		$settingsModuleInstance->setSettingOption(GdbcSettingsAdminModule::OPTION_PLUGIN_VERSION_ID, MchWpBase::getPluginVersionIdFromString(GoodByeCaptcha::PLUGIN_VERSION));
		GdbcPluginUtils::isUjiCountDownActivated() ? GdbcModulesController::getInstance($arrPluginInfo)->getAdminModuleInstance(GdbcModulesController::MODULE_SUBSCRIPTIONS)->setSettingOption(GdbcSubscriptionsAdminModule::UJI_COUNTDOWN_ACTIVATED, true) : null;





		GdbcTaskScheduler::scheduleGdbcTasks();
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
	{
		GdbcTaskScheduler::unscheduleGdbcTasks();
	}

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

	public function adminInitPlugin()
	{
		parent::adminInitPlugin();
	}

	public static function getInstance(array $arrPluginInfo)
	{
		static $arrInstances = array();
		$instanceKey         = implode('', $arrPluginInfo);
		return isset($arrInstances[$instanceKey]) ? $arrInstances[$instanceKey] : $arrInstances[$instanceKey] = new self($arrPluginInfo);
	}
}
