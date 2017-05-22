<?php

namespace NoMoreWar\Casualties\Exceptions;

use PDOException;

/**
 * Class DatabaseException
 *
 * @package ActivismeBe\Artillery\Exceptions
 */
class DatabaseException extends PDOException
{
    /**
     * DatabaseException constructor.
     *
     * @param PDOException $exception
     */
    public function __construct(PDOException $exception)
    {
        if (strstr($exception->getMessage(), 'SQLSTATE[')) {
            preg_match('/SQLSTATE\[(\w+)\] \[(\w+)\] (.*)/', $exception->getMessage(), $matches);
            $this->code    = ($matches[1] == 'HT000' ? $matches[2] : $matches[1]);
            $this->message = $matches[3];
        }
    }
}