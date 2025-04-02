<?php

namespace SchemaOrg\Generator\Writer;

use League\Flysystem\Filesystem as Flysystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use SchemaOrg\Generator\Type;
use SchemaOrg\Generator\TypeCollection;

class Filesystem
{
    /** @var \League\Flysystem\Filesystem */
    protected $flysystem;
    
    /** @var \League\Flysystem\Filesystem */
    protected $flysystemLocal;

    /** @var \SchemaOrg\Generator\Writer\Template */
    protected $contractTemplate;

    /** @var \SchemaOrg\Generator\Writer\Template */
    protected $typeTemplate;

    /** @var \SchemaOrg\Generator\Writer\Template */
    protected $builderClassTemplate;

    /** @var \SchemaOrg\Generator\Writer\Template */
    protected $graphClassTemplate;

    /** @var \SchemaOrg\Generator\Writer\Template */
    protected $multiTypedEntityClassTemplate;

    /** @var \SchemaOrg\Generator\Writer\Template[] */
    protected $staticTemplates;

    public function __construct(string $root, string $localRoot, private string $targetDirectory, private string $organization)
    {
        $adapter         = new LocalFilesystemAdapter($root);
        $this->flysystem = new Flysystem($adapter);
        
        $adapterLocal         = new LocalFilesystemAdapter($localRoot);
        $this->flysystemLocal = new Flysystem($adapterLocal);
        Filters::$organization = $this->organization;
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
        $files = $this->flysystem->listContents('generator/templates/twig/static', true);

        foreach ($files as $file) {
            if ($file['type'] !== 'file') {
                continue;
            }

            $templateName = str_replace('generator/templates/twig', '', $file['path']);
            $template = new Template($templateName);
            $fileName = str_replace('generator/templates/twig/static', $this->targetDirectory, $file['path']);
            $fileName = str_replace('.twig', '', $fileName);

            $this->flysystemLocal->write( $fileName, $template->render(['organization' => $this->organization]) );
        }
    }

    public function createType(Type $type)
    {
        $this->flysystemLocal->write(
            "{$this->targetDirectory}/Contracts/{$type->className}Contract.php",
            $this->contractTemplate->render(['type' => $type, 'organization' => $this->organization])
        );

        $this->flysystemLocal->write(
            "{$this->targetDirectory}/{$type->className}.php",
            $this->typeTemplate->render(['type' => $type, 'organization' => $this->organization])
        );
    }

    public function createBuilderClass(TypeCollection $types)
    {
        $this->flysystemLocal->write(
            "{$this->targetDirectory}/Schema.php",
            $this->builderClassTemplate->render(['types' => $types->toArray(), 'organization' => $this->organization])
        );

        $this->flysystemLocal->write(
            "{$this->targetDirectory}/Graph.php",
            $this->graphClassTemplate->render(['types' => $types->toArray(), 'organization' => $this->organization])
        );

        $this->flysystemLocal->write(
            "{$this->targetDirectory}/MultiTypedEntity.php",
            $this->multiTypedEntityClassTemplate->render(['types' => $types->toArray(), 'organization' => $this->organization])
        );
    }
}
