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

class GdbcAttemptEntity extends MchWpEntity
{
	public $Id          = null;
	public $CreatedDate = null;
	public $ModuleId    = null;
	public $SectionId   = null;
	public $ClientIp    = null;
	public $CountryId   = null;
//	public $AgentId     = null;
	public $ReasonId    = null;
	public $IsDeleted   = null;
	public $IsIpBlocked = null;

	public function __construct($primaryKeyValue = null)
	{
		parent::__construct($primaryKeyValue);
	}

	public function getTableName()
	{
		global $wpdb;
		return $wpdb->prefix . 'gdbc_attempts';
	}

	public function getPrimaryKey()
	{
		return 'Id';
	}

}
