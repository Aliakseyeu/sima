<?php
/**
 * Created by PhpStorm.
 * User: Olga
 * Date: 02.11.2018
 * Time: 14:59
 */

namespace App\Objects;


class ExceptionMessages
{

    protected $name = '';
    protected $key = 'try-again';
    protected $type = '';

    public function set(string $name, string $key, string $type): self
    {
        $this->name = $name;
        $this->key = $key;
        $this->type = $type;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function nameFirst()
    {
        
    }

}