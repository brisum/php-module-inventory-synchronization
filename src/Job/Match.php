<?php

namespace Brisum\InventorySynchronization\Job;

use Brisum\InventorySynchronization\SupplierFactoryInterface;
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
	 * @var SupplierFactoryInterface
	 */
	protected $supplierFactory;

	/**
	 * Match constructor.
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
		$convertedInDir = $this->inventorySynchronization->getConvertInDir($supplierName);
		$convertedDoneDir = $this->inventorySynchronization->getConvertDoneDir($supplierName);
		$matchedInDir = $this->inventorySynchronization->getMatchInDir($supplierName);
		$newInDir = $this->inventorySynchronization->getNewInDir($supplierName);

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

			$supplier->match($convertedInFile, $matchedInFile, $newInFile);
			if (!rename($convertedInFile, $convertedDoneFile)) {
				throw new Exception(sprintf("Can't move file %s to %s directory.", $convertedInFile, $convertedDoneDir));
			}
		}
	}
}
