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
namespace RestImperium\Domain\Entities;

use RestImperium\Domain\Entities\Permission as Permission;

class Permission
{
    private $id;
    private $resource;
    private $action;

    public function __construct($id, $resource, $action)
    {
        $this->id = $id;
        $this->resource = $resource;
        $this->action = $action;
    }

    public function getResource()
    {
        return $this->resource;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function toArray()
    {
        return array(
            'id'=>$this->id,
            'resource'=>$this->resource,
            'action'=>$this->action
        );
    }

    public static function createFromStdClass(\stdClass $data)
    {
        $id = $data->id;
        $resource = $data->resource;
        $action = $data->action;

        return new Permission($id, $resource, $action);
    }

    public function compareByResourceAction(Permission $permission)
    {
        $otherResource = $permission->getResource();
        $otherAction = $permission->getAction();

        $sameResource = (strcasecmp($otherResource, $this->resource)===0);
        $sameAction = (strcasecmp($otherAction, $permission->getAction())===0);

        return ($sameResource&&$sameAction);

    }
}
