<?php

namespace Brisum\InventorySynchronization\VisualComponent\Storage\DataProvider;

use Brisum\InventorySynchronization\InventorySynchronization;
use Brisum\Lib\VisualComponent\DataProviderInterface;
use FilesystemIterator;
use SplFileInfo;

class FileList implements DataProviderInterface
{
    /**
     * @var InventorySynchronization
     */
    protected $storageDir;

    /**
     * FileList constructor.
     * @param InventorySynchronization $InventorySynchronization
     */
    public function __construct(InventorySynchronization $InventorySynchronization)
    {
        $this->storageDir = $InventorySynchronization->getStorageDir();
    }

    /**
     * @return array
     */
    function getData()
    {
        return $this->getFileList($this->storageDir);
    }

    protected function getFileList($dir)
    {
        $fileIterator = new FilesystemIterator($dir, FilesystemIterator::SKIP_DOTS);
        $files = [];

        foreach ($fileIterator as $file) {
            /** @var SplFileInfo $file */
            if ($file->isDir()) {
                $filename = str_replace($this->storageDir, '', $file->getPathname()) . '/';
                $files[$filename] = $filename;
                foreach ($this->getFileList($file->getPathname()) as $subFile) {
                    $files[$subFile] = $subFile;
                }
            } else {
                $filename = str_replace($this->storageDir, '', $file->getPathname());
                $files[$filename] = $filename;
            }
        }

        ksort($files);
        return $files;
    }
}
