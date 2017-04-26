<?php

namespace Brisum\InventorySynchronization;

interface SupplierInterface {
	/**
	 * Fetch inventory files
	 *
	 * @param string $destDir
	 * @return array
	 */
	function fetch($destDir);

	/**
	 * Convert inventory file to json
	 *
	 * @param string $srcFile
	 * @param string $destFile
	 * @return void
	 */
	function convert($srcFile, $destFile);

	/**
	 * Match inventory data with product items
	 *
	 * @param string $srcFile
	 * @param string $destFileMatched
	 * @param string $destFileNew
	 * @return void
	 */
	function match($srcFile, $destFileMatched, $destFileNew);

	/**
	 * Create updating data of item
	 *
	 * @param array $item
	 * @param array $itemInformation
	 * @return array
	 */
	function createUpdating(array $item, array $itemInformation);

	/**
	 * Create new item
	 *
	 * @param string $srcFile
	 * @param string $destFileManual
	 * @return mixed
	 */
	function createItem($srcFile, $destFileManual);

	/**
	 * Get brand of item
	 *
	 * @param array $itemInformation
	 * @return string
	 */
	function getBrand(array $itemInformation);
}
