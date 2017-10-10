<?php

namespace Brisum\InventorySynchronization;

interface JobInterface
{
	/**
	 * Run job
	 *
	 * @param string $dealerName
	 * @return void
	 */
	function run($dealerName);
}