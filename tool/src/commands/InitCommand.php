<?php

namespace NoMoreWar\Casualties\Commands;

use NoMoreWar\Casualties\Traits\InitalizationTrait;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitCommand extends BaseCommand
{
    use InitalizationTrait, LockableTrait;

    /**
     * Command configuration.
     *
     * @return void.
     */
    protected function configure()
    {
        $this->setName('init')->setDescription('Convert the dataset files into some database.');
    }

    /**
     * Command execution.
     *
     * @param  InputInterface $input    An symfony inputInterface Instance.
     * @param  OutputInterface $output  An symfony Output interface Instance.
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (! $this->lock()) { // The command is running in another process.
            $output->writeln('<info>[ERROR]: The command is already running in another process.</info>');

            return 0;
        }

        $vietnamFileUrl = 'https://raw.githubusercontent.com/Tjoosten/casualty-dataset/master/sources/DCAS.VN.EXT08.DAT';
        $koreanFileUrl  = 'https://raw.githubusercontent.com/Tjoosten/casualty-dataset/master/sources/DCAS.KS.EXT08.DAT';

        if ($this->createDatabaseTables($this->pdo)) { // Database init
            $output->writeln('<info>[STATUS]: The data table has been created.</info>');
        } //> END Database init.

        if (count($this->pdo->query('SELECT count(service_number) FROM vietnam_casualties')->fetch()) > 0) { // Vietnam casualties.
            $vietnamCount = count($this->pdo->query('SELECT service_number FROM vietnam_casualties')->fetchAll());

            $output->writeln("<info>[INFO]:   The Vietnam table already has {$vietnamCount} data records.</info>");
            $output->writeln("<info>[STATUS]: Empty the Vietnam table.</info>");

            $this->truncateTable($this->pdo, 'vietnam_casualties');
        }

        if ($this->seedNaraData($this->pdo, $vietnamFileUrl, 'vietnam_casualties')) {
            $vietnamRows = count($this->pdo->query('SELECT service_number FROM vietnam_casualties')->fetchAll());

            $output->writeln('<info>[STATUS]: The data about the Vietnam war has been imported.</info>');
            $output->writeln("<info>[INFO]:   The vietnam casualties table has now $vietnamRows data rows");
        } //> END Vietnam casualties.

        if (count($this->pdo->query('SELECT count(service_number) FROM korean_casualties')->fetch()) > 0) { // Korean casualties.
            $koreanCount = count($this->pdo->query('SELECT service_number FROM korean_casualties')->fetchAll());

            $output->writeln("<info>[INFO]:   The Korean table already has {$koreanCount} data records.</info>");
            $output->writeln("<info>[STATUS]: Empty the Korean table.</info>");

            $this->truncateTable($this->pdo, 'korean_casualties');
        }

        if ($this->seedNaraData($this->pdo, $koreanFileUrl, 'korean_casualties')) {
            $koreanRows = count($this->pdo->query('SELECT * FROM korean_casualties')->fetchAll());

            $output->writeln('<info>[STATUS]: The data about the Korean war has been imported.</info>');
            $output->writeln("<info>[INFO]:   The korean casualties table has now $koreanRows data rows");
        } //> END Korean casualties.

        if (count($this->pdo->query('SELECT count(service_number) FROM all_casualties')->fetch()) > 0) { // All casualtie table?
            $allCount = count($this->pdo->query('SELECT service_number FROM all_casualties')->fetchAll());

            $output->writeln("<info>[STATUS]: The all casualty table has already {$allCount} datarows.</info>");
            $output->writeln('<info>[INFO]:   Empty the all casualties table. </info>');
        }

        if ($this->createALlCasualties($this->pdo, $vietnamFileUrl, $koreanFileUrl, 'all_casualties')) {
            $totalRows = count($this->pdo->query('SELECT service_number FROM all_casualties')->fetchAll());

            $output->writeln('<info>[STATUS]: The data about the Korean and Vietnnam war has been imported.</info>');
            $output->writeln("<info>[INFO]:   The casualties table in this table has now $totalRows data rows");
        } //> End all casualty table.

        $output->writeln("<info>[INFO]:   The init command is done. Quit process</info>");
        $this->release(); // Release the command.
    }
}