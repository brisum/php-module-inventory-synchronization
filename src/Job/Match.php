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
	protected $syncInventory;

	/**
	 * @var SupplierFactoryInterface
	 */
	protected $supplierFactory;

	/**
	 * ConvertInventory constructor.
	 * @param InventorySynchronization $syncInventory
	 * @param SupplierFactoryInterface $supplierFactory
	 */
	public function __construct(InventorySynchronization $syncInventory, SupplierFactoryInterface $supplierFactory)
	{
		$this->syncInventory = $syncInventory;
		$this->supplierFactory = $supplierFactory;
	}

	/**
	 * @param string $supplierName
	 * @throws Exception
	 */
	public function run($supplierName)
	{
		$supplier = $this->supplierFactory->create($supplierName);
		$convertedInDir = $this->syncInventory->getConvertInDir($supplierName);
		$convertedDoneDir = $this->syncInventory->getConvertDoneDir($supplierName);
		$matchedInDir = $this->syncInventory->getMatchInDir($supplierName);
		$newInDir = $this->syncInventory->getNewInDir($supplierName);

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
