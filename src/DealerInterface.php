<?php

namespace Brisum\InventorySynchronization;

interface DealerInterface {
	/**
	 * Fetch inventory files
	 *
	 * @param string $destDir
	 * @return void
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
	 * Create updating data
     *
     * @param $srcFile
     * @param $destFile
     * @return void
     */
	function createUpdating($srcFile, $destFile);

    /**
     * Update inventory
     *
     * @param string $srcFile
     * @return void
     */
	function update($srcFile);

	/**
	 * Create new item
	 *
	 * @param string $srcFile
	 * @param string $destFileManual
	 * @return mixed
	 */
	function createItem($srcFile, $destFileManual);
}
