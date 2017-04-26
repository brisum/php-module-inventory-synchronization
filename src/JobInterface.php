<?php

namespace Brisum\InventorySynchronization;

interface JobInterface
{
	/**
	 * Run job
	 *
	 * @param string $supplierName
	 * @return void
	 */
	function run($supplierName);
}