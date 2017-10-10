<?php

namespace Brisum\InventorySynchronization\Job;

use Brisum\InventorySynchronization\DealerFactoryInterface;
use Brisum\InventorySynchronization\InventorySynchronization;
use Brisum\InventorySynchronization\JobInterface;

class Clear implements JobInterface
{
    /**
     * @var InventorySynchronization
     */
    protected $inventorySynchronization;

    /**
     * @var DealerFactoryInterface
     */
    protected $dealerFactory;

    /**
     * Clear constructor.
     * @param InventorySynchronization $inventorySynchronization
     * @param DealerFactoryInterface $dealerFactory
     */
    public function __construct(
         InventorySynchronization $inventorySynchronization,
         DealerFactoryInterface $dealerFactory
    ) {
         $this->inventorySynchronization = $inventorySynchronization;
         $this->dealerFactory = $dealerFactory;
    }

    /**
     * Run job
     *
     * @param string $dealerName
     * @return void
     */
    function run($dealerName)
    {
        // TODO: Implement run() method.
    }
}
