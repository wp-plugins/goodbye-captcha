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

abstract class GdbcBaseAdminPlugin extends MchWpAdminPlugin
{
	const ADMIN_NONCE_VALUE = 'gdbc-reports-nonce';

	protected function __construct(array $arrPluginInfo)
	{
		parent::__construct($arrPluginInfo);

	}
	
	/**
	 *
	 * @return \GdbcModulesController 
	 */	
	public function getModulesControllerInstance(array $arrPluginInfo)
	{
		return GdbcModulesController::getInstance($arrPluginInfo);
	}

	public function getPluginAdminActionLinks(array $pluginActionLinks)
	{
		return array_merge(
			array('settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->PLUGIN_SLUG ) . '">' . __( 'Settings', $this->PLUGIN_SLUG ) . '</a>'),
			$pluginActionLinks
		);
	}
	
	public function enqueueAdminScriptsAndStyles()
	{
		if(null === $this->AdminSettingsPageHook)
			return;

		$screenId = get_current_screen();
		if( empty($screenId->id) || $this->AdminSettingsPageHook !== $screenId->id )
			return;


		$selectedTab = $this->getAdminSettingsCurrentTab();

		if ($selectedTab === GdbcModulesController::MODULE_REPORTS)
		{

			remove_action( 'admin_print_styles', 'print_emoji_styles' );
			remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );

			wp_enqueue_script($this->PLUGIN_SLUG . '-jquery-flot', plugins_url('/admin/scripts/jquery-flot.js', $this->PLUGIN_MAIN_FILE), array('jquery'), $this->PLUGIN_VERSION);
			wp_enqueue_script($this->PLUGIN_SLUG . '-jquery-flot-tooltip', plugins_url('/admin/scripts/jquery-flot-tooltip.js', $this->PLUGIN_MAIN_FILE), array(), $this->PLUGIN_VERSION);
			wp_enqueue_script($this->PLUGIN_SLUG . '-raphael', plugins_url('/admin/scripts/raphael.js', $this->PLUGIN_MAIN_FILE), array(), $this->PLUGIN_VERSION);
			wp_enqueue_script($this->PLUGIN_SLUG . '-morris', plugins_url('/admin/scripts/morris.js', $this->PLUGIN_MAIN_FILE), array(), $this->PLUGIN_VERSION);

			wp_enqueue_script($this->PLUGIN_SLUG . '-reports-script', plugins_url('/admin/scripts/gdbc-reports.js', $this->PLUGIN_MAIN_FILE), array(), $this->PLUGIN_VERSION);

			wp_localize_script($this->PLUGIN_SLUG . '-reports-script', 'GdbcAdmin', array(
				'ajaxUrl' => admin_url('admin-ajax.php'),
				'shortCode' => $this->PLUGIN_SHORT_CODE,
				'reportsNonce' => wp_create_nonce(self::ADMIN_NONCE_VALUE),
				'slug' => $this->PLUGIN_SLUG
			));

			wp_enqueue_script($this->PLUGIN_SLUG . '-bootstrap', plugins_url('/admin/scripts/bootstrap.min.js', $this->PLUGIN_MAIN_FILE), array(), $this->PLUGIN_VERSION);

			wp_enqueue_script($this->PLUGIN_SLUG . '-jquery-jvectormap', plugins_url('/admin/scripts/jquery-jvectormap-1.2.2.min.js', $this->PLUGIN_MAIN_FILE), array(), $this->PLUGIN_VERSION);
			wp_enqueue_script($this->PLUGIN_SLUG . '-jquery-jvectormap-world', plugins_url('/admin/scripts/jquery-jvectormap-world-mill-en.js', $this->PLUGIN_MAIN_FILE), array(), $this->PLUGIN_VERSION);

			wp_enqueue_style($this->PLUGIN_SLUG . '-bootstrap', plugins_url('/admin/styles/bootstrap.css', $this->PLUGIN_MAIN_FILE), array(), $this->PLUGIN_VERSION);
			wp_enqueue_style($this->PLUGIN_SLUG . '-morris', plugins_url('/admin/styles/morris.css', $this->PLUGIN_MAIN_FILE), array(), $this->PLUGIN_VERSION);
		}

		wp_enqueue_script($this->PLUGIN_SLUG . '-admin-script', plugins_url('/admin/scripts/gdbc-admin.js', $this->PLUGIN_MAIN_FILE), array('jquery'), $this->PLUGIN_VERSION);

		wp_enqueue_style($this->PLUGIN_SLUG . '-admin-style', plugins_url('/admin/styles/gdbc-admin.css', $this->PLUGIN_MAIN_FILE), array(), $this->PLUGIN_VERSION);

	}

	protected function getPageFooterNotice()
	{
		$screenId = get_current_screen();
		if( empty($screenId->id) || $this->AdminSettingsPageHook !== $screenId->id )
			return;

		$selectedTab = $this->getAdminSettingsCurrentTab();

		if ($selectedTab === GdbcModulesController::MODULE_REPORTS)
			return;


		return  '<div style="border-color:#ffba00;" class="update-nag">
				<span>
				It is strongly recommended to <b>Switch the plugin to Test Mode</b> and test it by yourself!
				<br/>While in test mode, the plugin simulates it\'s real behavior and sends email notifications to administrator.
				<br/>If something goes wrong, a warning message will be shown.
				<br/><b>Having a problem? Asking a question?</b> Do not hesitate to <a href="http://www.goodbyecaptcha.com/contact/">Contact us</a>!
				</span>
				</div>';
	}

	public function initPlugin()
	{
		parent::initPlugin();
	}

	public function adminInitPlugin()
	{
		parent::adminInitPlugin();

		if( ! self::isAjaxRequest() )
		{
			add_action('shutdown', array($this, 'changePluginsLoadOrder'), PHP_INT_MAX);
		}
	}

	public function changePluginsLoadOrder()
	{

		if(null === $this->PLUGIN_MAIN_FILE)
			return;

		if ( ! current_user_can( 'manage_options' ) )
		{
			return;
		}

		$pluginBaseName = plugin_basename($this->PLUGIN_MAIN_FILE);
		$arrBlogActivePlugins = (array) get_option( 'active_plugins', array() );

		$firstActivatedPlugin = reset($arrBlogActivePlugins);
		if(false === $firstActivatedPlugin || $firstActivatedPlugin === $pluginBaseName)
			return;

		$gdbcPluginKey = array_search( $pluginBaseName, $arrBlogActivePlugins );
		if(false === $gdbcPluginKey)
			return;

		array_splice( $arrBlogActivePlugins, $gdbcPluginKey, 1 );
		array_unshift( $arrBlogActivePlugins, $pluginBaseName );

		update_option( 'active_plugins', $arrBlogActivePlugins );
	}
}
