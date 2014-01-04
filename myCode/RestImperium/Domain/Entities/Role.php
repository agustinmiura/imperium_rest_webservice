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
use Doctrine\Common\Collections\ArrayCollection as ArrayCollection;

class Role
{
    private $id;
    private $name;
    private $permissions;

    public function __construct($id, $name)
    {
        $this->id = $id;
        $this->name = $name;
        $this->permissions = new ArrayCollection();
    }

    public function getName()
    {
        return ($this->name);
    }

    public function getId()
    {
        return $this->id;
    }

    public function setPermissions(ArrayCollection $permissions)
    {
        $this->permissions = $permissions;
    }

    public function getPermissionByNameAction($resource, $action)
    {
        $toFind = new Permission(-1, $resource, $action);

        $findByNameActionCb = function(Permission $permission) use ($toFind) {
            $compareResult = $toFind->compareByResourceAction($permission);
            return ($compareResult===true);
        };

        $permissionCollection = $this->permissions->filter(
            $findByNameActionCb
        );
        $answer = null;
        if ($permissionCollection->count()>=1) {
            $answer = $permissionCollection->first();
        }

        return $answer;
    }

    private function _getPermissionArray()
    {
        $answer = array();

        $permissions = $this->permissions;
        $iterator = $permissions->getIterator();
        $eachPermission;
        while($iterator->valid()) {
            $eachPermission = $iterator->current();
            $answer[] = ($eachPermission->toArray());
            $iterator->next();
        }

        return $answer;
    }

    public function toArray()
    {
        return array(
            'id'=>$this->id,
            'name'=>$this->name,
            'permissions'=>$this->_getPermissionArray()
        );
    }

    public static function createFromStdClass(\stdClass $data)
    {
        $id = $data->id;
        $name = $data->name;

        $entity = new Role($id, $name);

        $permissions = new ArrayCollection();
        $permissionsInfo = $data->permissions;
        $permissionEntity;
        foreach ($permissionsInfo as $eachPermissionInfo) {
            $permissionEntity = Permission::createFromStdClass(
                $eachPermissionInfo
            );
            $permissions->add($permissionEntity);
        }
        $entity->setPermissions($permissions);

        return $entity;
    }
}
