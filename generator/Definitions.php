<?php

namespace SchemaOrg\Generator;

use Illuminate\Support\Collection;
use SchemaOrg\Generator\Parser\JsonLdParser;
use SchemaOrg\Generator\Source\Source;

class Definitions
{
    protected array $sources;

    protected string $tempDir = __DIR__ . '/temp';

    /**
     * @param  \SchemaOrgs\Generator\Source\SourceInterface[] $sources
     */
    public function __construct(array $sources)
    {
        $this->sources = $sources;
    }

    public function preload(): void
    {
        foreach ($this->sources as $source) {
            $this->loadSource($source, false);
        }
    }

    public function query(string $selector): Collection
    {
        $items = [];

        foreach ($this->sources as $source) {
            $items = [...$items, ...(new JsonLdParser($this->loadSource($source)))->filter($selector)->all()];
        }

        return new Collection($items);
    }

    protected function loadSource(Source $source, bool $fromCache = true): string
    {
        $cachePath = $this->tempDir . '/' . $source->getName() . '.jsonld';

        if ($fromCache && file_exists($cachePath)) {
            return file_get_contents($cachePath);
        }

        $jsonLd = file_get_contents($source->getSourceFile());

        file_put_contents($cachePath, $jsonLd);

        return $jsonLd;
    }
}
