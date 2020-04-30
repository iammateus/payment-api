<?php

namespace App\Utils\Tests;

trait EntityCreatable
{
    /**
     * Generate an entity from a given model using its factory
     *
     * @param [type] $model
     * @param array $data
     * @return object
     */
    public function createEntity($model, array $data = []): object
    {
        $entity = factory($model)->make($data);
        $entity->save();
        return $entity;
    } 
}