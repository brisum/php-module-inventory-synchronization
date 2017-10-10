<?php

namespace Brisum\InventorySynchronization\Job;

use Brisum\InventorySynchronization\InventorySynchronization;
use Brisum\InventorySynchronization\JobInterface;
use Brisum\InventorySynchronization\DealerFactoryInterface;
use Exception;

class Fetch implements JobInterface
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
     * Fetch constructor.
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
     * @param string $dealerName
     * @throws Exception
     */
    public function run($dealerName)
    {
        $dealer = $this->dealerFactory->create($dealerName);
        $sourceInDir = $this->inventorySynchronization->getSourceInDir($dealerName);

		if (!is_dir($sourceInDir)) {
			mkdir($sourceInDir, 0755, true);
		}

		$dealer->fetch($sourceInDir);
	}
}
