<?php

namespace Brisum\InventorySynchronization\Job;

use Brisum\InventorySynchronization\InventorySynchronization;
use Brisum\InventorySynchronization\JobInterface;
use Brisum\InventorySynchronization\SupplierFactoryInterface;
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
     * @var SupplierFactoryInterface
     */
    protected $supplierFactory;

    /**
     * CreateUpdating constructor.
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
		$matchedInDir = $this->inventorySynchronization->getMatchInDir($supplierName);
		$matchedDoneDir = $this->inventorySynchronization->getMatchDoneDir($supplierName);
		$updatingInDir = $this->inventorySynchronization->getUpdatingInDir($supplierName);

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

            $supplier->createUpdating($matchedInFile, $updatingInFile);
            if (!rename($matchedInFile, $matchedDoneFile)) {
                throw new \Exception(sprintf("Can't move file %s to %s directory.", $matchedInFile, $matchedDoneFile));
            }
        }
	}
}
