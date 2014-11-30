<?php

class GdbcCheckAttemptsTask extends MchWpTask
{
	public function __construct($runningInterval, $isRecurring)
	{
		parent::__construct($runningInterval, $isRecurring);
	}

	public function run()
	{}
}