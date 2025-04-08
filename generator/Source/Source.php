<?php

namespace SchemaOrg\Generator\Source;

class Source implements SourceInterface
{
    public function __construct(
        private string $name,
        private string $sourceFile, 
        private string $location)
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSourceFile(): string
    {
        return $this->sourceFile;
    }

    public function getLocation(): string
    {
        return $this->location;
    }
}