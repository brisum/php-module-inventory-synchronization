<?php

namespace Brisum\InventorySynchronization\Job;

use Brisum\InventorySynchronization\InventorySynchronization;
use Brisum\InventorySynchronization\JobInterface;
use Brisum\InventorySynchronization\DealerFactoryInterface;
use Exception;
use FilesystemIterator;
use SplFileInfo;

class Update implements JobInterface
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
     * Update constructor.
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
        $updatingInDir = $this->inventorySynchronization->getUpdatingInDir($dealerName);
        $updatingDoneDir = $this->inventorySynchronization->getUpdatingDoneDir($dealerName);

        if (!is_dir($updatingDoneDir)) {
            mkdir($updatingDoneDir, 0755, true);
        }

        $files = new FilesystemIterator($updatingInDir, FilesystemIterator::SKIP_DOTS);
        foreach ($files as $file) {
            /** @var SplFileInfo $file */
            $updatingInFile = $file->getPathname();
            $updatingDoneFile = $updatingDoneDir . $file->getFilename();

            $dealer->update($updatingInFile);
            if (!rename($updatingInFile, $updatingDoneFile)) {
                throw new \Exception(sprintf("Can not move file %s to %s directory.", $updatingInFile, $updatingDoneFile));
            }
        }
	}
}
