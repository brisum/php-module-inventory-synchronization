<?php

namespace Brisum\InventorySynchronization\Job;

use Illuminate\Console\Command;
use Log;
use Brisum\InventorySynchronization\Supplier\SupplierFactory;
use Brisum\InventorySynchronization\Supplier\SupplierInterface;

class FetchInventory extends Command
{
	protected $signature = 'fetch-inventory {supplierSlug}';

	protected $description = 'Загрузка файлов инвентаризации продуктов';
	
	public function handle(SupplierFactory $supplierFactory)
	{
		$supplierSlug = $this->argument('supplierSlug');
		$localDirSourceIn = base_path(sprintf(SupplierInterface::FORMAT_DIR_SOURCE_IN, $supplierSlug));
		$supplier = $supplierFactory->create($supplierSlug);

		if (!is_dir($localDirSourceIn)) {
			mkdir($localDirSourceIn, 0755, true);
		}

		Log::info("Загрузка файлов инвентаризации продуктов. Дилер: {$supplierSlug}.");
		$this->info("Загрузка файлов инвентаризации продуктов. Дилер: {$supplierSlug}.");

		$fetchedFiles = $supplier->fetch($localDirSourceIn);

		Log::info(sprintf("Загружено %d файл(ов):\n %s.", count($fetchedFiles), print_r($fetchedFiles, true)));
		$this->info(sprintf("Загружено %d файл(ов):\n %s.", count($fetchedFiles), print_r($fetchedFiles, true)));

		Log::info('Загрузка файлов успешно завершена.');
		$this->info('Загрузка файлов успешно завершена.');
	}
}
