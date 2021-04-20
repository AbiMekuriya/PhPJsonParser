<?php
declare(strict_types=1);

namespace abimek\JsonParser\parsers;

use abimek\JsonParser\requirements\Requirement;

class ComponentParser{

    /**
     * @var Requirement[]
     */
    private $identifier;

    private $callable;

    /**
     * @var ComponentParser[]
     */
    private static $parsers;

    private $params = [];

    public function __construct(string $identifier, callable $callable)
    {
        $this->identifier = $identifier;
        $this->callable = $callable;
    }

    public static function register(ComponentParser $parser){
        self::$parsers[$parser->getIdentifier()] = $parser;
    }

    public function setParams(&...$params){
        $this->params = &$params;
    }

    public static function get(string $identifier, &...$args): ?ComponentParser{
        $c = clone self::$parsers[$identifier];
        $c->setParams(...$args);
        return $c;
    }

    public function getIdentifier(): string {
        return $this->identifier;
    }

    public function getCallable(): callable {
        return $this->callable;
    }

    public function &getParams(): array {
        return $this->params;
    }

    public function parse(array $data, &...$args){
        $value = null;
        $callable = $this->callable;
        $callable($data, ...$args);
    }

    public function meetsParseRequirements(array $jsonArray, array $parseRequirements): bool {
        if ($parseRequirements === null)
            return true;
        foreach ($parseRequirements as $requirement){
            if (!$requirement->meetsRequirements($jsonArray)){
                return false;
            }
        }
        return true;
    }
}