<?php

namespace SchemaOrg\Generator\Parser;

use SchemaOrg\Generator\Constant;
use SchemaOrg\Generator\Definitions;
use SchemaOrg\Generator\Parser\Tasks\ParseConstant;
use SchemaOrg\Generator\Parser\Tasks\ParseProperty;
use SchemaOrg\Generator\Parser\Tasks\ParseType;
use SchemaOrg\Generator\Property;
use SchemaOrg\Generator\Type;
use SchemaOrg\Generator\TypeCollection;

class DefinitionParser
{
    public function parse(Definitions $definitions): TypeCollection
    {
        $types = $definitions
            ->query('rdfs:Class')
            ->map(static function (array $jsonLdArray): ?Type {
                return call_user_func(ParseType::fromJsonLdArray($jsonLdArray));
            })
            ->filter();

        $properties = $definitions
            ->query('rdf:Property')
            ->map(static function (array $jsonLdArray): ?Property {
                return call_user_func(ParseProperty::fromJsonLdArray($jsonLdArray));
            })
            ->filter();

        $constants = collect([]);

        foreach ($types as $type) {
            $constants = $constants->merge(
                $definitions
                    ->query($type->resource)
                    ->map(static function (array $constant) use ($type): ?Constant {
                        $constant = call_user_func(ParseConstant::fromJsonLdArray($constant));

                        if (is_null($constant)) {
                            return null;
                        }

                        $constant->type = $type->name;

                        return $constant;
                    })
                    ->filter()
            );
        }

        return new TypeCollection($types, $properties, $constants);
    }
}
