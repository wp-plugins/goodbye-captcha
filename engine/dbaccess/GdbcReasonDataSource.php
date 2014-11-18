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

final class GdbcReasonDataSource 
{
	CONST TOKEN_INVALID         = 1;
	CONST TOKEN_MISSING         = 2;
	CONST TOKEN_EXPIRED         = 3;
	CONST TOKEN_SUBMITTED_EARLY = 4;

	CONST CLIENT_IP_BLOCKED     = 5;

	CONST BROWSER_INFO_MISSING  = 6;
	CONST BROWSER_INFO_INVALID  = 7;

	public static function getReasonDescription($reasonId)
	{
		static $arrReasonDescription = null;
		if(null === $arrReasonDescription)
		{
			$arrReasonDescription =  array(
				self::TOKEN_INVALID         => __('Invalid Token',         GoodByeCaptcha::PLUGIN_SLUG),
				self::TOKEN_MISSING         => __('Token Not Submitted',   GoodByeCaptcha::PLUGIN_SLUG),
				self::TOKEN_EXPIRED         => __('Token Expired',         GoodByeCaptcha::PLUGIN_SLUG),
				self::TOKEN_SUBMITTED_EARLY => __('Token Submitted Early', GoodByeCaptcha::PLUGIN_SLUG),
				self::CLIENT_IP_BLOCKED     => __('Client IP Blocked',     GoodByeCaptcha::PLUGIN_SLUG),
				self::BROWSER_INFO_MISSING  => __('Browser Info Missing',  GoodByeCaptcha::PLUGIN_SLUG),
				self::BROWSER_INFO_INVALID  => __('Browser Info Invalid',  GoodByeCaptcha::PLUGIN_SLUG),
			);
		}

		return isset($arrReasonDescription[$reasonId]) ? $arrReasonDescription[$reasonId] : __('Unknown', GoodByeCaptcha::PLUGIN_SLUG);
	}

}
