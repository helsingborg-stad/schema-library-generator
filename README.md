# Schema.org Types And ld+json Generator

This package is built on the excellent [spatie/schema-org](https://github.com/spatie/schema-org) package. However, it has been modified to include only the generator functionality.

The generator is designed to create a types package for working with [Schema.org](https://schema.org) objects, making it easier to define and use structured data in your projects.

## Installation
You can install the package via composer:

```bash
composer require --dev spatie/schema-org-types
```
## Usage
You can use the package to generate a types package for your project. The generator will create a set of classes that represent the Schema.org types, making it easier to work with structured data.
### Generating the Types Package
To generate the types package, you can use the following command:

```bash
vendor/bin/generate-schema-library <directory>
```

