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

abstract class MchWpBase implements MchWpIBase
{
	protected  $PLUGIN_VERSION        = null;
	//protected  $PLUGIN_VERSION_ID     = null;
	protected  $PLUGIN_SLUG           = null;
	protected  $PLUGIN_MAIN_FILE      = null;
	protected  $PLUGIN_SHORT_CODE     = null;
	
	protected  $PLUGIN_DOMAIN_PATH    = null;
	
	protected  $PLUGIN_DIRECTORY_PATH = null;
	protected  $PLUGIN_DIRECTORY_NAME = null;

	protected  $ArrPluginInfo          = null;
	
	protected function __construct(array $arrPluginInfo)
	{
		$this->PLUGIN_SLUG           = isset($arrPluginInfo['PLUGIN_SLUG'])        ? $arrPluginInfo['PLUGIN_SLUG'] : null;
		$this->PLUGIN_VERSION        = isset($arrPluginInfo['PLUGIN_VERSION'])     ? $arrPluginInfo['PLUGIN_VERSION'] : null;
		$this->PLUGIN_MAIN_FILE      = isset($arrPluginInfo['PLUGIN_MAIN_FILE'])   ? $arrPluginInfo['PLUGIN_MAIN_FILE'] : null;
		$this->PLUGIN_SHORT_CODE     = isset($arrPluginInfo['PLUGIN_SHORT_CODE'])  ? $arrPluginInfo['PLUGIN_SHORT_CODE'] : null;
		$this->PLUGIN_DOMAIN_PATH    = isset($arrPluginInfo['PLUGIN_DOMAIN_PATH']) ? trim($arrPluginInfo['PLUGIN_DOMAIN_PATH'], '/\\') : null;

		$this->PLUGIN_DIRECTORY_PATH = (null !== $this->PLUGIN_MAIN_FILE      ? dirname($this->PLUGIN_MAIN_FILE) : null);

		if(function_exists('plugin_basename'))
		$this->PLUGIN_DIRECTORY_NAME = (null !== $this->PLUGIN_DIRECTORY_PATH ? plugin_basename($this->PLUGIN_DIRECTORY_PATH) : null);

		$this->ArrPluginInfo         = $arrPluginInfo;
	}


	public static function getPluginVersionIdFromString($strVersion)
	{
		static $arrVersions = array();

		if(isset($arrVersions[$strVersion]))
			return $arrVersions[$strVersion];

		$arrVersionParts = explode('.', $strVersion);
		!isset($arrVersionParts[1]) ? $arrVersionParts[1] = 0 : null;
		!isset($arrVersionParts[2]) ? $arrVersionParts[2] = 0 : null;

		return $arrVersions[$strVersion] = $arrVersionParts[0] * 10000 + $arrVersionParts[1] * 100 + $arrVersionParts[2];
	}

	public static function isUserLoggedIn()
	{
		static $isLoggedIn = null;
		
		return (null !== $isLoggedIn) ? $isLoggedIn : $isLoggedIn = is_user_logged_in();
	}
	
	
	public static function isAdminLoggedIn()
	{
		return self::isSuperAdminLoggedIn();
		//static $isLoggedIn = null;
		//return (null !== $isLoggedIn) ? $isLoggedIn : $isLoggedIn = self::isUserLoggedIn() && current_user_can( 'manage_options' );
	}
	
	public static function isSuperAdminLoggedIn()
	{
		static $isLoggedIn = null;
		
		return (null !== $isLoggedIn) ? $isLoggedIn : $isLoggedIn = is_super_admin();
	}
	
	public static function isUserInDashboad()
	{
		static $isUserInDashboard = null;
		
		return (null !== $isUserInDashboard) ? $isUserInDashboard : $isUserInDashboard = (is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) );
	}
	
	public static function isAdminInDashboard()
	{
		return self::isAdminLoggedIn() && self::isUserInDashboad();
	}

	public static function isAjaxRequest()
	{
		static $isAjaxDashboardRequest = null;

		return (null !== $isAjaxDashboardRequest) ? $isAjaxDashboardRequest : $isAjaxDashboardRequest = (is_admin() && defined( 'DOING_AJAX' ) && DOING_AJAX );
	}
	
	public static function isMultiSite()
	{
		static $isMultisite = null;
		
		return (null !== $isMultisite) ? $isMultisite :  $isMultisite = function_exists( 'is_multisite' ) && is_multisite();
	}

	public static function getAdminEmailAddress()
	{
		return get_bloginfo('admin_email');
	}

	public static function getAdminDisplayName()
	{
		$adminUser = get_user_by('email', get_bloginfo('admin_email'));
		if(false === $adminUser)
			return null;

		return !empty($adminUser->display_name) ? $adminUser->display_name : null;
	}


	public static function getAdminFullName()
	{
		$adminUser = get_user_by('email', get_bloginfo('admin_email'));
		if(false === $adminUser)
			return null;

		$adminFullName  = empty($adminUser->first_name) ? '' : $adminUser->first_name;
		$adminFullName .= empty($adminUser->last_name)  ? '' : ' ' . $adminUser->last_name;

		return trim($adminFullName);

	}

	public static function hasPermalinkActivated()
	{
		static $isActivated = null;
		return (null !== $isActivated) ? $isActivated : $isActivated = (bool)(get_option('permalink_structure'));
	}
	
}