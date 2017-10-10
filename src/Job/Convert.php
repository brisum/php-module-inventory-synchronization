<?php

namespace Brisum\InventorySynchronization\Job;

use Brisum\InventorySynchronization\DealerFactoryInterface;
use Brisum\InventorySynchronization\InventorySynchronization;
use Brisum\InventorySynchronization\JobInterface;
use Exception;
use FilesystemIterator;
use SplFileInfo;

class Convert implements JobInterface
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
	 * Convert constructor.
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
		$sourceDoneDir = $this->inventorySynchronization->getSourceDoneDir($dealerName);
		$convertedInDir = $this->inventorySynchronization->getConvertInDir($dealerName);

		if (!is_dir($sourceDoneDir)) {
			mkdir($sourceDoneDir, 0755, true);
		}
		if (!is_dir($convertedInDir)) {
			mkdir($convertedInDir, 0755, true);
		}

		$files = new FilesystemIterator($sourceInDir, FilesystemIterator::SKIP_DOTS);
		foreach ($files as $file) {
			/** @var SplFileInfo $file */
			$sourceInFile = $file->getPathname();
			$sourceDoneFile = $sourceDoneDir . $file->getFilename();
			$convertedInFile = $file->isDir()
                ? $convertedInDir . $file->getFilename()
                : $convertedInDir . str_replace($file->getExtension(), '', $file->getFilename()) . 'json';

			$dealer->convert($sourceInFile, $convertedInFile);
			if (!rename($sourceInFile, $sourceDoneFile)) {
				throw new Exception(sprintf("Can't move file %s to %s directory.", $file->getFilename(), $sourceDoneDir));
			}
		}
	}
}
