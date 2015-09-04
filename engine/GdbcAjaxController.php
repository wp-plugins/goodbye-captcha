<?php
/** 
 * Copyright (C) 2015 Mihai Chelaru
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

defined( 'ABSPATH' ) || exit;

final class GdbcAjaxController
{
	CONST ACTION_RETRIEVE_TOKEN = 'retrieveToken';

	public static function processRequest()
	{

		if(self::isPublicGdbcAjaxRequest())
		{
			GdbcTokenController::getInstance()->retrieveEncryptedToken();
			exit;
		}

	}

	private static function isPublicGdbcAjaxRequest()
	{
		if(!(!empty($_POST['action']) && !empty($_POST['browserInfo']) && (self::ACTION_RETRIEVE_TOKEN === $_POST['action'])))
			return false;

		( !defined('LOGGED_IN_COOKIE') && function_exists('wp_cookie_constants') ) ? wp_cookie_constants() : null;

		require_once( ABSPATH . WPINC . '/pluggable.php' );
		require_once( ABSPATH . 'wp-admin/includes/ajax-actions.php' );

		wp_magic_quotes();
		send_origin_headers();

		@header('Content-Type: application/json; charset=' . get_option( 'blog_charset' ));
		@header('X-Robots-Tag: noindex' );

		send_nosniff_header();
		nocache_headers();

		@header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

		return true;
	}


}