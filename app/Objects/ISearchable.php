<?php
/**
 * Created by PhpStorm.
 * User: Olga
 * Date: 05.10.2018
 * Time: 20:18
 */

namespace App\Objects;


interface ISearchable
{

    public function find(int $id);

    public function where(string $column, string $value);

}