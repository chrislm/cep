<?php

namespace Respect\Relational\Schemas;

use PDOStatement;
use SplObjectStorage;
use Respect\Relational\Finder;

class FullyTyped extends Typed 
{

    protected $decorated;
    protected $namespace = '\\';

    public function setColumnValue(&$entity, $column, $value) 
    {
        $entity->{"set$column"}($value);
    }
    
    public function getColumnValue(&$entity, $column) 
    {
        return $entity->{"get$column"}();
    }
    
    
    public function extractColumns($entity, $name)
    {
        $cols = array();

        foreach (get_class_methods($entity) as $c)
            if (0 === stripos($c, 'get'))
                $cols[$n = substr($c, 3)] = $this->getColumnValue($entity, $n);
            
        return $cols;
    }
    
    public function fetchHydrated(Finder $finder, PDOStatement $statement)
    {
        $untyped = $this->decorated->fetchHydrated($finder, $statement);
        if (!$untyped)
            return $untyped;

        $map = new SplObjectStorage();
        $typed = new SplObjectStorage();
        foreach ($untyped as $e) {
            $className = $this->namespace . '\\' . static::normalize($untyped[$e]['name']);
            $newEntity = new $className;
            $map[$e] = $newEntity;
        }
        foreach ($untyped as $e) {
            foreach ($untyped[$e]['cols'] as $name => $value)
                if (is_object($e->{$name}))
                    $map[$e]->{"set$name"}($map[$e->{$name}]);
                else
                    $map[$e]->{"set$name"}(&$e->{$name});
            $typed[$map[$e]] = $untyped[$e];
        }
        return $typed;
    }

}

/**
 * LICENSE
 *
 * Copyright (c) 2009-2011, Alexandre Gomes Gaigalas.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 *     * Redistributions of source code must retain the above copyright notice,
 *       this list of conditions and the following disclaimer.
 *
 *     * Redistributions in binary form must reproduce the above copyright notice,
 *       this list of conditions and the following disclaimer in the documentation
 *       and/or other materials provided with the distribution.
 *
 *     * Neither the name of Alexandre Gomes Gaigalas nor the names of its
 *       contributors may be used to endorse or promote products derived from this
 *       software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
 * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
 * ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 */