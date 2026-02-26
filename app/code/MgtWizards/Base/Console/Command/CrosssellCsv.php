<?php

namespace MgtWizards\Base\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

/**
 * Class CrosssellCsv
 *
 * @package MgtWizards\Base\Console\Command
 */
class CrosssellCsv extends Command
{
    const STORE_ARGUMENT = "0";

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var \Magento\Framework\App\State
     */
    private $state;
    protected $filesystem;
    protected $directoryList;
    protected $csvProcessor;
    protected $driverFile;
    private $logger;
    protected $productCollectionFactory;
    protected $productVisibility;
    protected $productStatus;
    private $storeManager;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\App\State $state
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\State $state,
        \Magento\Framework\File\Csv $csvProcessor,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Filesystem\Driver\File $driverFile,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        \Magento\Store\Model\StoreManagerInterface $storeManager

    ) {
        $this->objectManager = $objectManager;
        $this->state = $state;
        $this->filesystem = $filesystem;
        $this->directoryList = $directoryList;
        $this->csvProcessor = $csvProcessor;
        $this->driverFile = $driverFile;
        $this->logger = $logger;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productStatus = $productStatus;
        $this->productVisibility = $productVisibility;
        $this->storeManager = $storeManager;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('mgtwizards:base:crosssellcsv')
            ->setDescription('Generate random crosssell product data to CSV/Magmi');
        $this->setDefinition([
            new InputArgument(self::STORE_ARGUMENT, InputArgument::OPTIONAL, "Store ID")
        ]);
        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_FRONTEND);

        $storeid = $input->getArgument(self::STORE_ARGUMENT);

        $output->writeln("<info>-> Generating CSV</info>");
        $csvs_task = $this->generateCsv($storeid);

        $output->writeln("   url: " . $csvs_task['front_file']);
        $output->writeln("   file: " . $csvs_task['export_file']);
        $output->writeln("   done!");

        return 1;
    }

    public function getStoreCodeById(int $id): ?string
    {
        try
        {
            $storeData = $this->storeManager->getStore($id);
            $storeCode = (string)$storeData->getCode();
        }
        catch (LocalizedException $localizedException)
        {
            $storeCode = null;
            $this->logger->error($localizedException->getMessage());
        }
        return $storeCode;
    }

    public function getProductCollection($storeid)
    {
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        //$collection->addStoreFilter($storeid);
        //$collection->addAttributeToFilter('status', ['in' => $this->productStatus->getVisibleStatusIds()]);
        //$collection->setVisibility($this->productVisibility->getVisibleInSiteIds());
        return $collection;
    }

    public function getFileContents($fileName)
    {
        $paths = [];
        try
        {
            $path = $this->directoryList->getPath(\Magento\Framework\App\Filesystem\DirectoryList::ROOT) . $fileName;
            $contents = $this->driverFile->fileGetContents($path);
        }
        catch (FileSystemException $e)
        {
            $this->logger->error($e->getMessage());
        }

        return $contents;
    }

    protected function generateCsv($storeid)
    {

        $collection = $this->getProductCollection($storeid);

        $fileDirectoryBasePath = $this->directoryList->getPath(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $fileDirectoryPath = $fileDirectoryBasePath . '/export';

        $fileDirectoryBasePathvar = $this->directoryList->getPath(\Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR);
        $fileDirectoryPathvar = $fileDirectoryBasePathvar . '/import';

        if (!is_dir($fileDirectoryPath))
            mkdir($fileDirectoryPath, 0777, true);
        $fileName = 'wiz_crosssell_export_' . $storeid . '.csv';
        $filePath = $fileDirectoryPath . '/' . $fileName;
        $filePathvar = $fileDirectoryPathvar . '/' . $fileName;
        $fronturl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'export/' . $fileName;

        $data = [];
        $data[] = [
            'sku' => 'sku',
            'cs_skus' => 'cs_skus',
        ];
        if ($storeid > 0)
        {
            $data['store'] = 'store';
        }

        $i = 0;
        foreach ($collection as $product)
        {
            //if ($i == 10)break;
            $cs_skus = $this->getRandomCategoryProductCollection($product->getCategoryIds(), $product->getSku());
            $data[] = [
                'sku' => $product->getSku(),
                'cs_skus' => $cs_skus,
            ];
            if ($storeid > 0)
            {
                $data['store'] = $this->getStoreCodeById($storeid);
            }
            $i++;
        }

        $this->csvProcessor
            ->setEnclosure('"')
            ->setDelimiter(',')
            ->saveData($filePath, $data);

        $this->csvProcessor
            ->setEnclosure('"')
            ->setDelimiter(',')
            ->saveData($filePathvar, $data);

        shell_exec("sed -i \"s/ -re/-re/\" $filePath");
        shell_exec("sed -i \"s/ -re/-re/\" $filePathvar");
        //$home = str_replace("/public_html", "", $this->directoryList->getPath(\Magento\Framework\App\Filesystem\DirectoryList::ROOT));
        //shell_exec("$home/magmi-m2/magmi/cli/magmi.cli.php -mode=update -profile=crosssell");

        return [
            "export" => true,
            "export_file" => $filePath,
            "front_file" => $fronturl
        ];
    }

    public function getRandomCategoryProductCollection($ids, $sku)
    {
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->addCategoriesFilter(['in' => $ids]);
        $collection->getSelect()->orderRand();
        $collection->setPageSize(5);

        if ($collection)
        {
            $data = [];
            $data[] = '-re::.*';
            $i = 0;
            foreach ($collection as $p)
            {
                if ($p->getSku() != $sku)
                {
                    if ($i == 4)
                    {
                        break;
                    }
                    $data[] = $p->getSku();
                    $i++;
                }
            }
            return implode(',', $data);
        }

        return '';
    }

}
