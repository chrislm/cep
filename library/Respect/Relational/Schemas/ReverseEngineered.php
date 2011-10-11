<?php

namespace Respect\Relational\Schemas;

use PDOStatement;
use Respect\Relational\Db;

class ReverseEngineered extends AbstractExtractor
{

    protected $db;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    public function findPrimaryKey($entityName)
    {
        return $this->db
                ->select('kcu.column_name')
                ->from('information_schema.key_column_usage as kcu')
                ->innerJoin('information_schema.table_constraints as tc')
                ->on('kcu.constraint_name=tc.constraint_name')
                ->where(array(
                    'kcu.table_name' => $entityName,
                    'tc.constraint_type' => 'PRIMARY KEY'
                ))->fetch()->column_name;
    }

    public function findObjectTableName($entity)
    {
        throw new \InvalidArgumentException('ReverseEngineered Schema does not support finding table names'); //TODO
    }

    public function findRealTableName($finderName, $parentFinderName=null, $nextFinderName=null)
    {
        return $finderName;
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