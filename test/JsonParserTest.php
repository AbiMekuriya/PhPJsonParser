<?php

namespace abimek\PhPJsonParser\Test;

use abimek\JsonParser\FileJsonParser;
use abimek\JsonParser\parsers\ComponentParser;
use abimek\JsonParser\requirements\Requirement;
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

    public function testIterativeComponent(){
        ComponentParser::register(new ComponentParser("items", function ($data, &...$args){
            $args[0][] = [$data["id"] . ":" . $data["meta"] . ":" . $data["count"]];
        }));
        $thing = FileJsonParser::create(getcwd() . "/test.json", []);
        $thing->addIterativeParseComponent("items", ComponentParser::get("itemParser", $items), [new Requirement("flags.item", Requirement::TYPE_BOOL, true)]);
        $thing->onComplete(function ()use(&$items){
            $this->assertTrue(is_array($items));
        });
        $thing->execute();
    }

    public function testMainRequirementNotMetCallable(){
        $thing = FileJsonParser::create(getcwd() . "/test.json", [new Requirement("nonexistent.test")]);
        $thing->onMainRequirementNotMet(function (Requirement $requirement){
            $this->assertTrue($requirement instanceof Requirement);
        });
        $thing->execute();
    }
}