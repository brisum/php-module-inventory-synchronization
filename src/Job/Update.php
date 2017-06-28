<?php

namespace Brisum\InventorySynchronization\Job;

use Brisum\InventorySynchronization\InventorySynchronization;
use Brisum\InventorySynchronization\JobInterface;
use Brisum\InventorySynchronization\SupplierFactoryInterface;
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
     * @var SupplierFactoryInterface
     */
    protected $supplierFactory;

    /**
     * Update constructor.
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
        $updatingInDir = $this->inventorySynchronization->getUpdatingInDir($supplierName);
        $updatingDoneDir = $this->inventorySynchronization->getUpdatingDoneDir($supplierName);

        if (!is_dir($updatingDoneDir)) {
            mkdir($updatingDoneDir, 0755, true);
        }

        $files = new FilesystemIterator($updatingInDir, FilesystemIterator::SKIP_DOTS);
        foreach ($files as $file) {
            /** @var SplFileInfo $file */
            $updatingInFile = $file->getPathname();
            $updatingDoneFile = $updatingDoneDir . $file->getFilename();

            $supplier->update($updatingInFile);
            if (!rename($updatingInFile, $updatingDoneFile)) {
                throw new \Exception(sprintf("Can not move file %s to %s directory.", $updatingInFile, $updatingDoneFile));
            }
        }
	}
}
