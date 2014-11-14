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
		
		if( self::WP_VERSION_ID >= 30100 && ($this->AdminSettingsPageHook !== get_current_screen()->id) )
			return;
		
		$selectedTab = $this->getAdminSettingsCurrentTab();
		if ($selectedTab === GdbcModulesController::MODULE_REPORTS)
		{
			wp_enqueue_script( $this->PLUGIN_SLUG . '-jquery-flot', plugins_url( '/admin/scripts/jquery-flot.js', $this->PLUGIN_MAIN_FILE ), array("jquery"), $this->PLUGIN_VERSION);
			wp_enqueue_script( $this->PLUGIN_SLUG . '-admin-script', plugins_url( '/admin/scripts/gdbc-admin.js', $this->PLUGIN_MAIN_FILE ), array(), $this->PLUGIN_VERSION);
			wp_enqueue_script( $this->PLUGIN_SLUG . '-bootstrap', plugins_url( '/admin/scripts/bootstrap.min.js', $this->PLUGIN_MAIN_FILE ),  array(), $this->PLUGIN_VERSION);
			wp_enqueue_script( $this->PLUGIN_SLUG . '-gdbc-dashboard', plugins_url( '/admin/scripts/gdbc-dashboard.js', $this->PLUGIN_MAIN_FILE ),  array(), $this->PLUGIN_VERSION);
			wp_enqueue_script( $this->PLUGIN_SLUG . '-jquery-ui', plugins_url( '/admin/scripts/jquery-ui-1.10.3.min.js', $this->PLUGIN_MAIN_FILE ),  array(), $this->PLUGIN_VERSION);
			wp_enqueue_script( $this->PLUGIN_SLUG . '-easy-pie-chart', plugins_url( '/admin/scripts/easy-pie-chart.js', $this->PLUGIN_MAIN_FILE ),  array(), $this->PLUGIN_VERSION);
			wp_enqueue_script( $this->PLUGIN_SLUG . '-jquery-jvectormap', plugins_url( '/admin/scripts/jquery-jvectormap-1.2.2.min.js', $this->PLUGIN_MAIN_FILE ),  array(), $this->PLUGIN_VERSION);
			wp_enqueue_script( $this->PLUGIN_SLUG . '-jquery-jvectormap-world', plugins_url( '/admin/scripts/jquery-jvectormap-world-mill-en.js', $this->PLUGIN_MAIN_FILE ),  array(), $this->PLUGIN_VERSION);
			wp_enqueue_script( $this->PLUGIN_SLUG . '-jquery-dataTables', plugins_url( '/admin/scripts/jquery.dataTables.js', $this->PLUGIN_MAIN_FILE ),  array(), $this->PLUGIN_VERSION);
			
			wp_enqueue_style( $this->PLUGIN_SLUG . '-bootstrap', plugins_url( '/admin/styles/bootstrap.css', $this->PLUGIN_MAIN_FILE ),  array(), $this->PLUGIN_VERSION);
			wp_enqueue_style( $this->PLUGIN_SLUG . '-admin-style', plugins_url( '/admin/styles/gdbc-admin.css', $this->PLUGIN_MAIN_FILE ),  array(), $this->PLUGIN_VERSION);		
		}
		
	}
	
}
