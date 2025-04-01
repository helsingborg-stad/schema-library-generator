<?php

namespace Spatie\SchemaOrg\Generator\Writer;

use League\Flysystem\Filesystem as Flysystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use Spatie\SchemaOrg\Generator\Type;
use Spatie\SchemaOrg\Generator\TypeCollection;

class Filesystem
{
    /** @var \League\Flysystem\Filesystem */
    protected $flysystem;
    
    /** @var \League\Flysystem\Filesystem */
    protected $flysystemLocal;

    /** @var \Spatie\SchemaOrg\Generator\Writer\Template */
    protected $contractTemplate;

    /** @var \Spatie\SchemaOrg\Generator\Writer\Template */
    protected $typeTemplate;

    /** @var \Spatie\SchemaOrg\Generator\Writer\Template */
    protected $builderClassTemplate;

    /** @var \Spatie\SchemaOrg\Generator\Writer\Template */
    protected $graphClassTemplate;

    /** @var \Spatie\SchemaOrg\Generator\Writer\Template */
    protected $multiTypedEntityClassTemplate;

    public function __construct(string $root, string $localRoot, private string $targetDirectory)
    {
        $adapter         = new LocalFilesystemAdapter($root);
        $this->flysystem = new Flysystem($adapter);
        
        $adapterLocal         = new LocalFilesystemAdapter($localRoot);
        $this->flysystemLocal = new Flysystem($adapterLocal);

        $this->contractTemplate              = new Template('Contract.php.twig');
        $this->typeTemplate                  = new Template('Type.php.twig');
        $this->builderClassTemplate          = new Template('Schema.php.twig');
        $this->graphClassTemplate            = new Template('Graph.php.twig');
        $this->multiTypedEntityClassTemplate = new Template('MultiTypedEntity.php.twig');
    }

    public function clear()
    {
        $this->flysystemLocal->deleteDirectory($this->targetDirectory);
        $this->flysystemLocal->createDirectory($this->targetDirectory);
    }

    public function cloneStaticFiles()
    {
        $files = $this->flysystem->listContents('generator/templates/static', true);

        foreach ($files as $file) {
            if ($file['type'] !== 'file') {
                continue;
            }

            $this->flysystemLocal->write(
                str_replace('generator/templates/static', $this->targetDirectory, $file['path']),
                $this->flysystem->read($file['path'])
            );
        }
    }

    public function createType(Type $type)
    {
        $this->flysystemLocal->write(
            "{$this->targetDirectory}/Contracts/{$type->className}Contract.php",
            $this->contractTemplate->render(['type' => $type])
        );

        $this->flysystemLocal->write(
            "{$this->targetDirectory}/{$type->className}.php",
            $this->typeTemplate->render(['type' => $type])
        );
    }

    public function createBuilderClass(TypeCollection $types)
    {
        $this->flysystemLocal->write(
            "{$this->targetDirectory}/Schema.php",
            $this->builderClassTemplate->render(['types' => $types->toArray()])
        );

        $this->flysystemLocal->write(
            "{$this->targetDirectory}/Graph.php",
            $this->graphClassTemplate->render(['types' => $types->toArray()])
        );

        $this->flysystemLocal->write(
            "{$this->targetDirectory}/MultiTypedEntity.php",
            $this->multiTypedEntityClassTemplate->render(['types' => $types->toArray()])
        );
    }
}
