<?php
declare(strict_types=1);

namespace JsonParser;

use JsonParser\exception\JsonParserException;
use JsonParser\parsers\ComponentParser;
use JsonParser\requirements\Requirement;

class FileJsonParser{
    
    public const TYPE_NORMAL = 0;
    public const TYPE_ITERATIVE = 1;

    /**
     * @var array
     */
    private $fileData;

    /**
     * @var Requirement[]
     */
    private $requirements;

    private $completionCallables = [];
    private $mainRequirementNotMetCallable;

    private $componentParsers = [];

    private function __construct(string $directory, array $requirements = [])
    {
        $this->fileData = json_decode(file_get_contents($directory), true);
        $this->requirements = $requirements;
    }

    public static function create(string $directory, array $requirements): FileJsonParser{
        return new FileJsonParser($directory, $requirements);
    }

    public function onMainRequirementNotMet(callable $onMainRequirementNotMet){
        $this->mainRequirementNotMetCallable = $onMainRequirementNotMet;
    }

    public function onComplete(callable $completeCallable){
        $this->completionCallables[] = $completeCallable;
    }

    public function addParseComponent(ComponentParser $parser, array $requirements = [], string $location = ""){

        $this->componentParsers[] = [self::TYPE_NORMAL, $parser, $requirements, $location];
    }

    public function addIterativeParseComponent(string $location, ComponentParser $parser, array $requirements = []){
        $this->componentParsers[] = [self::TYPE_ITERATIVE, $parser, $requirements, $location];
    }

    public function execute(){
        if (!$this->meetsRequirements()){
            if (is_callable($this->mainRequirementNotMetCallable)){
                $c = $this->mainRequirementNotMetCallable;
                $c($this->getUnmetRequirement());
            }
            return;
        }
        foreach ($this->componentParsers as $parseData){
            $type = $parseData[0];
            $parser = $parseData[1];
            assert($parser instanceof ComponentParser);
            $requirements = $parseData[2];
            if (!$parser->meetsParseRequirements($this->fileData, $requirements)){
                continue;
            }
            $data = $this->getDataForLocation($parseData[3]);
            if ($type === self::TYPE_NORMAL){
                $parser->parse($data, ...$parser->getParams());
                continue;
            }
            if ($type === self::TYPE_ITERATIVE && is_array($data)){
                foreach ($data as $datum){
                    $parser->parse($datum, ...$parser->getParams());
                }
                continue;
            }
        }
        foreach ($this->completionCallables as $completionCallable){
            if ($completionCallable !== null){
                $completionCallable();
            }
        }
    }

    public function getDataForLocation(string $location){
        if ($location === ""){
            return $this->fileData;
        }
        if ((new Requirement($location))->meetsRequirements($this->fileData)){
            $split = explode(".", $location);
            $data = $this->fileData;
            foreach ($split as $location){
                $data = $data[$location];
            }
            return $data;
        }
        return null;
    }

    public function meetsRequirements(): bool {
        foreach ($this->requirements as $requirement){
            if (!$requirement->meetsRequirements($this->fileData)){
                return false;
            }
        }
        return true;
    }

    public function getUnmetRequirement(): Requirement {
        foreach ($this->requirements as $requirement){
            if (!$requirement->meetsRequirements($this->fileData)){
                return $requirement;
            }
        }
        throw new JsonParserException("All requirements were met!");
    }
}