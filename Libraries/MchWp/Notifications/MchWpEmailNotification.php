<?php
/** 
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

abstract class MchWpEmailNotification extends MchWpBaseNotification
{
	private $layoutTemplateFilePath = null;
	private $isHtmlFormattedEmail   = false;

	public $AddressToSend   = null;
	public $EmailSubject    = null;
	public $EmailBodyContent = null;

//	public abstract function getEmailBodyContent();
//	public abstract function setEmailBodyContent();

	public function __construct($isHtmlFormattedEmail = true)
	{
		parent::__construct();

		$this->isHtmlFormattedEmail = (bool)$isHtmlFormattedEmail;
	}

	protected function setLayoutTemplateFilePath($layoutTemplateFilePath)
	{
		if(!isset($layoutTemplateFilePath[0]) || !is_readable($layoutTemplateFilePath))
			throw new Exception ('Layout template file path is not readable!');

		$this->layoutTemplateFilePath = $layoutTemplateFilePath;
	}

	public function send()
	{
		$emailHeaders = array();
		$this->isHtmlFormattedEmail ? $emailHeaders[] = 'Content-Type: text/html; charset=UTF-8' : null;

		if(empty($this->AddressToSend) ||  (false === filter_var($this->AddressToSend, FILTER_VALIDATE_EMAIL)))
			return;

		empty($this->EmailSubject) ? $this->EmailSubject = 'MchWp - Email Notification' : null;

		$emailContent = file_get_contents($this->layoutTemplateFilePath);
		if(false !== $emailContent) {
			$emailContent = str_replace('{email-body-content}', trim($this->EmailBodyContent), $emailContent);
		}
		else{
			$emailContent = trim($this->EmailBodyContent);
		}

		@wp_mail($this->AddressToSend, $this->EmailSubject, $emailContent, $emailHeaders);

	}
}