<?php
/**
 * Copyright 2013 Agustín Miura <"agustin.miura@gmail.com">
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */
namespace RestImperium\Helper;

use Doctrine\DBAL\Logging\SQLLogger as DoctrineSqlLogger;

class SqlLogger implements DoctrineSqlLogger
{
    protected $logger;

    protected $startTime;

    public function __construct()
    {
        $this->logger = \Logger::getRootLogger();
    }

    /**
     * Logs a SQL statement somewhere.
     *
     * @param string $sql The SQL to be executed.
     * @param array $params The SQL parameters.
     * @param array $types The SQL parameter types.
     * @return void
     */
    public function startQuery($sql, array $params = null, array $types = null)
    {
        $this->startTime = time();
        $logger = $this->logger;

        $asString = ($sql!=null) ? (print_r($sql, true)) : ' sql : NULL ';
        $logger->debug('Query to execute:'.$asString);

        $asString = print_r($params, true);
        $logger->debug('Params :'.$asString);

        $asString = print_r($types, true);
        $logger->debug('Types :'.$asString);
    }

    /**
     * Mark the last started query as stopped. This can be used for timing of queries.
     *
     * @return void
     */
    public function stopQuery()
    {
        $endTime = time();
        $length = $endTime - $this->startTime;

        $this->logger->debug('Query took :'.$length.' seconds');
    }
}
