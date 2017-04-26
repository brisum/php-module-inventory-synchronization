<?php

namespace Brisum\InventorySynchronization\Job;

use Brisum\InventorySynchronization\SupplierFactoryInterface;
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
		$newInDir = $this->syncInventory->getNewInDir($supplierName);
		$newDoneDir = $this->syncInventory->getNewDoneDir($supplierName);
		$newNotProcessedDir = $this->syncInventory->getNewNotProcessedDir($supplierName);

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

			$supplier->createItem($newInFile, $newNotProcessedFile);
			if (!rename($newInFile, $newDoneFile)) {
				throw new \Exception(sprintf("Can't move file %s to %s directory.", $file->getFilename(), $newDoneDir));
			}
		}
	}
}
