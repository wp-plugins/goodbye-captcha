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
	public static function getRequestTime()
	{
		static $requestTime = null;

		return (null !== $requestTime) ? $requestTime : $requestTime = (isset($_SERVER['REQUEST_TIME']) ? $_SERVER['REQUEST_TIME'] : time());
	}

	public static function getClientIp(array $arrTrustedProxyIps = array())
	{
		static $clientIp = 0;
		if(0 !== $clientIp )
			return $clientIp;

		if(empty($_SERVER['REMOTE_ADDR']) || -1 === ($ipVersion = MchHttpUtil::getIpAddressVersion($_SERVER['REMOTE_ADDR'])))
			return null;

		if(null !== ($clientIp = self::getIpAddressFromCloudFlare()))
			return $clientIp;

		if(null !== ($clientIp = self::getIpAddressFromRackSpace())) // RackSpace and WPEngine
			return $clientIp;

		if(null !== ($clientIp = self::getIpAddressFromIncapsula()))
			return $clientIp;

		if(null !== ($clientIp = self::getIpAddressFromAmazonCloudFront()))
			return $clientIp;


		$arrProxyHeaders = array(
			'HTTP_X_FORWARDED_FOR',
			'HTTP_CLIENT_IP',
			'HTTP_X_FORWARDED',
			'HTTP_FORWARDED'
		);

		if(!empty($arrTrustedProxyIps) && in_array($_SERVER['REMOTE_ADDR'], $arrTrustedProxyIps, true))
		{
			foreach ($arrProxyHeaders as $proxyHeader)
			{
				if(null !== ($clientIp = self::getClientIpAddressFromProxyHeader($proxyHeader)))
					return $clientIp;
			}
		}

		return $clientIp = $_SERVER['REMOTE_ADDR'];

	}

	private static function getClientIpAddressFromProxyHeader($proxyHeader)
	{
		if(empty($_SERVER[$proxyHeader]))
			return null;

		$arrClientIps = explode(',', $_SERVER[$proxyHeader]);

		if (empty($arrClientIps[0]))
			return null;

		$arrClientIps[0] = str_replace(' ', '', $arrClientIps[0]);

		if (preg_match('{((?:\d+\.){3}\d+)\:\d+}', $arrClientIps[0], $match))
			$arrClientIps[0] = trim($match[1]);

		return (-1 !== MchHttpUtil::getIpAddressVersion($arrClientIps[0])) ? $arrClientIps[0] : null;

	}

	private static function getIpAddressFromRackSpace()
	{

		if(0 !== strpos($_SERVER['REMOTE_ADDR'], '10.'))
			return null;

		$arrProxyHeaders = array('HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_X_FORWARDED_FOR');

		//http://www.rackspace.com/knowledge_center/article/using-cloud-load-balancers-with-rackconnect
		$arrRackSpaceRanges = array(
			'10.183.248.0/22',
			'10.189.252.0/23',
			'10.189.254.0/23',
			'10.183.250.0/23',
			'10.183.252.0/23',
			'10.183.254.0/23',
			'10.189.245.0/24',
			'10.189.246.0/23',
			'10.190.254.0/23',
		);

		foreach($arrProxyHeaders as $proxyHeader)
		{
			if(empty($_SERVER[$proxyHeader]))
				continue;

			if(null === ($ipAddress = self::getClientIpAddressFromProxyHeader($proxyHeader)))
				continue;

			if(!MchHttpUtil::isIpInRanges($_SERVER['REMOTE_ADDR'], $arrRackSpaceRanges, 4))
				continue;

			return $ipAddress;
		}

		return null;
	}

	private static function getIpAddressFromIncapsula()
	{

		if(empty($_SERVER['HTTP_INCAP_CLIENT_IP']) || -1 === ($ipVersion = MchHttpUtil::getIpAddressVersion($_SERVER['HTTP_INCAP_CLIENT_IP'])))
			return null;

		//https://incapsula.zendesk.com/hc/en-us/articles/200627570-Restricting-direct-access-to-your-website-Incapsula-s-IP-addresses-
		//curl -k -s --data "resp_format=json" https://my.incapsula.com/api/integration/v1/ips

		$arrIncapsulaRanges = ( 4 === $ipVersion )
			?
			array(
				'199.83.128.0/21','198.143.32.0/19','149.126.72.0/21','103.28.248.0/22','185.11.124.0/22','192.230.64.0/18','45.64.64.0/22',
			)
			: array(
				'2620:28:4000::/44', '2a02:e980::/29',
			);

		return MchHttpUtil::isIpInRanges($_SERVER['REMOTE_ADDR'], $arrIncapsulaRanges, $ipVersion) ? $_SERVER['HTTP_INCAP_CLIENT_IP'] : null;
	}


	private static function getIpAddressFromCloudFlare()
	{
		if(empty($_SERVER['HTTP_CF_CONNECTING_IP']) || -1 === ($ipVersion = MchHttpUtil::getIpAddressVersion($_SERVER['HTTP_CF_CONNECTING_IP'])))
			return null;

		//https://www.cloudflare.com/ips

		$arrCloudFlareRanges = ( 4 === $ipVersion )
			?
			array(
				'199.27.128.0/21',
				'173.245.48.0/20',
				'103.21.244.0/22',
				'103.22.200.0/22',
				'103.31.4.0/22',
				'141.101.64.0/18',
				'108.162.192.0/18',
				'190.93.240.0/20',
				'188.114.96.0/20',
				'197.234.240.0/22',
				'198.41.128.0/17',
				'162.158.0.0/15',
				'104.16.0.0/12',
			)
			: array(
				'2400:cb00::/32',
				'2606:4700::/32',
				'2803:f800::/32',
				'2405:b500::/32',
				'2405:8100::/32',
			);

		return MchHttpUtil::isIpInRanges($_SERVER['REMOTE_ADDR'], $arrCloudFlareRanges, $ipVersion) ? $_SERVER['HTTP_CF_CONNECTING_IP'] : null;
	}


	private static function getIpAddressFromAmazonCloudFront()
	{

		if(!empty($_SERVER['HTTP_X_AMZ_CF_ID'])
			|| (!empty($_SERVER['HTTP_USER_AGENT']) && $_SERVER['HTTP_USER_AGENT'] === 'Amazon CloudFront')
			|| (!empty($_SERVER['HTTP_VIA']) &&  false !== strpos($_SERVER['HTTP_VIA'], 'CloudFront'))
		)
		{
			#http://docs.aws.amazon.com/general/latest/gr/aws-ip-ranges.html
			#https://ip-ranges.amazonaws.com/ip-ranges.json

			$arrAmazonCloudFront = array(
				'205.251.254.0/24',
				'54.239.192.0/19',
				'204.246.176.0/20',
				'54.230.0.0/16',
				'205.251.250.0/23',
				'205.251.192.0/19',
				'216.137.32.0/19',
				'204.246.164.0/22',
				'205.251.249.0/24',
				'54.192.0.0/16',
				'54.239.128.0/18',
				'54.240.128.0/18',
				'204.246.174.0/23',
				'204.246.168.0/22',
				'205.251.252.0/23',
				'54.182.0.0/16',
			);

			if(null === $proxyIpAddress = self::getClientIpAddressFromProxyHeader('HTTP_X_FORWARDED_FOR'))
				return null;

			return MchHttpUtil::isIpInRanges($_SERVER['REMOTE_ADDR'], $arrAmazonCloudFront, 4) ? $proxyIpAddress : null;

		}

		return null;
	}


}