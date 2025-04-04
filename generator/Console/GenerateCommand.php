<?php

namespace SchemaOrg\Generator\Console;

use SchemaOrg\Generator\Definitions;
use SchemaOrg\Generator\PackageGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('generate')
            ->setDescription('Generate the package code from the schema.org docs')
            ->addArgument('directory', InputArgument::REQUIRED, 'Destination target directory for the generated code')
            ->addOption('organization', 'o', InputOption::VALUE_REQUIRED, 'Organization name for the generated code, is used in the namespace of the generated code', 'SchemaOrg')
            ->addOption('additionalSources', 'a', InputOption::VALUE_REQUIRED, 'Additional sources to include in the package. Format: "source1:https://example.com/source1.jsonld,source2:https://example.com/source2.jsonld"')
            ->addOption('local', 'l', InputOption::VALUE_NONE, 'Use a cached version of the source');
    }

    /**
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Generating package code...');
        $localRoot = getcwd();
        $localRoot = realpath($localRoot);
        $localRoot = str_replace('\\', '/', $localRoot);

        $generator = new PackageGenerator($localRoot, $input->getArgument('directory'), $input->getOption('organization'));

        $definitions = new Definitions($this->getDefinitionSources($input));

        if (! $input->getOption('local')) {
            $definitions->preload();
        }

        $generator->generate($definitions);

        $output->writeln('Done!');

        return 0;
    }

    private function getDefinitionSources(InputInterface $input)
    {
        $sources           = [ 'core' => 'https://raw.githubusercontent.com/schemaorg/schemaorg/main/data/releases/28.1/schemaorg-all-https.jsonld'];
        $additionalSources = $input->getOption('additionalSources');

        if ($additionalSources) {
            $additionalSources = explode(',', $additionalSources);
            foreach ($additionalSources as $source) {
                $source              = explode(':', $source, 2);
                $sources[$source[0]] = $source[1];
            }
        }

        return $sources;
    }
}
