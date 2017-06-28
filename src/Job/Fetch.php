<?php

namespace Brisum\InventorySynchronization\Job;

use Brisum\InventorySynchronization\InventorySynchronization;
use Brisum\InventorySynchronization\JobInterface;
use Brisum\InventorySynchronization\SupplierFactoryInterface;
use Exception;

class Fetch implements JobInterface
{
    /**
     * @var InventorySynchronization
     */
    protected $inventorySynchronization;

    /**
     * @var SupplierFactoryInterface
     */
    protected $supplierFactory;

    /**
     * Fetch constructor.
     * @param InventorySynchronization $inventorySynchronization
     * @param SupplierFactoryInterface $supplierFactory
     */
    public function __construct(
        InventorySynchronization $inventorySynchronization,
        SupplierFactoryInterface $supplierFactory
    ) {
        $this->inventorySynchronization = $inventorySynchronization;
        $this->supplierFactory = $supplierFactory;
    }

    /**
     * @param string $supplierName
     * @throws Exception
     */
    public function run($supplierName)
    {
        $supplier = $this->supplierFactory->create($supplierName);
        $sourceInDir = $this->inventorySynchronization->getSourceInDir($supplierName);

		if (!is_dir($sourceInDir)) {
			mkdir($sourceInDir, 0755, true);
		}

		$supplier->fetch($sourceInDir);
	}
}
