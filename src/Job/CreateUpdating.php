<?php

namespace Brisum\InventorySynchronization\Job;

use Brisum\InventorySynchronization\InventorySynchronization;
use Brisum\InventorySynchronization\JobInterface;
use Brisum\InventorySynchronization\DealerFactoryInterface;
use Exception;
use FilesystemIterator;
use SplFileInfo;

class CreateUpdating implements JobInterface
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
     * CreateUpdating constructor.
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
		$matchedInDir = $this->inventorySynchronization->getMatchInDir($dealerName);
		$matchedDoneDir = $this->inventorySynchronization->getMatchDoneDir($dealerName);
		$updatingInDir = $this->inventorySynchronization->getUpdatingInDir($dealerName);

        if (!is_dir($matchedInDir)) {
            mkdir($matchedInDir, 0755, true);
        }
        if (!is_dir($matchedDoneDir)) {
            mkdir($matchedDoneDir, 0755, true);
        }
        if (!is_dir($updatingInDir)) {
            mkdir($updatingInDir, 0755, true);
        }

        $files = new FilesystemIterator($matchedInDir, FilesystemIterator::SKIP_DOTS);
        foreach ($files as $file) {
            /** @var SplFileInfo $file */
            $matchedInFile = $file->getPathname();
            $matchedDoneFile = $matchedDoneDir . $file->getFilename();
            $updatingInFile = $updatingInDir . $file->getFilename();

            $dealer->createUpdating($matchedInFile, $updatingInFile);
            if (!rename($matchedInFile, $matchedDoneFile)) {
                throw new \Exception(sprintf("Can't move file %s to %s directory.", $matchedInFile, $matchedDoneFile));
            }
        }
	}
}
