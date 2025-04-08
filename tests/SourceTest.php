<?php

namespace GeneratedDefault\Tests;

use SchemaOrg\Generator\Source\Source;

test('getName() returns the provided name', function () {
    $source = new Source('schema', 'https://schema.org/docs/schema_org_all-https.jsonld', 'https://schema.org');

    expect($source->getName())->toBe( 'schema' );
});

test('getSourceFile() returns the provided source file', function () {
    $source = new Source('schema', 'https://schema.org/docs/schema_org_all-https.jsonld', 'https://schema.org');

    expect($source->getSourceFile())->toBe( 'https://schema.org/docs/schema_org_all-https.jsonld' );
});

test('getLocation() returns the provided location', function () {
    $source = new Source('schema', 'https://schema.org/docs/schema_org_all-https.jsonld', 'https://schema.org');

    expect($source->getLocation())->toBe( 'https://schema.org' );
});