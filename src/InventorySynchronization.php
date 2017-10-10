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
     * @return string
     */
    public function getStorageDir()
    {
        return $this->storageDir;
    }

    /**
     * @return string
     */
    public function getTmpDir()
    {
        return $this->tmpDir;
    }

	/**
	 * @param string $dealerName
	 * @return string
	 */
	public function getSourceInDir($dealerName)
	{
		return $this->storageDir . sprintf(self::FORMAT_DIR_SOURCE_IN, $dealerName);

	}

	/**
	 * @param string $dealerName
	 * @return string
	 */
	public function getSourceDoneDir($dealerName)
	{
		return $this->storageDir . sprintf(self::FORMAT_DIR_SOURCE_DONE, $dealerName);

	}

	/**
	 * @param string $dealerName
	 * @return string
	 */
	public function getConvertInDir($dealerName)
	{
		return $this->storageDir . sprintf(self::FORMAT_DIR_CONVERTED_IN, $dealerName);

	}

	/**
	 * @param string $dealerName
	 * @return string
	 */
	public function getConvertDoneDir($dealerName)
	{
		return $this->storageDir . sprintf(self::FORMAT_DIR_CONVERTED_DONE, $dealerName);

	}

	/**
	 * @param string $dealerName
	 * @return string
	 */
	public function getMatchInDir($dealerName)
	{
		return $this->storageDir . sprintf(self::FORMAT_DIR_MATCHED_IN, $dealerName);

	}

	/**
	 * @param string $dealerName
	 * @return string
	 */
	public function getMatchDoneDir($dealerName)
	{
		return $this->storageDir . sprintf(self::FORMAT_DIR_MATCHED_DONE, $dealerName);

	}

	/**
	 * @param string $dealerName
	 * @return string
	 */
	public function getUpdatingInDir($dealerName)
	{
		return $this->storageDir . sprintf(self::FORMAT_DIR_UPDATING_IN, $dealerName);

	}

	/**
	 * @param string $dealerName
	 * @return string
	 */
	public function getUpdatingDoneDir($dealerName)
	{
		return $this->storageDir . sprintf(self::FORMAT_DIR_UPDATING_DONE, $dealerName);

	}

	/**
	 * @param string $dealerName
	 * @return string
	 */
	public function getNewInDir($dealerName)
	{
		return $this->storageDir . sprintf(self::FORMAT_DIR_NEW_IN, $dealerName);

	}

	/**
	 * @param string $dealerName
	 * @return string
	 */
	public function getNewDoneDir($dealerName)
	{
		return $this->storageDir . sprintf(self::FORMAT_DIR_NEW_DONE, $dealerName);

	}

	/**
	 * @param string $dealerName
	 * @return string
	 */
	public function getNewNotProcessedDir($dealerName)
	{
		return $this->storageDir . sprintf(self::FORMAT_DIR_NEW_NOT_PROCESSED, $dealerName);

	}
}
