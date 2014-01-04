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

use RestImperium\Sdk\BaseSdk as BaseSdk;
use RestImperium\Domain\Entities\Application as Application;
use Doctrine\Common\Collections\ArrayCollection as ArrayCollection;
use RestImperium\Domain\Entities\Subject as Subject;
use RestImperium\Domain\Entities\Role as Role;
use RestImperium\Domain\Entities\Permission as Permission;
use stdClass as StdClass;

class Imperium extends BaseSdk
{
    /**
     *
     * @var RestImperium\Domain\Entities\Application
     */
    protected $application;

    public function setApplication(Application $application)
    {
        $this->application = $application;
    }

    public function getApplication()
    {
        return $this->application;
    }

    private function _getHeaders()
    {
        return array(
            'application_id' => $this->applicationId,
            'application_key' => $this->applicationKey
        );
    }

    /**
     * Get request parameters
     */
    private function _getRequestParameters()
    {
        return array(
        );
    }

    public function init()
    {
        $application = $this->_getInformation();
        $this->application = $application;
    }

    /**
     * Whan called do the request to
     * the webservice and parse the information
     *
     * @return type
     * @throws \RuntimeException
     */
    private function _getInformation()
    {
        $restClient = $this->restClient;
        $domainMap = $this->getDomainMap();
        $url = $this->imperiumRestUrl . $domainMap['information'];

        $headers = $this->_getHeaders();
        $requestParameters = $this->_getRequestParameters();

        $answer = $restClient->consumeGet(
                $url, $headers, $requestParameters
        );

        if ($answer['success'] !== true) {
            throw new \RuntimeException('An exception happened');
        }

        $jsonString = $answer['data'];
        $rawData = $this->_jsonDecode($jsonString, false);
        $appInfo = $rawData->information;

        return Application::createFromStdClass($appInfo);
    }

    private function _jsonDecode($jsonString, $fetchAsArray)
    {
        $answer = json_decode($jsonString, $fetchAsArray);
        if ($answer === null) {
            throw new \RuntimeException('Cannot decode the json string or
            the recursion is deeper than the max level. Using json
            content:'.$jsonString);
        } else {
            return $answer;
        }
    }

    public function isGranted($role, $resource, $action)
    {
        $application = $this->application;

        $cb = function(Subject $subject) use ($role) {
            $roleFound = $subject->getRoleByName($role);
            return ($roleFound!==null);
        };

        $subjectFound = $application->getSubjectWithCb($cb);

        $permission = null;
        if ($subjectFound!==null) {
            $roleEntitiy = $subjectFound->getRoleByName($role);

            $permission = $roleEntitiy->getPermissionByNameAction(
                $resource,
                $action
            );
        }
        return ($permission!==null);
    }

}

