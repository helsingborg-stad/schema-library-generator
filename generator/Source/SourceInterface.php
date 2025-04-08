<?php

namespace SchemaOrg\Generator\Source;

interface SourceInterface
{
    /**
     * Get the name of the source.
     * 
     * @return string The name of the source. E.g. "schema".
     */
    public function getName(): string;

    /**
     * Get the source file of the source.
     * 
     * @return string The URL of the source file. E.g. "https://schema.org/docs/schema_org_all-https.jsonld". Can also be a local file path.
     */
    public function getSourceFile(): string;

    /**
     * Get the location of the source.
     * 
     * @return string
     */
    public function getLocation(): string;
}