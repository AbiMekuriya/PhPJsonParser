# PhPJsonParser

A simple reusable php OOP API for reading and manipulating json data from files.


## Why
This php library was specifically built for massive json-based configs, such as minecrafts Bedrock Edition addon system. Massive json based config 
systems usually have some identical json data/fields between configs. And managing them is sometimes an annoying hassle. Using this api
you can write json parsers that are moulder allowing you to implement the same reader twice.

## Requirements
The following PHP versions are guaranteed to work.
 - PHP 5.6
 - PHP 7
 - PHP 8
 
## Usage
  
 Download the library using [composer](https://packagist.org/packages/abimekuriya/php-json-parser):

```php
$ composer require abimekuriya/php-json-parser
```

We can now begin working with the Api.

### Quick Example
```php
<?php
use abimek\JsonParser\FileJsonParser;
use abimek\JsonParser\parsers\ComponentParser;
use abimek\JsonParser\requirements\Requirement;

 ComponentParser::register(new ComponentParser("nameIdentifier", function ($data, &...$args){
     $args[0] = $data["name"];
     $args[1] = $data["identifier"];
 }));
$fileParser = FileJsonParser::create(getcwd() . "/test.json", []);
$fileParser->addParseComponent(ComponentParser::get("nameIdentifier", $name, $identifier), [new Requirement("name", Requirement::TYPE_STRING)]);
$fileParser->onComplete(function ()use(&$name, &$identifier){
    echo $name;
    echo $identifier;
});
$fileParser->execute();
```
