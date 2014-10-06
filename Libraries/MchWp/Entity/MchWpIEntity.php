<?php

interface MchWpIEntity
{

	/**
	 * @return string The table name
	 */
	public function getTableName();

	/**
	 * @return string The primary key name
	 */
	public function getPrimaryKey();

}