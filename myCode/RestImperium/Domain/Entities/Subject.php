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
use RestImperium\Domain\Entities\Role as Role;
use Doctrine\Common\Collections\ArrayCollection as ArrayCollection;
use stdClass as stdClass;

class Subject
{

    /**
     *
     * @var int
     */
    private $id;

    /**
     *
     * @var string
     */
    private $name;

    /**
     *
     * @var Doctrine\Common\Collections\ArrayCollection
     */
    private $roles;

    public function __construct($id, $name)
    {
        $this->id = $id;
        $this->name = $name;
        $this->roles = new ArrayCollection();
    }

    public function getName()
    {
        return $this->name;
    }

    public function addRole(Role $role)
    {
        $roleCollection = $this->roles;
        $roleCollection->add($role);
    }

    public function setRoles(ArrayCollection $roles)
    {
        $this->roles = $roles;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    private function _getRolesAsArray()
    {
        $roles = $this->roles;
        $answer = array();

        $iterator = $roles->getIterator();
        $current;
        while($iterator->valid()) {
            $current = $iterator->current();
            $answer[] = ($current->toArray());
            $iterator->next();
        }

        return $answer;
    }

    public function toArray()
    {
        return array(
            'id'=>$this->id,
            'name'=>$this->name,
            'roles'=>$this->_getRolesAsArray()
        );
    }

    public function getRoleByName($name)
    {
        $roleArray = $this->roles;
        $iterator = $roleArray->getIterator();
        $eachName;
        $answer = null;
        foreach ($roleArray as $eachRole) {
            $eachName = $eachRole->getName();
            if (strcasecmp($eachName, $name)===0) {
                $answer =  $eachRole;
                break;
            }
        }
        return $answer;
    }

    public static function parseFromStdClass(stdClass $data)
    {
        $id = $data->id;
        $name = $data->name;

        $entity = new Subject($id, $name);
        $roles = new ArrayCollection();
        $roleInfo = $data->roles;
        $roleEntity;
        foreach ($roleInfo as $eachRole) {
            $roleEntity = Role::createFromStdClass($eachRole);
            $roles->add($roleEntity);
        } 
        $entity->setRoles($roles);

        return $entity;
    }

}
