<?php

namespace Brisum\InventorySynchronization;

interface SupplierFactoryInterface
{
	/**
	 * Create supplier by name
	 *
	 * @param string $supplierName
	 * @return SupplierInterface
	 */
	public function create($supplierName);
}
