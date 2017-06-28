<?php

namespace Brisum\InventorySynchronization\Job;

use Brisum\InventorySynchronization\SupplierFactoryInterface;
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
	 * @var SupplierFactoryInterface
	 */
	protected $supplierFactory;

	/**
	 * Convert constructor.
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
		$sourceInDir = $this->inventorySynchronization->getSourceInDir($supplierName);
		$sourceDoneDir = $this->inventorySynchronization->getSourceDoneDir($supplierName);
		$convertedInDir = $this->inventorySynchronization->getConvertInDir($supplierName);

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
			$convertedInFile = $convertedInDir . str_replace($file->getExtension(), '', $file->getFilename()) . 'json';

			$supplier->convert($sourceInFile, $convertedInFile);
			if (!rename($sourceInFile, $sourceDoneFile)) {
				throw new Exception(sprintf("Can't move file %s to %s directory.", $file->getFilename(), $sourceDoneDir));
			}
		}
	}
}
