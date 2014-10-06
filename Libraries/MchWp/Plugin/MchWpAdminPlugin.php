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

abstract class MchWpAdminPlugin extends MchWpPlugin
{
	protected $AdminSettingsPageHook = null;
	
	public abstract function getPluginAdminActionLinks(array $pluginActionLinks);
	
	public abstract function enqueueAdminScriptsAndStyles();
	public abstract function activateForNewSite($blogId);
	
	protected function __construct(array $arrPluginInfo) 
	{
		parent::__construct($arrPluginInfo);

		add_action('admin_init', array($this, 'activateAdminModulesSettings'));

		add_filter('plugin_action_links_' . plugin_basename($this->PLUGIN_MAIN_FILE) , array( $this, 'getPluginAdminActionLinks' ) );

		add_action('admin_enqueue_scripts', array( $this, 'enqueueAdminScriptsAndStyles' ));
		//add_action('admin_enqueue_scripts', array( $this, 'enqueueAdminScriptsAndStyles' ));
		
		add_action('wpmu_new_blog', array( $this, 'activateForNewSite' ));
		
	}
	
	
	public function activateAdminModulesSettings()
	{
		foreach (array_keys($this->ModulesController->getRegisteredModules()) as $moduleName)
		{
			$this->ModulesController->getModuleInstance($moduleName, MchWpModule::MODULE_TYPE_ADMIN)->activateAdminSetting();
		}
	}

	public function renderPluginAdminPage()
	{

		$code  = '<div class="wrap container-fluid">' . $this->getAdminSettingsTabsCode();
		
		$code .= '<form method="post" action="options.php">';

		ob_start();
		
		$moduleSetting = $this->ModulesController->getModuleSetting($this->getAdminSettingsCurrentTab());
		
		if(null !== $moduleSetting)
		{

			settings_fields($moduleSetting->SettingGroup);
			do_settings_sections($moduleSetting->SettingGroup);
			
			$moduleOptions = $moduleSetting->getDefaultOptions();
			if (!empty($moduleOptions))
				submit_button();
			
		}

		
		$code .= ob_get_clean();
		
		$code .= '</form>';
		
		$code .= '</div>';
		
		echo $code;
	}
	
	
	
	
	private function getAdminSettingsTabsCode()
	{

		$activeSettingsTab = $this->getAdminSettingsCurrentTab();
		
		$htmlCode =  '<h2 class="nav-tab-wrapper">';
		
		foreach (array_keys($this->ModulesController->getRegisteredModules()) as $moduleName)
		{
			
			$htmlCode .= '<a class="nav-tab ' . (($moduleName === $activeSettingsTab) ? 'nav-tab-active' : '') . '" href="?page=' . $this->PLUGIN_SLUG . '&tab=' . $moduleName . '">';
			
			if( null === ($tabCaption = $this->ModulesController->getModuleInstance($moduleName, MchWpModule::MODULE_TYPE_ADMIN)->getModuleSettingTabCaption()))
			{
				$tabCaption = $moduleName;
			}
			
			$htmlCode .= $tabCaption . '</a>';
		}
		
		return $htmlCode .= '</h2>';
		
	}
	
	protected function getAdminSettingsCurrentTab()
	{
		$arrModules = $this->ModulesController->getRegisteredModules();
		return !empty($_GET['tab']) && isset($arrModules[$_GET['tab']]) ? $_GET['tab'] : key($arrModules);
		
	}
	
	
	protected static function getAllBlogIds()
	{
		global $wpdb;
		
		if( empty($wpdb->blogs) )
			return array();
		
		$sql = "SELECT blog_id FROM $wpdb->blogs WHERE archived = '0' AND spam = '0' AND deleted = '0'";

		return false === ( $arrBlogs = $wpdb->get_col( $sql ) ) ? array() : $arrBlogs;
		
	}

	
}