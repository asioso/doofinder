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

use DooFinderBundle\DependencyInjection\DooFinder\IDooFinderService;
use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class SetupDooFinderTypesCommand
 * @package DooFinderBundle\Command
 */
class SetupDooFinderTypesCommand extends AbstractCommand
{
    /**
     * @var IDooFinderService
     */
    private $dooFinder;

    /**
     * SetupDooFinderTypesCommand constructor.
     * @param IDooFinderService $dooFinder
     */
    public function __construct(IDooFinderService $dooFinder)
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
            ->setName('doo:setup')
            ->setDescription('checks and creates for types in dooFinder SearchEngines');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);

        foreach ($this->dooFinder->getRequiredTypesFromConfigPerEngine() as $hashId => $types) {
            $availableTypes = $this->dooFinder->getTypes($hashId);

            foreach ($types as $type) {
                if (!in_array($type, $availableTypes)) {
                    $helper = $this->getHelper('question');
                    $question = new ChoiceQuestion(sprintf('Type "%s" is missing in engine "%s"! Do you want to add it?', $type, $hashId), array('yes', 'no'),
                        1);

                    if ($helper->ask($input, $output, $question) == 0) {
                        $this->dooFinder->addType($hashId, $type);
                        $this->io->success(sprintf("Type: %s is added to engine: %s", $type, $hashId));
                    }

                } else {
                    $this->io->success(sprintf("Type: %s is available in engine: %s", $type, $hashId));
                }
            }
        }
    }
}
