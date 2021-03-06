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
namespace RestImperium\Sdk;

class SdkException extends Exception
{
    protected $result;

    const CODE_APPLICATION_NOT_FOUND = 1;

    public function __construct($result) 
    {
        $this->result = $result;
        $code = $result['code'];
        $message = $result['errorMessage'];

        parent::__construct($message, $code); 
    }

    public function getResult()
    {
        return $this->result;
    }
}
