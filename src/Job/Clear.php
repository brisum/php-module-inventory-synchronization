<?php

namespace Brisum\InventorySynchronization\Job;

use Brisum\InventorySynchronization\SupplierFactoryInterface;
use Brisum\InventorySynchronization\InventorySynchronization;
use Brisum\InventorySynchronization\JobInterface;

class Clear implements JobInterface
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
     * Clear constructor.
     * @param InventorySynchronization $inventorySynchronization
     * @param SupplierFactoryInterface $supplierFactory
     */
    public function __construct(
        // InventorySynchronization $inventorySynchronization,
        // SupplierFactoryInterface $supplierFactory
    ) {
        // $this->inventorySynchronization = $inventorySynchronization;
        // $this->supplierFactory = $supplierFactory;
    }

    /**
     * Run job
     *
     * @param string $supplierName
     * @return void
     */
    function run($supplierName)
    {
        // TODO: Implement run() method.
    }
}
