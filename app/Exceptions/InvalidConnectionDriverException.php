<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class InvalidConnectionDriverException extends Exception
{
    /**
     * Creates invalid driver connection exception
     * 
     * @param $message
     * @param $code - error code
     * 
     * @param Throwable|null - Previous exception instance
     * 
     * @return void
     */
    public function __construct(string $message, $code = 0, Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
    
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}
