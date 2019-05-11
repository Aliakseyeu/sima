<?php
/**
 * Created by PhpStorm.
 * User: Andrei
 * Date: 30.10.18
 * Time: 7:12
 */

namespace App\Objects\Response;


interface IResponsable
{

    public function setError(bool $error = true): void;

    public function isError(): bool;

    public function addMessage(string $message): void;

    public function getMessages(): array;

    public function addSuccess(string $message): void;

    public function addError(string $message): void;

    public function getSuccess(): array;

    public function getErrors(): array;

}