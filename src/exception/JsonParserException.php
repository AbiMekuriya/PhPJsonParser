<?php
declare(strict_types=1);

namespace abimek\JsonParser\exception;

use Exception;
use RuntimeException;

class JsonParserException extends RuntimeException{

    /**
     * JsonParserException
     *
     * @param string $message
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Activated when casting to string
     */
    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}