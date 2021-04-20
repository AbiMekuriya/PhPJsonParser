<?php
declare(strict_types=1);

namespace abimek\JsonParser\requirements;

use abimek\JsonParser\exception\JsonParserException;

class Requirement{

    public const TYPES = [self::TYPE_ANY, self::TYPE_BOOL, self::TYPE_INT, self::TYPE_STRING, self::TYPE_FLOAT, self::TYPE_ARRAY];

    public const TYPE_ANY = 0;
    public const TYPE_BOOL = 1;
    public const TYPE_INT = 2;
    public const TYPE_STRING = 3;
    public const TYPE_FLOAT = 4;
    public const TYPE_ARRAY = 5;

    /**
     * The type of value it must have
     *
     * @var int
     */
    private $valueType;

    /**
     * The requirement, in a key.key.key fashion
     *
     * @var string
     */
    private $requirement;

    private $wantedValue = null;

    public function __construct(string $requirement, ?int $type = null, $wantedValue = null)
    {
        $this->requirement = $requirement;
        $this->valueType = ($type === null) ? self::TYPE_ANY : $type;
        $this->wantedValue = $wantedValue;
        if (!in_array($type, self::TYPES)){
            throw new JsonParserException("Nonexistent type given PHP-JsonParser");
        }
    }

    public function getRequirement(): string {
        return $this->requirement;
    }

    public function meetsRequirements(array $data): bool {
        $split = explode(".", $this->requirement);
        $d = $data;
        foreach ($split as $name){
            if (isset($d[$name])){
                $d = $d[$name];
            }else{
                return false;
            }
        }
        return self::meetsTypeRequirement($this->valueType, $d, $this->wantedValue);
    }


    public static function meetsTypeRequirement(int $type, $value, $wantedValue = null): bool {
        if ($wantedValue !== null){
            return ($wantedValue === $value);
        }
        switch ($type){
            case self::TYPE_ANY:
                return true;
            case self::TYPE_BOOL:
                return is_bool($value);
            case self::TYPE_INT:
                return is_int($value);
            case self::TYPE_STRING:
                return is_string($value);
            case self::TYPE_FLOAT:
                return is_float($value);
            case self::TYPE_ARRAY:
                return is_array($value);
        }
        return false;
    }

}