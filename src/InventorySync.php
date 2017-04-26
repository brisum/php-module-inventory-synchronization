<?php

namespace Brisum\InventorySynchronization;

class InventorySynchronization
{
	const FORMAT_DIR_SOURCE_IN = '%s/source/in/';
	const FORMAT_DIR_SOURCE_DONE = '%s/source/done/';
	const FORMAT_DIR_CONVERTED_IN = '%s/converted/in/';
	const FORMAT_DIR_CONVERTED_DONE = '%s/converted/done/';
	const FORMAT_DIR_MATCHED_IN = '%s/matched/in/';
	const FORMAT_DIR_MATCHED_DONE = '%s/matched/done/';
	const FORMAT_DIR_UPDATING_IN = '%s/updating/in/';
	const FORMAT_DIR_UPDATING_DONE = '%s/updating/done/';
	const FORMAT_DIR_NEW_IN = '%s/new/in/';
	const FORMAT_DIR_NEW_DONE = '%s/new/done/';
	const FORMAT_DIR_NEW_NOT_PROCESSED = '%s/new/not-processed/';

	/**
	 * @var string
	 */
	protected $storageDir;

	/**
	 * @var string
	 */
	protected $tmpDir;

	/**
	 * InventorySynchronization constructor.
	 * @param string $storageDir
	 * @param string $tmpDir
	 */
	public function __construct($storageDir, $tmpDir)
	{
		$this->storageDir = rtrim($storageDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
		$this->tmpDir = rtrim($tmpDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
	}

	/**
	 * @param string $supplierName
	 * @return string
	 */
	public function getSourceInDir($supplierName)
	{
		return $this->storageDir . sprintf(self::FORMAT_DIR_SOURCE_IN, $supplierName);

	}

	/**
	 * @param string $supplierName
	 * @return string
	 */
	public function getSourceDoneDir($supplierName)
	{
		return $this->storageDir . sprintf(self::FORMAT_DIR_SOURCE_DONE, $supplierName);

	}

	/**
	 * @param string $supplierName
	 * @return string
	 */
	public function getConvertInDir($supplierName)
	{
		return $this->storageDir . sprintf(self::FORMAT_DIR_CONVERTED_IN, $supplierName);

	}

	/**
	 * @param string $supplierName
	 * @return string
	 */
	public function getConvertDoneDir($supplierName)
	{
		return $this->storageDir . sprintf(self::FORMAT_DIR_CONVERTED_DONE, $supplierName);

	}

	/**
	 * @param string $supplierName
	 * @return string
	 */
	public function getMatchInDir($supplierName)
	{
		return $this->storageDir . sprintf(self::FORMAT_DIR_MATCHED_IN, $supplierName);

	}

	/**
	 * @param string $supplierName
	 * @return string
	 */
	public function getMatchDoneDir($supplierName)
	{
		return $this->storageDir . sprintf(self::FORMAT_DIR_MATCHED_DONE, $supplierName);

	}

	/**
	 * @param string $supplierName
	 * @return string
	 */
	public function getUpdatingInDir($supplierName)
	{
		return $this->storageDir . sprintf(self::FORMAT_DIR_UPDATING_IN, $supplierName);

	}

	/**
	 * @param string $supplierName
	 * @return string
	 */
	public function getUpdatingDoneDir($supplierName)
	{
		return $this->storageDir . sprintf(self::FORMAT_DIR_UPDATING_DONE, $supplierName);

	}

	/**
	 * @param string $supplierName
	 * @return string
	 */
	public function getNewInDir($supplierName)
	{
		return $this->storageDir . sprintf(self::FORMAT_DIR_NEW_IN, $supplierName);

	}

	/**
	 * @param string $supplierName
	 * @return string
	 */
	public function getNewDoneDir($supplierName)
	{
		return $this->storageDir . sprintf(self::FORMAT_DIR_NEW_DONE, $supplierName);

	}

	/**
	 * @param string $supplierName
	 * @return string
	 */
	public function getNewNotProcessedDir($supplierName)
	{
		return $this->storageDir . sprintf(self::FORMAT_DIR_NEW_NOT_PROCESSED, $supplierName);
	}

	/**
	 * @return string
	 */
	public function getTmpDir()
	{
		return $this->tmpDir;
	}
}
