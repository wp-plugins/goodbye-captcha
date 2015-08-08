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

if (!defined('PHP_VERSION_ID'))
{
	$arrVersionParts = explode('.', PHP_VERSION);

	!isset($arrVersionParts[1]) ? $arrVersionParts[1] = 0 : null;
	!isset($arrVersionParts[2]) ? $arrVersionParts[2] = 0 : null;

	define('PHP_VERSION_ID', $arrVersionParts[0] * 10000 + $arrVersionParts[1] * 100 + $arrVersionParts[2]);

	unset($arrVersionParts);
}

final class MchHttpUtil
{

	public static function getIpAddressVersion($ipAddress)
	{
		static $arrIpVersions = array();
		if(isset($arrIpVersions[$ipAddress]))
			return $arrIpVersions[$ipAddress];

		if(false !== filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4))
			return $arrIpVersions[$ipAddress] = 4;

		if(false !== filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6))
			return $arrIpVersions[$ipAddress] = 6;

		return -1;
	}

	public static function ipAddressToBinary($ipAddress)
	{
		if(-1 === ($ipAddressVersion = self::getIpAddressVersion($ipAddress)))
			return null;

		if(PHP_VERSION_ID < 50300 && ('so' !== PHP_SHLIB_SUFFIX))
			return (4 === $ipAddressVersion) ? self::ipv4ToBinary($ipAddress) : self::ipv6ToBinary($ipAddress);


		return false !== ($binStr = current(unpack( (4 === $ipAddressVersion) ? 'A4': 'A16', inet_pton($ipAddress)))) ? $binStr : null;

//		if(4 === $ipAddressVersion)
//		{
//			if(PHP_VERSION_ID < 50300 && ('so' !== PHP_SHLIB_SUFFIX))
//				return self::ipv4ToBinary($ipAddress);
//
//			return false !== ($binStr = current(unpack('A4', inet_pton($ipAddress)))) ? $binStr : null;
//		}
//
//		if(filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6))
//		{
//			if(PHP_VERSION_ID < 50300 && ('so' !== PHP_SHLIB_SUFFIX))
//				return self::ipv6ToBinary($ipAddress);
//
//			return false !== ($binStr = current(unpack('A16', inet_pton($ipAddress)))) ? $binStr : null;
//		}

		return null;
	}

	private static function ipv4ToBinary($ipAddress)
	{
		return  pack('N',ip2long($ipAddress));
	}

	private static function binaryToIpV4($binaryString)
	{
		$decode = unpack('N', $binaryString);
		return isset($decode[1]) ? long2ip($decode[1]) : null;
	}

	private static function ipv6ToBinary($ipAddress)
	{
		$binary = explode(':', $ipAddress);
		$binaryCount = count($binary);
		if (($doub = array_search('', $binary, 1)) !== false)
		{
			$length = (!$doub || $doub === ($binaryCount - 1) ? 2 : 1);
			array_splice($binary, $doub, $length, array_fill(0, 8 + $length - $binaryCount, 0));
		}

		$binary = array_map('hexdec', $binary);
		array_unshift($binary, 'n*');
		$binary = call_user_func_array('pack', $binary);

		return $binary;

	}

	private static function binaryToIpV6($binaryString)
	{
		return preg_replace(
			array('/(?::?\b0+\b:?){2,}/', '/\b0+([^0])/e'),
			array('::', '(int)"$1"?"$1":"0$1"'),
			substr(chunk_split(bin2hex($binaryString), 4, ':'), 0, -1));
	}

	public static function ipAddressFromBinary($binaryString)
	{
		$strLength = strlen($binaryString);

		if(PHP_VERSION_ID < 50300 && ('so' !== PHP_SHLIB_SUFFIX))
		{
			if (4 === $strLength)
				return self::binaryToIpV4($binaryString);

			if (16 === $strLength)
				return self::binaryToIpV6($binaryString);
		}

		return ($strLength === 4 || $strLength === 16) ?
			false !== ($ipAddress = inet_ntop(pack('A'.$strLength, $binaryString))) ? $ipAddress : null : null;
	}

	public static function isPublicIpAddress($ipAddress)
	{
		if(0 === strpos($ipAddress, '127.0.0.'))
			return false;

		return false !== filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
	}

	public static function isValidIpAddress($ipAddress)
	{
		return (-1 !== self::getIpAddressVersion($ipAddress));
	}

	public static function getNumberFromIpV4($ipAddress)
	{
		return sprintf("%u", ip2long($ipAddress));
	}

	public static function getIpV4FromNumber($number)
	{
		return long2ip(-(4294967295 - ($number - 1)));
	}

	public static function getCountryCodeByIp($ipAddress)
	{
		$filePath = dirname(__FILE__) . '/ip-country.txt';
		if(!file_exists($filePath))
			return null;

		if(false === filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4))
			return null;

		if(!self::isPublicIpAddress($ipAddress))
			return null;

		$ipAddress = self::getNumberFromIpV4($ipAddress);

		$fileHandle = fopen($filePath, 'r');

		while(!feof($fileHandle))
		{
			$arrLine = explode(' ', fgets($fileHandle));

			if(!isset($arrLine[0]))
				continue;

			if($ipAddress > (int)$arrLine[0])
				continue;

			fclose($fileHandle);
			return $arrLine[1];
		}

		fclose($fileHandle);
		return null;
	}

	public static function isIpInRanges($ipAddress, array $arrCIDRRange, $ipVersion = -1)
	{
		foreach($arrCIDRRange as $cidrRange)
		{
			if(self::isIpInRange($ipAddress, $cidrRange, $ipVersion))
				return true;
		}

		return false;
	}

	public static function isIpInRange($ipAddress, $cidrRange, $ipVersion = -1)
	{
		list($ipAddressRange, $netMask) = explode('/', $cidrRange, 2);

		if(!isset($ipAddressRange) || !isset($netMask) || $netMask < 0)
			return false;

		(4 !== $ipVersion || 6 !== $ipVersion) ? $ipVersion = -1 : null;

		if( (-1 === $ipVersion) && false !== filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4))
		{
			$ipVersion = 4;
		}
		elseif( (-1 === $ipVersion) &&  false !== filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6))
		{
			$ipVersion = 6;
		}

		if($ipVersion === -1)
			return false;

		if($ipVersion === 4)
		{
			return 0 === self::compareIPV4($ipAddress, $ipAddressRange, $netMask);
		}

		return 0 === self::compareIPV6($ipAddress, $ipAddressRange, $netMask);

	}



	private static function compareIPV4($firstIpAddress, $secondIpAddress, $netMask)
	{
		return substr_compare(sprintf('%032b', ip2long($firstIpAddress)), sprintf('%032b', ip2long($secondIpAddress)), 0, $netMask);
	}

	private static function compareIPV6($firstIpAddress, $secondIpAddress, $netMask)
	{
		$bytesAddr = unpack("n*", self::ipAddressToBinary($secondIpAddress));
		$bytesTest = unpack("n*", self::ipAddressToBinary($firstIpAddress));

		for ($i = 1, $ceil = ceil($netMask / 16); $i <= $ceil; ++$i)
		{
			($left = $netMask - 16 * ($i-1)) > 16 ? $left = 16 : null;

			$mask = ~(0xffff >> $left) & 0xffff;

			if (($bytesAddr[$i] & $mask) != ($bytesTest[$i] & $mask))
				return -1;
		}

		return 0;
	}


	public static function getServerIPAddress()
	{
		static $serverIp = null;

		return null !== $serverIp ? $serverIp : $serverIp = (PHP_VERSION_ID < 50300 ? gethostbyname(php_uname('n')) : gethostbyname(getHostName()));
	}
}