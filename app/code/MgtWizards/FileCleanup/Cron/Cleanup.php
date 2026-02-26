<?php
namespace MgtWizards\FileCleanup\Cron;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

class Cleanup
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
        $this->rotateLogFiles();
        $this->cleanOldFiles();
    }

    protected function rotateLogFiles()
    {
        $logDir = $this->directoryList->getPath(DirectoryList::VAR_DIR) . '/log';
        $date = date('Ymd');

        if (!is_dir($logDir))
        {
            return;
        }

        $files = glob($logDir . '/*.{log,txt}', GLOB_BRACE);
        foreach ($files as $file)
        {
            $fileInfo = pathinfo($file);
            // Skip files that already have a timestamp (e.g., filename_YYYYMMDD.extension)
            if (preg_match('/_\d{8}\.' . preg_quote($fileInfo['extension'], '/') . '$/', $fileInfo['basename']))
            {
                continue;
            }
            $newFile = $logDir . '/' . $fileInfo['filename'] . '_' . $date . '.' . $fileInfo['extension'];
            rename($file, $newFile);
        }
    }

    protected function cleanOldFiles()
    {
        $directories = [
            '/report',
            '/session',
            '/log'
        ];

        $sevenDaysAgo = time() - (7 * 24 * 60 * 60);

        foreach ($directories as $dir)
        {
            $fullPath = $this->directoryList->getPath('var') . '/' . trim($dir, '/');
            if (!is_dir($fullPath))
            {
                continue;
            }

            $files = glob($fullPath . '/*');
            foreach ($files as $file)
            {
                if (is_file($file) && filemtime($file) < $sevenDaysAgo)
                {
                    unlink($file);
                }
            }
        }
    }
}