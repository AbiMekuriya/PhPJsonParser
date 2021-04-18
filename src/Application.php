<?php
declare(strict_types=1);

namespace JsonParser;

class Application{


    /**
     * the array data of the file
     *
     * @var string
     */
    private $fileData;

    private static $readCallables = [];

    public function __construct(string $directory)
    {
        $this->fileData = json_decode(file_get_contents($directory), true);
    }

    public static function registerParser(){

    }

    public function parse(){

    }

}