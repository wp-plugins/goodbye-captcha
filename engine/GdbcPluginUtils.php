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

final class GdbcPluginUtils
{
	public static function isWooCommerceActivated()
	{
		return class_exists('WooCommerce', false);
	}

	public static function isUltimateMemberActivated()
	{
		return class_exists('UM_API', false);
	}

	public static function isUjiCountDownActivated()
	{
		return class_exists('Uji_Countdown', false);
	}

	public static function isMailChimpLiteActivated()
	{
		return function_exists('mc4wp_load_plugin') || function_exists('mc4wp_pro_load_plugin');
	}

	public static function isNinjaFormsActivated()
	{
		return class_exists('Ninja_Forms', false);
	}
	
	public static function isGravityFormsActivated()
	{
		return class_exists('GFForms', false);
	}
	
	public static function isContactForm7Activated()
	{
		return class_exists('WPCF7_ContactForm', false);
	}

	public static function isFastSecureFormActivated()
	{
		return class_exists('FSCF_Util', false);
	}

	public static function isFormidableFormsActivated()
	{
		return class_exists('FrmSettings', false);
	}

	public static function isUserProPluginActivated()
	{
		return class_exists('userpro_api', false);
	}

	public static function setCookie($cookieKey, $cookieValue, $cookieTime, $path = null, $httpOnly = true)
	{
		if(headers_sent()) return;
		return setcookie($cookieKey, $cookieValue, $cookieTime  + (isset($_SERVER['REQUEST_TIME']) ? $_SERVER['REQUEST_TIME'] : time()), empty($path) ? COOKIEPATH : $path, COOKIE_DOMAIN, is_ssl(), $httpOnly);
	}
	
	public static function getCookie($cookieKey)
	{
		return isset($_COOKIE[$cookieKey]) ? $_COOKIE[$cookieKey] : null;
	}

	public static function deleteCookie($cookieKey)
	{
		if(headers_sent()) return;
		return setcookie($cookieKey, null, (isset($_SERVER['REQUEST_TIME']) ? $_SERVER['REQUEST_TIME'] : time()) - 3600);
	}
	
	public static function isJetPackContactFormModuleActivated()
	{
		return self::isJetPackModuleActivated('contact-form');
	}	
	
	public static function isJetPackCommentsModuleActivated()
	{
		return self::isJetPackModuleActivated('comments');
	}

	public static function isValidReferer()
	{
		static $validReferer = null;
		if(null !== $validReferer)
			return $validReferer;

		$referer    = wp_get_referer();
		$actualHost = parse_url(home_url(), PHP_URL_HOST);

		return $validReferer = (!empty($referer) && !empty($actualHost) && false !== stripos($referer, $actualHost));
	}

	private static function isJetPackModuleActivated($moduleName)
	{
		static $arrActivatedModules = array();
		if(isset($arrActivatedModules[$moduleName]))
			return $arrActivatedModules[$moduleName];
		
		return $arrActivatedModules[$moduleName] = ((null !== ($arrJetPackModules = self::getJetPackActiveModules())) && 
													in_array(strtolower($moduleName), $arrJetPackModules, true));
	}
	
	private static function getJetPackActiveModules()
	{
		static $isActivated = null;
		(null === $isActivated) ? $isActivated = class_exists( 'Jetpack', false) : null;
		
		if( !$isActivated)
			return null;
		
		static $arrJetPackOptions = null;
		if(null !== $arrJetPackOptions)
			return $arrJetPackOptions;
		
		$arrJetPackOptions = get_option('jetpack_active_modules');
		if(false === $arrJetPackOptions)
			return null;
		
		foreach ($arrJetPackOptions as &$moduleName)
			$moduleName = strtolower(trim($moduleName));

		return $arrJetPackOptions;
	}

	public static function getMySQLDateTime($time = "now", DateTimeZone $timezone = NULL)
	{
		$dateTime = (null === $timezone) ? new DateTime($time) : new DateTime($time, $timezone);
		return $dateTime->format('Y-m-d H:i:s');
	}

	public static function getMySQLDate($time = "now", DateTimeZone $timezone = NULL)
	{
		$dateTime = (null === $timezone) ? new DateTime($time) : new DateTime($time, $timezone);
		return $dateTime->format('Y-m-d');
	}


}