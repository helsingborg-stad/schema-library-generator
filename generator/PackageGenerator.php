<?php

namespace Spatie\SchemaOrg\Generator;

use Spatie\SchemaOrg\Generator\Parser\DefinitionParser;
use Spatie\SchemaOrg\Generator\Writer\Filesystem;

class PackageGenerator
{
    public function __construct(private string $targetDirectory)
    {
    }

    public function generate(Definitions $definitions)
    {
        $types = (new DefinitionParser())->parse($definitions);

        $filesystem = new Filesystem(__DIR__ . '/..', $this->targetDirectory);

        $filesystem->clear();

        $filesystem->cloneStaticFiles();

        $types->each(function (Type $type) use ($filesystem, $types) {
            $type->setTypeCollection($types);
            $filesystem->createType($type);
        });

        $filesystem->createBuilderClass($types);
    }
}
