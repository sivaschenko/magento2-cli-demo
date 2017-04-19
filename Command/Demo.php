<?php

namespace Sivaschenko\Cli\Command;

use Sivaschenko\Cli\Model\Meetup;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class Demo extends Command
{
    /**#@+
     * Arguments' and options' keys
     */
    const ARGUMENT_MEETUP_GROUP_URL = 'meetup_group_url';
    const ARGUMENT_MEETUP_ID = 'meetup_id';
    /**#@-*/

    /**
     * @var Meetup
     */
    private $meetup;

    /**
     * Demo constructor.
     * @param Meetup $meetup
     */
    public function __construct(Meetup $meetup)
    {
        $this->meetup = $meetup;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('sivaschenko:demo')
            ->addArgument(
                static::ARGUMENT_MEETUP_GROUP_URL,
                InputArgument::OPTIONAL,
                'Meetup Group URL',
                'Magento-Developers-Dublin'
            )->addArgument(
                static::ARGUMENT_MEETUP_ID,
                InputArgument::OPTIONAL,
                'Meetup ID',
                '238921619'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($this->areWeOnline($input, $output)) {
            $output->writeln('<fg=green>Nice. Loading RSVPs!</>');
            $this->renderTableOfRsvps(
                $input->getArgument(static::ARGUMENT_MEETUP_GROUP_URL),
                $input->getArgument(static::ARGUMENT_MEETUP_ID),
                $output
            );
        } else {
            $output->writeln('<fg=red>OK. Running offline demo.</>');
            $this->renderProgressBar($output);
            $this->renderTable($output);
        }
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return bool
     */
    private function areWeOnline(InputInterface $input, OutputInterface $output)
    {
        $question = new ConfirmationQuestion('Have you managed to connect to network?', true);
        return $this->getHelper('question')->ask($input, $output, $question);
    }

    /**
     * @param string $meetup
     * @param int|string $meetupId
     * @param OutputInterface $output
     */
    private function renderTableOfRsvps($meetup, $meetupId, OutputInterface $output)
    {
        $table = new Table($output);
        $table->setHeaders(['Hello folks!', 'Meetuper since']);

        $progressBar = new ProgressBar($output);

        $table->setRows($this->meetup->getRsvps($meetup, $meetupId, $progressBar));

        $output->writeln('');

        $table->render();
    }

    /**
     * @param OutputInterface $output
     */
    private function renderTable(OutputInterface $output)
    {
        $table = new Table($output);
        $table->setHeaders(['1', '2', '3']);
        $table->setRows(
            [
                ['first', 'second', 'third'],
                ['first', 'second', 'third']
            ]
        );
        $table->render();
    }

    /**
     * @param OutputInterface $output
     */
    private function renderProgressBar(OutputInterface $output)
    {
        $progressBar = new ProgressBar($output);
        $progressBar->start(142);
        for ($i = 0; $i < 142; $i++) {
            $progressBar->advance();
            usleep(10000);
        }
        $progressBar->finish();
        $output->writeln('');
    }
}