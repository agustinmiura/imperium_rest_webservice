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

use RestImperium\Rest\RestClient as RestClient;

abstract class BaseSdk
{
    protected $applicationId;
    protected $applicationKey;
    protected $imperiumRestUrl;

    protected $restClient;

    private function _getErrors($config)
    {
        $answer = array();
        if (isset($config['applicationId'])===false) {
            $answer = array(
                'applicationId'=>'Not set the application id'
            );
        } else if ( (isset($config['applicationKey'])) === false ) {
            $answer = array(
                'applicationKey'=>'Not set application key'
            );
        } else if ( (isset($config['imperiumRestUrl']))===false ) {
            $answer = array(
                'imperiumRestUrl'=>'Not set imperiumRestUrl'
            );
        }
        return $answer;
    }
 
    private function _subConstructor($config) 
    {
        $this->applicationId = $config['applicationId'];
        $this->applicationKey = $config['applicationKey'];
        $this->imperiumRestUrl = $config['imperiumRestUrl'];
        $this->restClient = new RestClient();
    }

    public function __construct($config) 
    {
        if (function_exists('curl_init')===false) {
            throw \RuntimeException('Imperium needs the CURL PHP extension');
        } else if (function_exists('json_decode')===false) {
            throw new \RuntimeException('Imperium needs the JSON PHP extension.');
        }

        $errors = $this->_getErrors($config);
        if (empty($errors)===false) {
            throw \RuntimeException('Invalid config parameters');
        }else{
            $this->_subConstructor($config);
        }
    }

    protected function getDomainMap() 
    {
        return array(
            'information'=>'/api/application/get'
        );
    }
    public abstract function isGranted($role, $resource, $action); 
}
