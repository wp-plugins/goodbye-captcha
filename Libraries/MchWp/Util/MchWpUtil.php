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

final class MchWpUtil
{

	public static function getAjaxUrl()
	{
		$ajaxUrl = admin_url('admin-ajax.php', self::isSslRequest() ? 'admin' : 'http');

		if(0 === strpos(self::getCurrentPageUrl(), 'https') && 0 !== strpos($ajaxUrl, 'https'))
			return  str_replace('http:', 'https:', $ajaxUrl);

		if(0 === strpos(self::getCurrentPageUrl(), 'http:') && 0 !== strpos($ajaxUrl, 'http:'))
			return str_replace('https:', 'http:', $ajaxUrl);

		return $ajaxUrl;
	}

	public static function isSslRequest()
	{
		static $isSsl = null;
		if(null !== $isSsl)
			return $isSsl;

		if (isset($_SERVER['HTTP_CF_VISITOR']) && strpos($_SERVER['HTTP_CF_VISITOR'], 'https'))
			return $isSsl = true;

		if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && stripos($_SERVER['HTTP_X_FORWARDED_PROTO'], 'https') === 0)
			return $isSsl = true;

		return $isSsl = is_ssl();
	}

	public static function getCurrentPageUrl()
	{
		static $pageUrl = null;

		if(null !== $pageUrl)
			return $pageUrl;

		if(is_front_page())
			return $pageUrl = home_url();

		$pageUrl = self::isSslRequest() ? 'https://' : 'http://';

		if(isset($_SERVER['SERVER_PORT']) && ($_SERVER['SERVER_PORT'] != 80))
			$pageUrl .= $_SERVER['SERVER_NAME' ]. ':' . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI'];
		else
			$pageUrl .= $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

		return $pageUrl = esc_url($pageUrl);

	}

	public static function stripNonAlphaCharacters($strText)
	{
		return preg_replace("/[^a-z]/i", '', $strText );
	}


	public static function replaceNonAlphaCharacters($strText, $token = '-')
	{
		$strText = str_replace(' ', $token, $strText);
		$strText = preg_replace('/[^A-Za-z\-]/', $token, $strText);
		$strText = preg_replace('/-+/', $token, trim($strText, '-'));


		return $strText;
	}

	public static function replaceNonAlphaNumericCharacters($strText, $token = '-')
	{
		$strText = str_replace(' ', $token, $strText);
		$strText = preg_replace('/[^A-Za-z0-9\-]/', $token, $strText);
		$strText = preg_replace('/-+/', $token, trim($strText, '-'));


		return $strText;
	}

	public static function stripLeftAndRightSlashes($str)
	{
		return trim($str, '/\\');
	}


	public static function stripLeftSlashes($str)
	{
		return ltrim($str, '/\\');
	}

	public static function stripRightSlashes($str)
	{
		return rtrim($str, '/\\');
	}

}