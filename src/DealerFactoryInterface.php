<?php

namespace Brisum\InventorySynchronization;

interface DealerFactoryInterface
{
	/**
	 * Create dealer by name
	 *
	 * @param string $dealerName
	 * @return DealerInterface
	 */
	public function create($dealerName);
}
