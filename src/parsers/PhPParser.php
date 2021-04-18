<?php
declare(strict_types=1);

namespace JsonParser\parsers;

use JsonParser\requirements\Requirement;

class PhPParser{

    /**
     * @var Requirement[]
     */
    private $parseRequirements = null;

    private function __construct(?array $parseRequirements = null)
    {
        $this->parseRequirements = $parseRequirements;
    }

    public function parse(array $data){

    }

    public function meetsParseRequirements(array $jsonArray): bool {
        if ($this->parseRequirements === null)
            return true;
        foreach ($this->parseRequirements as $requirement){
            if (!$requirement->meetsRequirements($jsonArray)){
                return false;
            }
        }
        return true;
    }
}