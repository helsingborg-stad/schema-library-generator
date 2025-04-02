<?php

namespace SchemaOrg\Generator;

use SchemaOrg\Generator\Parser\DefinitionParser;
use SchemaOrg\Generator\Writer\Filesystem;

class PackageGenerator
{
    public function __construct(private string $localRoot, private string $targetDirectory)
    {
    }

    public function generate(Definitions $definitions)
    {
        $types = (new DefinitionParser())->parse($definitions);

        $filesystem = new Filesystem(__DIR__ . '/..', $this->localRoot, $this->targetDirectory);

        $filesystem->clear();

        $filesystem->cloneStaticFiles();

        $types->each(function (Type $type) use ($filesystem, $types) {
            $type->setTypeCollection($types);
            $filesystem->createType($type);
        });

        $filesystem->createBuilderClass($types);
    }
}
