<?php

namespace Brisum\InventorySynchronization\Job;

use App\Alert;
use Brisum\Lib\Translit\Translit;
use Eventy;
use Illuminate\Console\Command;
use Log;
use Brisum\InventorySynchronization\Supplier\SupplierFactory;
use Brisum\InventorySynchronization\Supplier\SupplierInterface;
use Brisum\InventorySynchronization\PriceCalculate;

class CreateUpdatingInventory extends Command
{
	protected $signature = 'create-updating-inventory {supplierSlug}';

	protected $description = 'Создание данных обновления';

	public function handle(SupplierFactory $supplierFactory)
	{
		$supplierSlug = $this->argument('supplierSlug');
		$localDirMatchedIn = base_path(sprintf(SupplierInterface::FORMAT_DIR_MATCHED_IN, $supplierSlug));
		$localDirMatchedDone = base_path(sprintf(SupplierInterface::FORMAT_DIR_MATCHED_DONE, $supplierSlug));
		$localDirUpdatingIn = base_path(sprintf(SupplierInterface::FORMAT_DIR_UPDATING_IN, $supplierSlug));
		$supplier = $supplierFactory->create($supplierSlug);

		if (!is_dir($localDirMatchedDone)) {
			mkdir($localDirMatchedDone, 0755, true);
		}
		if (!is_dir($localDirUpdatingIn)) {
			mkdir($localDirUpdatingIn, 0755, true);
		}

		Log::info("Создание данных обновления. Дилер: {$supplierSlug}.");
		$this->info("Создание данных обновления. Дилер: {$supplierSlug}.");

		$files = scandir($localDirMatchedIn);
		$bar = $this->output->createProgressBar(count($files));
		foreach ($files as $file) {
			if ('.' === $file || '..' === $file) {
				$bar->advance();
				continue;
			}

			Log::info("Создание данных обновления. Файл: {$localDirMatchedIn}{$file}.");

			$itemsContent = file_get_contents($localDirMatchedIn . $file);
			$items = json_decode($itemsContent, true);
			$updatingItems = [];

			foreach ($items as $item) {
				$itemBrand = $this->normalizeBrandName($supplier->getBrand($item));

				$priceCalculateTemplate = config("price-calculate.supplier.{$supplierSlug}.{$itemBrand}");
				if (!$priceCalculateTemplate) {
					Alert::setAlert(
						'warning',
						'sync',
						sprintf("Не найдено шаблон расчета цен. Продукт: %s.", json_encode($item)),
						6
					);
					Log::error(sprintf("Не найдено шаблон расчета цен. Продукт: %s.", json_encode($item)));
					continue;
				}

				$priceCalculateConfig = config('price-calculate.template.' . $priceCalculateTemplate);
				if (!$priceCalculateConfig) {
					Alert::setAlert(
						'warning',
						'sync',
						sprintf(
							"Не найдено конфигурацию расчета цен для шаблона {$priceCalculateTemplate}. Продукт: %s.",
							json_encode($item)
						),
						6
					);
					Log::info(sprintf(
						"Не найдено конфигурацию расчета цен для шаблона {$priceCalculateTemplate}. Продукт: %s.",
						json_encode($item)
					));
					continue;
				}

				$updatingItem = ['item_id' => $item['item_id']];
				$storePrices = PriceCalculate::calculate($priceCalculateConfig, $item);
				if (null != $storePrices) {
					$updatingItem['item_price'] = $storePrices['store_special_price']
						? $storePrices['store_special_price']
						: $storePrices['store_price'];
					$updatingItem['item_price_old'] = $storePrices['store_special_price']
						? $storePrices['store_price']
						: 0;
					$updatingItem['item_price_opt'] = $storePrices['store_purchase_price'];
				}
				$updatingItem = $supplier->createUpdating($updatingItem, $item);
				$updatingItem = Eventy::filter(
					"sync_product.create_updating_inventory.{$supplierSlug}",
					$updatingItem,
					$item
				);

				$updatingItems[] = $updatingItem;
			}

			if(!file_put_contents($localDirUpdatingIn . $file, json_encode($updatingItems, JSON_PRETTY_PRINT))) {
				Log::error("Не удалось созадать файл {$localDirUpdatingIn}{$file}.");
				throw new \Exception("Can't save file {$localDirUpdatingIn}{$file}.");
			}

			if (!rename($localDirMatchedIn . $file, $localDirMatchedDone . $file)) {
				Log::error(sprintf("Не удалось переместить файл %s в папку %s.", $file, $localDirMatchedDone));
				throw new \Exception(sprintf("Can't move file %s to %s directory.", $file, $localDirMatchedDone));
			}

			Log::info("Создание данных обновления с файла {$localDirMatchedIn}{$file} успешно завершено.");
			$bar->advance();
		}

		$bar->finish();
		Log::info("Создание данных для обновления успешно завершено. Дилер: {$supplierSlug}.");
		$this->info("Создание данных для обновления успешно завершено. Дилер: {$supplierSlug}.");
	}

	/**
	 * Normalize brand name
	 *
	 * @param string $brand
	 * @return string
	 */
	protected function normalizeBrandName($brand)
	{
		$brand = Translit::exec(mb_strtolower($brand));
		return str_replace('__', '_', preg_replace('/[^a-z0-9]/i', '_', $brand));
	}
}
