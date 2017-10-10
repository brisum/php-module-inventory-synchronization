<?php

namespace Brisum\InventorySynchronization\Job;

use Brisum\InventorySynchronization\DealerFactoryInterface;
use Brisum\InventorySynchronization\InventorySynchronization;
use Brisum\InventorySynchronization\JobInterface;
use Exception;
use FilesystemIterator;
use SplFileInfo;

class Match implements JobInterface
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
	 * Match constructor.
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
		$convertedInDir = $this->inventorySynchronization->getConvertInDir($dealerName);
		$convertedDoneDir = $this->inventorySynchronization->getConvertDoneDir($dealerName);
		$matchedInDir = $this->inventorySynchronization->getMatchInDir($dealerName);
		$newInDir = $this->inventorySynchronization->getNewInDir($dealerName);

		if (!is_dir($convertedDoneDir)) {
			mkdir($convertedDoneDir, 0755, true);
		}
		if (!is_dir($matchedInDir)) {
			mkdir($matchedInDir, 0755, true);
		}
		if (!is_dir($newInDir)) {
			mkdir($newInDir, 0755, true);
		}

		$files = new FilesystemIterator($convertedInDir, FilesystemIterator::SKIP_DOTS);
		foreach ($files as $file) {
			/** @var SplFileInfo $file */
			$convertedInFile = $file->getPathname();
			$convertedDoneFile = $convertedDoneDir . $file->getFilename();
			$matchedInFile = $matchedInDir . $file->getFilename();
			$newInFile = $newInDir . $file->getFilename();

			$dealer->match($convertedInFile, $matchedInFile, $newInFile);
			if (!rename($convertedInFile, $convertedDoneFile)) {
				throw new Exception(sprintf("Can't move file %s to %s directory.", $convertedInFile, $convertedDoneDir));
			}
		}
	}
}
