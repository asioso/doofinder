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
use Doofinder\Api\Management\Errors\ThrottledResponse;
use DooFinderBundle\Adapter\AbstractDooFinderSearchableItem;
use DooFinderBundle\DependencyInjection\DooFinder\IDooFinderServiceHandler;
use Pimcore\Console\AbstractCommand;
use Pimcore\Model\DataObject;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class PushObjectsToDooFinderCommand
 * @package DooFinderBundle\Command
 */
class PushObjectsToDooFinderCommand extends AbstractCommand
{
    /**
     * @var IDooFinderServiceHandler
     */
    private $dooFinder;

    /**
     * SetupDooFinderTypesCommand constructor.
     * @param IDooFinderServiceHandler $dooFinder
     */
    public function __construct(IDooFinderServiceHandler $dooFinder)
    {
        parent::__construct();

        $this->dooFinder = $dooFinder;
    }


    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('doo:push')
            ->addOption('id', null, InputOption::VALUE_REQUIRED, null)
            ->setDescription('push all published objects to dooFinder SearchEngines (definition by config)');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);

        foreach ($this->dooFinder->getWatchedClasses() as $watchedClass) {
            $class = $watchedClass['class'];
            $this->io->note(sprintf("fetching all published objects of %s", $class));
            $listing = $watchedClass['listing'];

            try {
                $listing = $this->getListing($listing);
                if ($input->getOption('id')) {
                    $listing->setCondition('o_id = ?', [$input->getOption('id')]);
                }

                $listing->setUnpublished(false);
                #$listing->setLimit(1);
                $count = $listing->getTotalCount();
                #$listing->loadIdList();
                $this->io->note(sprintf("%d items", $count));

                $this->io->progressStart($count);
                foreach ($listing as $object) {
                    $this->io->note($object->getName());
                    $this->update($object);

                    $this->io->progressAdvance();
                }
                $this->io->progressFinish();
            } catch (\Exception $e) {
                //this class failed... try the next
                $this->io->error($e->getMessage());
                continue;
            }
        }
    }

    /**
     * @param $object
     * @param int $retry
     */
    private function update($object, $retry = 0)
    {
        if ($retry > 2) {
            /**
             * @var $object AbstractDooFinderSearchableItem
             */
            $this->io->error(sprintf("couldn't push %s anymore. too many retries", $object->getKey()));
            return;
        }

        try {
            $this->dooFinder->handleUpdate($object);
        } catch (ThrottledResponse $exception) {
            // wait a bit and try again
            sleep(2);
            $this->update($object, ++$retry);
        }
        //what else could happen???
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
