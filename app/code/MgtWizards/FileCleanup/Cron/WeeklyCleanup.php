<?php
namespace MgtWizards\FileCleanup\Cron;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

class WeeklyCleanup
{
    protected $filesystem;
    protected $directoryList;

    public function __construct(
        Filesystem $filesystem,
        DirectoryList $directoryList
    ) {
        $this->filesystem = $filesystem;
        $this->directoryList = $directoryList;
    }

    public function execute()
    {
        $this->cleanCacheFiles();
    }

    protected function cleanCacheFiles()
    {
        $directories = [
            '/cache',
            '/page_cache'
        ];

        foreach ($directories as $dir)
        {
            $fullPath = $this->directoryList->getPath('var') . '/' . trim($dir, '/');
            if (!is_dir($fullPath))
            {
                continue;
            }

            $this->deleteFilesRecursively($fullPath);
        }
    }

    protected function deleteFilesRecursively($path)
    {
        $items = glob($path . '/*');
        foreach ($items as $item)
        {
            if (is_file($item))
            {
                unlink($item);
            }
            elseif (is_dir($item))
            {
                $this->deleteFilesRecursively($item);
            }
        }
    }
}