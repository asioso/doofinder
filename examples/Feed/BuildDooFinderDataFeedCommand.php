<?php
/**
 * This source file is available under  GNU General Public License version 3 (GPLv3)
 *
 * Full copyright and license information is available in LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Asioso GmbH (https://www.asioso.com)
 *
 */

namespace DooFinderBundle\Command;

use AppBundle\Model\DefaultProduct;
use AppBundle\Model\DefaultProductSpecial;
use DooFinderBundle\Adapter\DooFinderSearchEngine;
use DooFinderBundle\DependencyInjection\DooFinder\IDooFinderServiceHandler;
use DooFinderBundle\File\FileHandler;
use Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\ProductList\IProductList;
use Pimcore\Console\AbstractCommand;
use Pimcore\Model\DataObject;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class BuildDooFinderDataFeedCommand
 * @package DooFinderBundle\Command
 */
class BuildDooFinderDataFeedCommand extends AbstractCommand
{

    const BATCH_SIZE = 25;
    /**
     * @var IDooFinderServiceHandler
     */
    private $dooFinder;

    /**
     * @var
     */
    private $header;
    /**
     * @var FileHandler
     */
    private $doofinderFile;

    /**
     * @var
     */
    private $averageTime;


    /**
     * SetupDooFinderTypesCommand constructor.
     * @param IDooFinderServiceHandler $dooFinder
     * @param FileHandler $doofinderFile
     */
    public function __construct(IDooFinderServiceHandler $dooFinder, FileHandler $doofinderFile)
    {
        parent::__construct();

        $this->dooFinder = $dooFinder;
        $this->doofinderFile = $doofinderFile;
    }


    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('doo:build')
            ->addOption('id', null, InputOption::VALUE_REQUIRED, null)
            ->addOption('unpublished', null, InputOption::VALUE_NONE, "include unpublished")
            ->addOption('process', null, InputOption::VALUE_NONE, "notify doofinder to process new feeds")
            ->addOption('force', null, InputOption::VALUE_NONE, "notify doofinder to process new feeds")
            ->addOption('gzip', null, InputOption::VALUE_NONE, "compress your feeds")
            ->addOption('notify', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, "notify doofinder to process new feeds")
            ->setDescription('create datafeeds for all published objects to dooFinder SearchEngines (definition by config)');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);

        $unpublished = false;

        if ($useUnpublished = $input->getOption('unpublished')) {
            $unpublished = $useUnpublished;
        }
        // you might need to disable the sql logger in a dev environment!
        //Db::getConnection()->getConfiguration()->setSQLLogger(null);

        // generate feeds for watched classes
        foreach ($this->dooFinder->getWatchedClasses() as $watchedClass) {

            $class = $watchedClass['class'];
            $this->io->note(sprintf("fetching all published objects of %s", $class));

            if ($class == DefaultProduct::class || $class == DefaultProductSpecial::class) {
                $this->processProducts($class);
            } else {
                //not products
                $listing = $class::getList($watchedClass['listing_arguments']);
                $listing->getTotalCount();
                $listing->loadIdList();


                foreach ($listing as $item) {
                    if (is_subclass_of($item, $class)) {
                        $this->processTree($item, $class);
                    }

                }
                $this->header = null;
                $this->io->success("done");
            }
        }

        //compress feeds with gzip
        if ($input->getOption('gzip')) {
            $this->doofinderFile->compressFiles();
            $this->io->success('files compressed');
        }

        //request to start crawl if possible
        if ($processFeed = $input->getOption('process')) {
            $force = $input->getOption('force');

            $this->io->note('starting processing at doofinder');
            $rows = array();
            foreach ($this->dooFinder->getConfiguredEngines() as $engine) {
                /**
                 * @var $engine DooFinderSearchEngine
                 */
                $rows[] = array($engine->getHashId(), $engine->getType());
            }
            $this->io->note('following configuration has been retrieved:');
            $this->io->table(array("hashId", "dataType"), $rows);


            if (!$force) {
                $helper = $this->getHelper('question');
                $question = new ConfirmationQuestion('Continue with this action?', false);
                if (!$helper->ask($input, $output, $question)) {
                    return;
                }
            }


            $response = $this->dooFinder->startProcessingForConfiguredEngines();
            $this->io->success('processing has been started!');

        }

        //notify someone that all this happened!
        if ($recipients = $input->getOption('notify')) {

            $engines = "";
            foreach ($this->dooFinder->getConfiguredEngines() as $engine) {
                /**
                 * @var $engine DooFinderSearchEngine
                 */
                $engines .= $engine->getHashId() . " => " . $engine->getType() . "\n";
            }
            $processFeed = "";
            if ($input->getOption('process')) {
                $processFeed = "Doofinder has been notified to process the new feed. Please check for yourself!";
            }


            $mail = new \Pimcore\Mail();
            $mail->addTo($recipients);
            //example sender!
            //$mail->setFrom('someaddress@email.com');

            $mail->setSubject("DOOFINDER Feed");
            $mail->setBodyText("Jo!\n A new Datafeed has been created for the following engines and data types:\n" . $engines . "\n" . $processFeed . "\n best, F ");
            try {
                $mail->send();
            } catch (\Exception $e) {

            }

        }


    }

    private function processTree($item, $class)
    {
        if (!$item->isPublished()) {
            return;
        }
        foreach ($item->getChildren() as $child) {
            $this->processTree($child, $class);
        }

        if (is_subclass_of($item, $class)) {
            if ($item->getType() == 'page') {
                $this->header = $this->dooFinder->getHeaderForEngine($item);
                $this->update($item);
            } else {
                //not sure what to do here?
            }

        }

    }

    /**
     * @param $class
     */
    private function processProducts($class)
    {
        try {
            /**
             * @var $listing IProductList
             */
            $listing = \Pimcore\Bundle\EcommerceFrameworkBundle\Factory::getInstance()->getIndexService()->getProductListForCurrentTenant();


            if ($class == DefaultProduct::class) {
                $listing->addCondition("shopProduct = '1' ", 'shopProduct');
                $listing->addCondition("o_classId = '20' ", 'o_classId');
            } elseif ($class == DefaultProductSpecial::class) {
                $listing->addCondition("o_classId = '26' ", 'o_classId');
            }

            $listing->addCondition('active = "1"', "active");
            $listing->addCondition('o_virtualProductActive = 1', "o_virtualProductActive");


            $count = $listing->count();
            $this->io->note(sprintf("%d items", $count));

            /*
            //useful for direct interactions
            $helper = $this->getHelper('question');
            $question = new ConfirmationQuestion('Continue with this action? ', false);

            if (!$helper->ask($input, $output, $question)) {
                return;
            }
            */

            $this->io->progressStart($count);
            $row = 0;

            $written = 0;
            $start = microtime(true);
            while ($row < $count) {

                $list = \Pimcore\Bundle\EcommerceFrameworkBundle\Factory::getInstance()->getIndexService()->getProductListForCurrentTenant();

                if ($class == DefaultProduct::class) {
                    $list->addCondition("shopProduct = '1' ", 'shopProduct');
                    $list->addCondition("o_classId = '20' ", 'o_classId');

                } elseif ($class == DefaultProductSpecial::class) {
                    $list->addCondition("o_classId = '26' ", 'o_classId');
                }
                $list->addCondition('active = "1"', "active");
                $list->addCondition('o_virtualProductActive = "1"', "o_virtualProductActive");

                $list->setLimit(self::BATCH_SIZE);

                if ($row > 0) {
                    //$listing->setOffset($row);
                    $list->setOffset($row);
                }

                $processed = $row;
                $lines = $this->runBatch($list, $row);
                $written += $lines;
                $this->io->progressAdvance($row - $processed);
                //clear up some things
                $list = null;
                unset($list);

            }
            $stop = microtime(true);


            $this->header = null;
            $this->io->progressFinish();

            $this->averageTime = $this->averageTime / $row;

            $this->io->success(sprintf("total time: %s seconds", $stop - $start));
            $this->io->success(sprintf("lines written: %s ", $written));
            $this->io->success(sprintf("avg: %s seconds/item", $this->averageTime));

        } catch (\Exception $e) {
            //this class failed... try the next
            $this->io->error($e->getMessage());
            return;
        } finally {
            $this->header = null;
        }
    }

    /**
     * @param IProductList $listing
     * @param $row
     * @return int
     */
    private function runBatch(IProductList $listing, &$row)
    {
        if ($listing->count() == 0) {
            $row++;
            return 0;
        }

        $start = null;
        $stop = null;
        $linesWritten = 0;
        foreach ($listing->getItems($row, self::BATCH_SIZE) as $object) {
            $start = microtime(true);

            $this->header = $this->dooFinder->getHeaderForEngine($object);

            $linesWritten += $this->update($object);

            //clear up some things
            unset($object);

            if ($row % 10 == 0 && $row > 1) {
                //flush all!
                //$this->io->note("collecting garbage");
                \Pimcore::collectGarbage();
            }
            $row++;
            $stop = microtime(true);
            $this->averageTime += $stop - $start;
        }

        return $linesWritten;

    }

    /**
     * @param $object
     * @return int
     */
    private function update($object)
    {
        $c = 0;
        $feed = $this->dooFinder->getValuesForEngine($object);

        foreach ($feed as $engineKey => $feedContent) {
            if ($this->doofinderFile->writeToFile($feedContent['engine'], $feedContent['type'], $feedContent['data'], $this->header)) {
                $c++;
            }
        }

        if ($c == 0) {
            $this->io->note(sprintf("object with id: %s has been skipped", $object->getId()));
        }

        return $c;
    }

    /**
     * @param $listing
     * @return null|DataObject\Listing
     * @throws \Exception
     */
    private function getListing($listing): DataObject\Listing
    {
        try {
            return new $listing();
        } catch (\Exception $e) {
            $this->io->error($e->getMessage());
            throw $e;
        }
    }
}
