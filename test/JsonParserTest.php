<?php

namespace Test;

use JsonParser\FileJsonParser;
use JsonParser\parsers\ComponentParser;
use PHPUnit\Framework\TestCase;

class JsonParserTest extends TestCase{

    public function testComponentRegistration(){
        ComponentParser::register(new ComponentParser("nameIdentifier", function ($data, &...$args){
            $args[0] = $data["name"];
            $args[1] = $data["identifier"];
        }));
        $this->assertTrue(ComponentParser::get("nameIdentifier") instanceof ComponentParser);
    }

    public function testNormalComponent(){
        ComponentParser::register(new ComponentParser("nameIdentifier", function ($data, &...$args){
            $args[0] = $data["name"];
            $args[1] = $data["identifier"];
        }));
        $fileParser = FileJsonParser::create(getcwd() . "/test.json", []);
        $fileParser->addParseComponent(ComponentParser::get("nameIdentifier", $name, $identifier));
        $fileParser->onComplete(function ()use(&$name, &$identifier){
            $class = new class(){
                public $name;
                public $identifier;
            };
            $class->name = $name;
            $class->identifier = $identifier;
            $this->assertTrue($name !== null);
        });
        $fileParser->execute();
    }
}