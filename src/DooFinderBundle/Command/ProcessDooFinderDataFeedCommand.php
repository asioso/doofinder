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

use DooFinderBundle\Adapter\DooFinderSearchEngine;
use DooFinderBundle\DependencyInjection\DooFinder\IDooFinderServiceHandler;
use DooFinderBundle\File\FileHandler;
use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class ProcessDooFinderDataFeedCommand
 * @package DooFinderBundle\Command
 */
class ProcessDooFinderDataFeedCommand extends AbstractCommand
{

    /**
     * @var IDooFinderServiceHandler
     */
    private $dooFinder;


    /**
     * SetupDooFinderTypesCommand constructor.
     * @param IDooFinderServiceHandler $dooFinder
     * @param FileHandler $doofinderFile
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
            ->setName('doo:process')
            ->addOption('force', null, InputOption::VALUE_NONE, "no interaction")
            ->addOption('notify', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, "notify doofinder to process new feeds")
            ->setDescription('notify doofinder to process new feeds');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);

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
        #dump($response);
        if ($recipients = $input->getOption('notify')) {

            $engines = "";
            foreach ($this->dooFinder->getConfiguredEngines() as $engine) {
                /**
                 * @var $engine DooFinderSearchEngine
                 */
                $engines .= $engine->getHashId() . " => " . $engine->getType() . "\n";
            }

            try {
                $mail = new \Pimcore\Mail();
                $mail->setSubject("DOOFINDER Feed");
                $mail->addTo($recipients);
                $mail->setBodyText("
            Jo!\n
            A new Datafeed has been created for the following engines and data types:\n" . $engines);
                $mail->send();
            } catch (\Exception $e) {

            }


        }


        $this->io->success('processing has been started!');


    }

}
