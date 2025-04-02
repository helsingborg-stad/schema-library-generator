<?php

namespace SchemaOrg\Tests;

use GrahamCampbell\Analyzer\AnalysisTrait;
use PHPUnit\Framework\TestCase;

class AnalysisTest extends TestCase
{
    use AnalysisTrait;

    protected static function getPaths(): array
    {
        return [
            __DIR__.'/../.generated',
        ];
    }
}

uses(AnalysisTest::class);
