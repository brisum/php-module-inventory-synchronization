<?php

namespace Brisum\InventorySynchronization\Job;

use App\Alert;
use Eventy;
use Illuminate\Console\Command;
use Barbarys\Store\Model\Item;
use Log;
use Brisum\InventorySynchronization\Supplier\SupplierInterface;

class UpdateInventory extends Command
{
	const PID_FILE = '/tmp/sync-product/updating-inventory.pid';

	protected $signature = 'update-inventory {supplierSlug}';

	protected $description = 'Обновление данных инвентаризации товаров';

	public function handle()
	{
		$supplierSlug = $this->argument('supplierSlug');
		$localDirUpdatingIn = base_path(sprintf(SupplierInterface::FORMAT_DIR_UPDATING_IN, $supplierSlug));
		$localDirUpdatingDone = base_path(sprintf(SupplierInterface::FORMAT_DIR_UPDATING_DONE, $supplierSlug));

		if (file_exists(self::PID_FILE)) {
			$this->info('Команда уже запущена, попробуйте позже.');
			return;
		}
		if (!file_exists(dirname(self::PID_FILE))) {
			mkdir(dirname(self::PID_FILE), 0755, true);
		}
		$this->info('Создание файла процесса.');
		touch(self::PID_FILE);

		if (!is_dir($localDirUpdatingDone)) {
			mkdir($localDirUpdatingDone, 0755, true);
		}

		try {
			Log::info("Обновление данных инвентаризации товаров. Дилер: {$supplierSlug}.");
			$this->info("Обновление данных инвентаризации товаров. Дилер: {$supplierSlug}.");

			$files = scandir($localDirUpdatingIn);
			$bar = $this->output->createProgressBar(count($files));
			foreach ($files as $file) {
				if ('.' === $file || '..' === $file) {
					$bar->advance();
					continue;
				}

				$itemsContent = file_get_contents($localDirUpdatingIn . $file);
				$items = json_decode($itemsContent, true);
				foreach ($items as $item) {
					if (!is_array($item)) {
						Alert::setAlert(
							'warning',
							'sync',
							sprintf(
								'Неправильные данные для обновления: %s. Файл: %s.',
								serialize($item),
								$localDirUpdatingIn . $file
							),
							6
						);
						Log::error(sprintf(
							'Неправильные данные для обновления: %s. Файл: %s.',
							serialize($item),
							$localDirUpdatingIn . $file
						));
						continue;
					}

					/** @var Item $productItem */
					$productItem = Item::find($item['item_id']);
					foreach ($item as $fieldName => $fieldValue) {
						$productItem->$fieldName = $fieldValue;
					}
					$productItem->save();
				}
				Eventy::action("sync_product.update_inventory.after.{$supplierSlug}", $localDirUpdatingIn . $file);

				if (!rename($localDirUpdatingIn . $file, $localDirUpdatingDone . $file)) {
					Log::error(sprintf("Не удалось переместить файл %s в папку %s.", $file, $localDirUpdatingDone));
					throw new \Exception(sprintf("Can't move file %s to %s directory.", $file, $localDirUpdatingDone));
				}

				// Временное решение для Вадима. Вадим просит не выводить ему в журнале мелкие файлики.
				$itemsCount = count($items);
				if (10000 <= $itemsCount) {
					Alert::setAlert(
						'info',
						'sync',
						"Синхронизация с поставщиком Арго произведена. Обновлено товаров - {$itemsCount}",
						6
					);
				}
				Log::info("Файл обновления {$localDirUpdatingIn}{$file} успешно обработан.");
				$bar->advance();
			}

			$bar->finish();
			Log::info("Обновление данных инвентаризации товаров успешно завершено. Дилер: {$supplierSlug}.");
			$this->info("Обновление данных инвентаризации товаров успешно завершено. Дилер: {$supplierSlug}.");
		} catch (\Exception $e) {
			Log::error('Exception: ' . $e->getMessage());
			$this->info('Exception: ' . $e->getMessage());
		} finally {
			$this->info('Удаление файла процесса.');
			unlink(self::PID_FILE);
		}
	}
}
