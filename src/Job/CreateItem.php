<?php

namespace Brisum\InventorySynchronization\Job;

use Brisum\InventorySynchronization\DealerFactoryInterface;
use Brisum\InventorySynchronization\InventorySynchronization;
use Brisum\InventorySynchronization\JobInterface;
use Exception;
use FilesystemIterator;
use SplFileInfo;

class CreateItem implements JobInterface
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
	 * CreateItem constructor.
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
		$newInDir = $this->inventorySynchronization->getNewInDir($dealerName);
		$newDoneDir = $this->inventorySynchronization->getNewDoneDir($dealerName);
		$newNotProcessedDir = $this->inventorySynchronization->getNewNotProcessedDir($dealerName);

		if (!is_dir($newDoneDir)) {
			mkdir($newDoneDir, 0755, true);
		}
		if (!is_dir($newNotProcessedDir)) {
			mkdir($newNotProcessedDir, 0755, true);
		}

		$files = new FilesystemIterator($newInDir, FilesystemIterator::SKIP_DOTS);
		foreach ($files as $file) {
			/** @var SplFileInfo $file */
			$newInFile = $file->getPathname();
			$newDoneFile = $newDoneDir . $file->getFilename();
			$newNotProcessedFile = $newNotProcessedDir . $file->getFilename();

			$dealer->createItem($newInFile, $newNotProcessedFile);
			if (!rename($newInFile, $newDoneFile)) {
				throw new Exception(sprintf("Can't move file %s to %s directory.", $file->getFilename(), $newDoneDir));
			}
		}
	}
}
