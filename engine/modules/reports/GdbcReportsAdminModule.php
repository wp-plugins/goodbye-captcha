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

final class GdbcReportsAdminModule extends GdbcBaseAdminModule
{	
	CONST GDBC_ITEMS_PER_PAGE                = 20;
	CONST GDBC_MODULES_ITEMS_PER_PAGE        = 10;
	CONST GDBC_ATTEMPTS_ITEMS_FRONT_PAGE     = 10;
	CONST GDBC_ATTEMPTS_NUMBER_OF_DAYS       = 30;
	CONST GDBC_ATTEMPTS_CHART_NUMBER_OF_DAYS = 60;
	CONST TABLE_HEADER_NO                    = 'No';
	CONST TABLE_HEADER_ID                    = 'AttemptId';
	CONST TABLE_HEADER_DATE                  = 'Date';
	CONST TABLE_HEADER_MODULE_SECTION        = 'ModuleSection';
	CONST TABLE_HEADER_CLIENT_IP             = 'ClientIp';
	CONST TABLE_HEADER_IS_IP_BLOCKED         = 'IsIpBlocked';
	CONST TABLE_HEADER_COUNTRY               = 'Country';
	CONST TABLE_HEADER_REASON                = 'Reason';
	CONST TABLE_HEADER_TOTAL                 = 'Total';
	CONST TABLE_HEADER_COMMENTS              = 'Comments';
	CONST TABLE_HEADER_LOGIN                 = 'Login';
	CONST TABLE_HEADER_REGISTRATION          = 'Registration';
	CONST TABLE_HEADER_FORGOT_PASSWORD       = 'ForgotPassword';

	private $moduleSetting                   = null;
	private $registeredModulesArray          = null;
	private $moduleController                = null;

	private $arrDefaultSettingOptions        = array();

	private static $latestAttemptsTableHeader  = array(
												self::TABLE_HEADER_DATE             => 'Time of attempt',
												self::TABLE_HEADER_MODULE_SECTION   => 'Module / Section',
												self::TABLE_HEADER_CLIENT_IP        => 'Client Ip',
												self::TABLE_HEADER_COUNTRY          => 'Country',
												self::TABLE_HEADER_REASON           => 'Reason'
											);
	private static $attemptsLocationsTableHeader = array(
												//self::TABLE_HEADER_NO               => 'No',
												self::TABLE_HEADER_COUNTRY          => 'Country',
												self::TABLE_HEADER_TOTAL            => 'Total',
												self::TABLE_HEADER_COMMENTS         => 'Comments',
												self::TABLE_HEADER_REGISTRATION     => 'Registration',
												self::TABLE_HEADER_LOGIN            => 'Login',
												self::TABLE_HEADER_FORGOT_PASSWORD  => 'Lost Password'
											);

	private static $moduleTableHeaderArray = array (
		'SectionId'   => 'Section',
		'CreatedDate' => 'Date of attempt',
		'CountryId'   => 'Country',
		'ClientIp'    => 'Client Ip'
	);

	protected function __construct(array $arrPluginInfo)
	{			
		$this->moduleSetting = new MchWpSetting(__CLASS__, $this->arrDefaultSettingOptions);
		parent::__construct($arrPluginInfo);

		$this->moduleController = GdbcModulesController::getInstance($arrPluginInfo);
		$this->registeredModulesArray = $this->moduleController->getRegisteredModules();
	}
	
	public function getModuleSetting()
	{
		return $this->moduleSetting;
	}
	
	public function getModuleSettingTabCaption()
	{
		return __('Reports', $this->PLUGIN_SLUG);
	}
	
	protected function getModuleSettingSections()
	{
		$settingSection = new MchWpSettingSection($this->moduleSetting->SettingKey . '-section', __('GoodBye Captcha - Blocked Attempts', $this->PLUGIN_SLUG));

		return array($settingSection);
	}
	
	private function displayContent()
	{
		if (!empty($_GET['report']) && $_GET['report'] == 1)
		{
			$statsPageUrl = '?page=' . $this->PLUGIN_SLUG . '&tab=' . GdbcModulesController::MODULE_REPORTS;
			require_once $this->PLUGIN_DIRECTORY_PATH . '/engine/modules/reports/partials/modules.php';
			require_once $this->PLUGIN_DIRECTORY_PATH . '/engine/modules/reports/partials/module-table.php';

			return;
		}

		$wpSectionsPercentageArray   = $this->getSectionsPercentageArray();
		$modulesPageUrl              = $_SERVER['REQUEST_URI'] . "&report=1";

		$adminModuleInstance  = $this->moduleController->getAdminModuleInstance(GdbcModulesController::MODULE_WORDPRESS);
		$wpSectionOptionsInfo = array();
		foreach($adminModuleInstance->getModuleSetting()->getDefaultOptions() as $optionName => $optionValue)
		{
			$wpSectionOptionsInfo[$optionName]['id']           = $adminModuleInstance->getSettingOptionIdByOptionName($optionName);
			$wpSectionOptionsInfo[$optionName]['display-text'] = $adminModuleInstance->getSettingOptionDisplayTextByOptionId($wpSectionOptionsInfo[$optionName]['id']);
		}

		$topAttemptsLocationsArray    =  GdbcAttemptsManager::getTopAttemptsLocations($wpSectionOptionsInfo, self::GDBC_ATTEMPTS_ITEMS_FRONT_PAGE );
		if( null === $topAttemptsLocationsArray )
            $topAttemptsLocationsArray = array();

        $latestAttemptsLocationHeader = $this->getHeaderOfAttemptsLocationsArray();
		$latestAttemptsLocationArray  = $this->getAttemptsLocationsArray($topAttemptsLocationsArray);
		$latestAttemptsLocationJs     = $this->getTopCountriesJs($topAttemptsLocationsArray);

		require_once $this->PLUGIN_DIRECTORY_PATH . '/engine/modules/reports/partials/reports-header.php';
		require_once $this->PLUGIN_DIRECTORY_PATH . '/engine/modules/reports/partials/main-charts.php';
		require_once $this->PLUGIN_DIRECTORY_PATH . '/engine/modules/reports/partials/latest-attempts-table.php';
		require_once $this->PLUGIN_DIRECTORY_PATH . '/engine/modules/reports/partials/section-start.php';
		require_once $this->PLUGIN_DIRECTORY_PATH . '/engine/modules/reports/partials/latest-attempts-locations.php';
		require_once $this->PLUGIN_DIRECTORY_PATH . '/engine/modules/reports/partials/percentage-chart.php';
		require_once $this->PLUGIN_DIRECTORY_PATH . '/engine/modules/reports/partials/section-end.php';
		require_once $this->PLUGIN_DIRECTORY_PATH . '/engine/modules/reports/partials/reports-footer.php';

	}

	private function getTopCountriesJs($topAttemptsLocationsArray)
	{
		if (!isset($topAttemptsLocationsArray[0]))
			return '';

		$countriesJs = '';
		foreach ($topAttemptsLocationsArray as $row)
		{
			if(null === $countryCode = GdbcCountryDataSource::getCountryCodeById($row->CountryId))
				continue;

			$countriesJs .= '"' . $countryCode . '" : ' . $row->Total . ',';
		}

		return rtrim($countriesJs, ',');
	}

	private function getAttemptsChartArray()
	{
		$attemptsChartArray = array();

		$timestamp = strtotime(current_time('mysql'));

		for ($i = 0 ; $i <= self::GDBC_ATTEMPTS_CHART_NUMBER_OF_DAYS ; ++$i)
		{
			$day = date('Y-m-d', $timestamp);
			$attemptsChartArray[$day] = 0;
			$timestamp -= 24 * 3600;
		}

		$attemptsArray = GdbcAttemptsManager::getAttemptsChartArray(self::GDBC_ATTEMPTS_CHART_NUMBER_OF_DAYS);
        if (null === $attemptsArray)
            $attemptsArray = array();

		foreach ($attemptsArray as $attempt)
		{
			if (!isset($attemptsChartArray[$attempt->CreatedDate]))
				continue;

			$attemptsChartArray[$attempt->CreatedDate] = $attempt->AttemptsNumber;
		}

		$resultArray = array();
		foreach ( $attemptsChartArray as $attemptDate => $attemptNumber)
		{
			$resultArray[strtotime($attemptDate) . '000'] = $attemptNumber;
		}

		return $resultArray;
	}

	private function getSectionsPercentageArray()
	{
		$adminModuleInstance  = $this->moduleController->getAdminModuleInstance(GdbcModulesController::MODULE_WORDPRESS);
		$moduleId = $this->moduleController->getModuleIdByName(GdbcModulesController::MODULE_WORDPRESS);

		$wpSectionOptionsInfo = array();
		foreach($adminModuleInstance->getModuleSetting()->getDefaultOptions() as $optionName => $optionValue)
		{
			$optionId = $adminModuleInstance->getSettingOptionIdByOptionName($optionName);
			if ($optionId > 4)
				continue;
			$wpSectionOptionsInfo[$optionName]['id']           = $optionId;
			$wpSectionOptionsInfo[$optionName]['display-text'] = $adminModuleInstance->getSettingOptionDisplayTextByOptionId($wpSectionOptionsInfo[$optionName]['id']);
		}

		if(empty($wpSectionOptionsInfo))
			return array();

		$latestTotalAttemptsPerSections   =  GdbcAttemptsManager::getTotalAttemptsPerModuleSection($moduleId, $wpSectionOptionsInfo, self::GDBC_ATTEMPTS_NUMBER_OF_DAYS, 0);
		$previousTotalAttemptsPerSections = GdbcAttemptsManager::getTotalAttemptsPerModuleSection($moduleId, $wpSectionOptionsInfo, self::GDBC_ATTEMPTS_NUMBER_OF_DAYS, self::GDBC_ATTEMPTS_NUMBER_OF_DAYS );

		if ( !isset($latestTotalAttemptsPerSections[0]) || !isset($previousTotalAttemptsPerSections[0]) )
			return array();

		$latestArray   = get_object_vars($latestTotalAttemptsPerSections[0]);
		$previousArray = get_object_vars($previousTotalAttemptsPerSections[0]);

		$percentageArray = array();
		foreach($previousArray as $key => $value)
		{
			if ($value > 0) {
				$percentageArray[0][$key] = floor(100 * ($latestArray[$key] / $value));
			}
			else {
            	if ($latestArray[$key] > 0)
					$percentageArray[0][$key] = 100;
				else
					$percentageArray[0][$key] = 0;
			}

			$percentageArray[1][$key] = $latestArray[$key];
			$percentageArray[2][$key] = $value;
		}

		return $percentageArray;
	}

	private function getProModulesPercentageArray()
	{
		$arrModulesIds = array();
		foreach($this->registeredModulesArray as $moduleName => $moduleInfo)
		{
			$arrModulesIds[$moduleName] = $this->moduleController->getModuleIdByName($moduleName);
			if(!isset($arrModulesIds[$moduleName]))
				unset($arrModulesIds[$moduleName]);
		}

		$latestTotalAttemptsPerModules   = GdbcAttemptsManager::getTotalAttemptsPerModule($arrModulesIds, self::GDBC_ATTEMPTS_NUMBER_OF_DAYS, 0);
		$previousTotalAttemptsPerModules = GdbcAttemptsManager::getTotalAttemptsPerModule($arrModulesIds, self::GDBC_ATTEMPTS_NUMBER_OF_DAYS, self::GDBC_ATTEMPTS_NUMBER_OF_DAYS );

		if (!isset($latestTotalAttemptsPerModules[0]) && !isset($previousTotalAttemptsPerModules[0]))
			return array();

		$latestArray   = get_object_vars($latestTotalAttemptsPerModules[0]);
		$previousArray = get_object_vars($previousTotalAttemptsPerModules[0]);

		$percentageArray = array();
		foreach($previousArray as $key => $value)
		{
			if (!isset($value))
			{
				$percentageArray[0][$key] = 0;
				$percentageArray[2][$key] = 0;
				$percentageArray[1][$key] = isset($latestArray[$key]) ? $latestArray[$key] : 0;

				continue;
			}

			$percentageArray[0][$key] = ($value > 0) ? $percentageArray[0][$key] = floor(100 * ($latestArray[$key] / $value)) : 0;
			$percentageArray[1][$key] = $latestArray[$key];
			$percentageArray[2][$key] = $value;
		}

		foreach($percentageArray[0] as $key => $value)
		{
			if($this->moduleController->isFreeModule($key))
				continue;

			unset($percentageArray[0][$key]);
			unset($percentageArray[1][$key]);
			unset($percentageArray[2][$key]);
		}

		return $percentageArray;
	}

	public function retrieveInitialDashboardData()
	{
        if (!isset($_POST['reportsNonce']) || false === wp_verify_nonce($_POST['reportsNonce'], GdbcBaseAdminPlugin::ADMIN_NONCE_VALUE))
            exit;

		$ajaxData = array();
		$ajaxData['ChartDataArray'] = $this->getAttemptsChartArray();

		echo json_encode($ajaxData);
		exit;
	}

	private function getHeaderOfLatestAttemptsArray()
	{
		return self::$latestAttemptsTableHeader;
	}

	private function getLatestAttemptsArray()
	{
		$attemptsList = GdbcAttemptsManager::getTopAttemptsList( self::GDBC_ATTEMPTS_ITEMS_FRONT_PAGE );
		if (!isset($attemptsList[0]))
			return array();

		$contentArray = array();
		for ($i = 0, $numberOfRows = count($attemptsList); $i < $numberOfRows; ++$i)
		{
			$moduleName = $this->moduleController->getModuleNameById($attemptsList[$i]->ModuleId);

			if(null === $moduleName)
				continue;

			$moduleInstance = $this->moduleController->getAdminModuleInstance($moduleName);
			$section = $moduleInstance->getSettingOptionDisplayTextByOptionId($attemptsList[$i]->SectionId);
			if (null === $section)
				$section = 'N/A';

			//$contentArray[$i][self::TABLE_HEADER_MODULE_SECTION] = $moduleName;

			$contentArray[$i][self::TABLE_HEADER_MODULE_SECTION] = empty($attemptsList[$i]->ModuleId) ? 'Custom Form' : $moduleName;

			$contentArray[$i][self::TABLE_HEADER_ID] = $attemptsList[$i]->Id;
			$contentArray[$i][self::TABLE_HEADER_IS_IP_BLOCKED] = $attemptsList[$i]->IsIpBlocked;

			if ($moduleName !== $section) {
				if ($section !== 'N/A')
					$contentArray[$i][self::TABLE_HEADER_MODULE_SECTION] = $moduleName . ' / ' . $section;
//				else
//					$contentArray[$i][self::TABLE_HEADER_MODULE_SECTION] = $moduleName;
			}

			$clientIp = ($attemptsList[$i]->ClientIp !== null) ?  MchHttpUtil::ipAddressFromBinary($attemptsList[$i]->ClientIp) : 'N/A';

			$contentArray[$i][self::TABLE_HEADER_CLIENT_IP] = $clientIp;

			$contentArray[$i][self::TABLE_HEADER_COUNTRY] = $this->getCountry(GdbcCountryDataSource::getCountryCodeById($attemptsList[$i]->CountryId), GdbcCountryDataSource::getCountryNameById($attemptsList[$i]->CountryId));
			$contentArray[$i][self::TABLE_HEADER_DATE] = date('M d, Y h:i:s', strtotime($attemptsList[$i]->CreatedDate));

			$reason = GdbcReasonDataSource::getReasonDescription($attemptsList[$i]->ReasonId);
			$contentArray[$i][self::TABLE_HEADER_REASON] = $reason;
		}

		return $contentArray;
	}

	private function getHeaderOfAttemptsLocationsArray()
	{
		return self::$attemptsLocationsTableHeader;
	}

	private function getAttemptsLocationsArray($topAttemptsLocationsArray)
	{
		$attemptsLocationArray = array();
		for ($i = 0, $arrSize = count($topAttemptsLocationsArray); $i < $arrSize; ++$i)
        {
			//$attemptsLocationArray[$i][self::TABLE_HEADER_NO] = ($i + 1);
			$attemptsLocationArray[$i][self::TABLE_HEADER_COUNTRY]         = $this->getCountry(GdbcCountryDataSource::getCountryCodeById($topAttemptsLocationsArray[$i]->CountryId),
                                                                                               GdbcCountryDataSource::getCountryNameById($topAttemptsLocationsArray[$i]->CountryId));
			$attemptsLocationArray[$i][self::TABLE_HEADER_LOGIN]           = $topAttemptsLocationsArray[$i]->Login;
			$attemptsLocationArray[$i][self::TABLE_HEADER_REGISTRATION]    = $topAttemptsLocationsArray[$i]->Registration;
			$attemptsLocationArray[$i][self::TABLE_HEADER_COMMENTS]        = $topAttemptsLocationsArray[$i]->Comments;
			$attemptsLocationArray[$i][self::TABLE_HEADER_FORGOT_PASSWORD] = $topAttemptsLocationsArray[$i]->LostPassword;
			$attemptsLocationArray[$i][self::TABLE_HEADER_TOTAL]           = $topAttemptsLocationsArray[$i]->Total;
		}

		return $attemptsLocationArray;
	}

	public function retrieveLatestAttemptsTable()
	{
        if (!isset($_POST['reportsNonce']) || false === wp_verify_nonce($_POST['reportsNonce'], GdbcBaseAdminPlugin::ADMIN_NONCE_VALUE))
            exit;

		$ajaxData['LatestAttemptsArrayHeader'] = $this->getHeaderOfLatestAttemptsArray();
		$ajaxData['LatestAttemptsArray'] = $this->getLatestAttemptsArray();

		echo json_encode($ajaxData);
		exit;
	}

	public function getModuleStatsPercentage()
	{
        if (!isset($_POST['reportsNonce']) || false === wp_verify_nonce($_POST['reportsNonce'], GdbcBaseAdminPlugin::ADMIN_NONCE_VALUE))
            exit;

		$modulesTotalAttemptsArray = GdbcAttemptsManager::getModulesTotalAttempts();
        if (null === $modulesTotalAttemptsArray)
            $modulesTotalAttemptsArray = array();

		$total = 0;
		$attemptsPerModuleArray = array();
		foreach($modulesTotalAttemptsArray as $attempt)
		{
			$total += $attempt->Total;
			$attemptsPerModuleArray[$attempt->ModuleId] = $attempt->Total;
		}

		$percentageArray = array();
		foreach($this->registeredModulesArray as $key => $value)
		{
			$moduleId = $this->moduleController->getModuleIdByName($key);
			if (0 === $moduleId|| !GdbcAttemptsManager::moduleHasAttempts($moduleId) && $this->moduleController->getModuleNameById($moduleId) != GdbcModulesController::MODULE_WORDPRESS)
				continue;

			$totalAttemptsPerModule = 0;
			if (isset($attemptsPerModuleArray[$moduleId]))
				$totalAttemptsPerModule = $attemptsPerModuleArray[$moduleId];
			if ($total > 0)
				$percentageArray[] = array($key, floor(($totalAttemptsPerModule/$total)*100));
			else
				$percentageArray[] = array($key, 0);
		}
		$ajaxData = array();
		$ajaxData['PercentageArray'] = $percentageArray;
		echo json_encode($ajaxData);
		exit;
	}

	function getTopIpAttempts()
	{
        if (!isset($_POST['reportsNonce']) || false === wp_verify_nonce($_POST['reportsNonce'], GdbcBaseAdminPlugin::ADMIN_NONCE_VALUE))
            exit;

		$topIpAttemptsArray = GdbcAttemptsManager::getTopIpAttempts(self::GDBC_ATTEMPTS_ITEMS_FRONT_PAGE);
		if (!isset($topIpAttemptsArray[0]))
		{
			$ajaxData = array();
			$ajaxData['TopAttemptsArray'] = 0;

			echo json_encode($ajaxData);
			exit;
		}

		$resultArray = array();
		foreach($topIpAttemptsArray as $attempt)
		{
			$clientIp = ($attempt->ClientIp !== null) ? MchHttpUtil::ipAddressFromBinary($attempt->ClientIp) : 'N/A';
            $country = $this->getCountry(GdbcCountryDataSource::getCountryCodeById($attempt->CountryId), GdbcCountryDataSource::getCountryNameById($attempt->CountryId));
			$attemptInfo = array($clientIp, $country, $attempt->Total, $attempt->IsIpBlocked);

			$resultArray[] = $attemptInfo;
		}
		$ajaxData = array();
		$ajaxData['TopAttemptsArray'] = $resultArray;
		echo json_encode($ajaxData);
		exit;
	}

    function getTotalAttemptsPerModule()
    {
        if (!isset($_POST['reportsNonce']) || false === wp_verify_nonce($_POST['reportsNonce'], GdbcBaseAdminPlugin::ADMIN_NONCE_VALUE))
            exit;

        $arrModulesIds = array();
        foreach($this->registeredModulesArray as $moduleName => $moduleInfo)
        {
            $arrModulesIds[$moduleName] = $this->moduleController->getModuleIdByName($moduleName);
            if(!isset($arrModulesIds[$moduleName]))
                unset($arrModulesIds[$moduleName]);
        }

        $attemptsModuleTotalsObj = GdbcAttemptsManager::getTotalAttemptsPerModule($arrModulesIds, 365, 0);
        $attemptsModuleTotalsArr = (array) $attemptsModuleTotalsObj[0];

        $resultArray = array();
        foreach($this->registeredModulesArray as $key => $value)
        {
            $moduleId = $this->moduleController->getModuleIdByName($key);
            if (0 === $moduleId)
                continue;

            if (!empty($attemptsModuleTotalsArr[$key]))
                $resultArray[$key] =  $attemptsModuleTotalsArr[$key];
        }

        $ajaxData = array();
        $ajaxData['TopAttemptsArrayPerModule'] = $resultArray;
        echo json_encode($ajaxData);
        exit;
    }

	public function manageIp()
	{
        if (!isset($_POST['reportsNonce']))
	        return json_encode(false);

		if (!isset($_POST['clientIp']))
			return json_encode(false);

		if(false === wp_verify_nonce($_POST['reportsNonce'], GdbcBaseAdminPlugin::ADMIN_NONCE_VALUE))
			return json_encode(false);

		echo json_encode(false === GdbcAttemptsManager::manageIp(trim($_POST['clientIp']),  empty($_POST['shouldBlock']) ? 0 : 1) ? false : true);

		exit;
	}

	/// Start Functions for Modules Page
	private function getAttemptsArrayByModuleAndDay($startDate, $endDate)
	{
		if(null === $startDate || null === $endDate || $startDate >= $endDate)
			return array();

		return GdbcAttemptsManager::getAttemptsArrayByModuleAndDay($startDate, $endDate);
	}

	public function getDisplayableAttemptsArray()
	{
        if (!isset($_POST['reportsNonce']) || false === wp_verify_nonce($_POST['reportsNonce'], GdbcBaseAdminPlugin::ADMIN_NONCE_VALUE))
            exit;

        $endDate = strtotime(current_time('mysql'));
		$startDate = $endDate - self::GDBC_ATTEMPTS_NUMBER_OF_DAYS * 24 * 60 * 60;
		$attemptsByModuleAndDay = $this->getAttemptsArrayByModuleAndDay($startDate, $endDate);
		$displayableAttemptsArray = $this->createDisplayableAttemptsArray($attemptsByModuleAndDay, $startDate, $endDate);

		$ajaxData = array();
		$ajaxData['ModulesDescriptionArray'] = $this->getModulesFromAttemptArray($attemptsByModuleAndDay);
		$ajaxData['ModulesAttemptsArray'] = $displayableAttemptsArray;



		echo json_encode($ajaxData);

		exit;
	}

	private function createDisplayableAttemptsArray($attemptsArray, $startDate, $endDate)
	{
		if (null === $attemptsArray)
			return array();

		$displayableArray = array();
		foreach ($attemptsArray as $attemptObj)
		{
			$moduleId = $attemptObj->ModuleId;
			if (!isset($displayableArray[$moduleId][$attemptObj->CreatedDate])) {
				$displayableArray[$moduleId][$attemptObj->CreatedDate] = 0;
			}
			$displayableArray[$moduleId][$attemptObj->CreatedDate] += $attemptObj->AttemptsNumber;
		}
		$numberOfDays = floor(($endDate - $startDate) / (60 * 60 * 24));
		foreach($displayableArray as &$value)
		{
			$newArray = array();
			for ($i = 0 ; $i <= $numberOfDays ; ++$i) {
				$day = date('Y-m-d', $startDate + $i * 24 * 60 * 60);
				$newArray[$day] = 0;
				if (isset($value[$day]))
					$newArray[$day] += $value[$day];
			}
			$value = $newArray;
		}
		$resultArray = array();
		foreach($displayableArray as $arrKey => $arrValue)
		{
			$i = 0;
			foreach($arrValue as $key1 => $value1)
			{
				$resultArray[$arrKey][$i] = array(strtotime($key1) . '000', $value1);
				$i++;
			}
		}

		return $resultArray;
	}

	private function getModulesFromAttemptArray($attemptsArray)
	{
		$resultArray = array();
		foreach($attemptsArray as $attempt)
		{
			if(0 == $attempt->ModuleId)
			{
				$resultArray[$attempt->ModuleId] = 'Custom Form';
				continue;
			}

			$resultArray[$attempt->ModuleId] = $this->moduleController->getModuleNameById($attempt->ModuleId);
		}

		return $resultArray;
	}

	public function getModuleData()
	{
        if (!isset($_POST['reportsNonce']) || false === wp_verify_nonce($_POST['reportsNonce'], GdbcBaseAdminPlugin::ADMIN_NONCE_VALUE))
            exit;

		if (!isset($_POST['moduleId']))
			return array();

		$moduleId   = $_POST['moduleId'];
		$pageNumber = !empty($_POST['pageNumber']) ? $_POST['pageNumber'] : 1;
		$orderBy    = isset($_POST['orderBy']) ? $_POST['orderBy'] : '';

		$recordsNumber = GdbcAttemptsManager::getTotalNumberOfAttemptsPerModule($moduleId);

		$totalPages = ceil($recordsNumber / self::GDBC_MODULES_ITEMS_PER_PAGE);
		$pageNumber > $totalPages ? $pageNumber = $totalPages : null;

		$moduleName     = $this->moduleController->getModuleNameById($moduleId);
		$moduleInstance = $this->moduleController->getAdminModuleInstance($moduleName);

		$moduleSettingSectionsArray = $moduleInstance->getModuleSettingSections();

		$hasSections = isset($moduleSettingSectionsArray[0]);

		$moduleData = GdbcAttemptsManager::getAttemptsPerModule($moduleId, $hasSections, $pageNumber, self::GDBC_MODULES_ITEMS_PER_PAGE, $orderBy);
		if (!isset($moduleData[0]))
		{
			$ajaxData = array();
			$ajaxData['PaginationInfo'] = 0; //pageNumber, totalPages
			$ajaxData['ModuleDataHeader'] = array();

			echo json_encode($ajaxData);
			exit;
		}

		$headerArray = array_keys(get_object_vars($moduleData[0]));
		foreach($headerArray as $headerKey => &$headerValue)
		{
			if (isset(self::$moduleTableHeaderArray[$headerValue]))
				$headerValue = self::$moduleTableHeaderArray[$headerValue];
			else
				unset($headerArray[$headerKey]);
		}

		$headerArray[] = ' '; //Block column

		$moduleDataArray = array_values($moduleData);
		$valuesArray     = array();

		foreach($moduleDataArray as $attempt)
		{
            $itemArray = array();
            $itemArray[] = $attempt->IsIpBlocked;
			if ($hasSections)
                $itemArray[] = isset($attempt->SectionId) ? $moduleInstance->getSettingOptionDisplayTextByOptionId($attempt->SectionId) : 'N/A';

			$itemArray[] = isset($attempt->CreatedDate) ? date('M d, Y h:i:s', strtotime($attempt->CreatedDate)) : 'N/A';

			if (isset($attempt->CountryId))
                $itemArray[] = $this->getCountry(GdbcCountryDataSource::getCountryCodeById($attempt->CountryId), GdbcCountryDataSource::getCountryNameById($attempt->CountryId));

			$itemArray[] = isset($attempt->ClientIp) ? MchHttpUtil::ipAddressFromBinary($attempt->ClientIp) : 'N/A';

			$valuesArray[] = $itemArray;
		}

		$ajaxData = array();
		$ajaxData['PaginationInfo'] = array($pageNumber, $totalPages);
		$ajaxData['ModuleDataHeader'] = $headerArray;
		$ajaxData['ModuleDataRows'] = $valuesArray;

		echo json_encode($ajaxData);

		exit;
	}

	/// End Functions for Modules Page


    /// Utility function
    public function getCountry($countryCode, $countryName)
    {

        if (null === $countryCode || null === $countryName)
            return 'N/A';

        $country = '<img width="16px" height="11px" title="' . $countryName . '" src="' . plugins_url('/admin/images/flags/' . strtolower($countryCode) . '.gif', $this->PLUGIN_MAIN_FILE) . '"/>';
        $country .= '<span>' . $countryName . '</span>';
        return $country;
    }

	public function renderModuleSettingSection(array $arrSectionInfo)
	{
		$this->displayContent();
	}
	
	public function validateModuleSetting($arrSettingOptions)
	{
		return $arrSettingOptions;
	}
	
	public function renderModuleSettingField(array $arrSettingField)
	{}
	
	public function filterOptionsBeforeSave($arrNewSettings, $arrOldSettings)
	{}


	public static function getInstance(array $arrPluginInfo)
	{
		static $arrInstances = array();
		$instanceKey         = implode('', $arrPluginInfo);
		return isset($arrInstances[$instanceKey]) ? $arrInstances[$instanceKey] : $arrInstances[$instanceKey] = new self($arrPluginInfo);
	}

	protected function getDefaultSettingOptions()
	{
		return $this->arrDefaultSettingOptions;
	}
}
