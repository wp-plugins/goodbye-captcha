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

class MchHttpRequest
{
	public static function getClientIp($arrTrustedProxyIps = array())
	{
		if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && isset($_SERVER['REMOTE_ADDR']) && in_array($_SERVER['REMOTE_ADDR'], $arrTrustedProxyIps))
		{
			$arrClientIps = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
			return  isset($arrClientIps[0]) ? $arrClientIps[0] : null;
		}

		if(isset($_SERVER['HTTP_CLIENT_IP']) && isset($_SERVER['REMOTE_ADDR']) && in_array($_SERVER['REMOTE_ADDR'], $arrTrustedProxyIps))
		{
			$arrClientIps = explode(',', $_SERVER['HTTP_CLIENT_IP']);
			return  isset($arrClientIps[0]) ? $arrClientIps[0] : null;
		}

		return isset($_SERVER['REMOTE_ADDR']) ?  $_SERVER['REMOTE_ADDR'] : null;

	}
}