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
namespace RestImperium\Domain\Service;

use RestImperium\Domain\Service\AbstractService as AbstractService;
use RestImperium\Domain\Service\IApplicationService as IApplicationService;

use RestImperium\Domain\Entities\Application as Application;
use RestImperium\Domain\Entities\Role as Role;
use RestImperium\Domain\Entities\Subject as Subject;
use RestImperium\Domain\Entities\Permission as Permission;
use Doctrine\Common\Collections\ArrayCollection as ArrayCollection;

use Doctrine\ORM\Query\ResultSetMapping;

class ApplicationService extends AbstractService implements IApplicationService
{
    public function getApplicationById($id)
    {
        $app = $this->getContainer();

        $sql = 'SELECT
                    application.id id,
                    application.name name,
                    application.description description
                FROM im_application application
                WHERE id = ?
        ';

        $result = $app['db']->fetchAssoc($sql,array($id));

        $answer = null;
        if (is_array($result)===true) {
            $id = intval($result['id']);
            $answer = new Application(
                $id,
                $result['name'],
                $result['description']
            );
        }

        return $answer;
    }

    public function getApplicationBy($id, $key)
    {
        $container = $this->getContainer();
        $sql = '
            SELECT * FROM im_application WHERE id=?
            AND apiKey=? LIMIT 0,1
        ';
        $result = $container['db']->fetchAssoc($sql, array($id, $key));
        $answer = null;
        if (is_array($result)===true) {
            $answer = array(
                'id'=>$result['id'],
                'apiKey'=>$result['apiKey'],
                'description'=>$result['description'],
                'name'=>$result['name']
            );
        }
        return $answer;
    }


    public function getInformation($id)
    {
        /*
        @todo remove
        */
        $logger = \Logger::getLogger('MyLogger');
        //$logger->debug('Get information id:'.$id);
        /*
        */

        $applicationEntity = $this->getApplicationById($id);

        if ($applicationEntity===null) {
            throw new \RuntimeException(
                'Cannot found the application with id:'.$id
            );
        }

        $subjects = $this->_getAllSubjects($id);
        $subjectIterator=$subjects->getIterator();
        $eachSubject = null;
        $eachRole = null;
        $roleCollection = null;
        $permissionCollection = null;
        $roleIterator = null;
        while($subjectIterator->valid()) {
            $eachSubject = $subjectIterator->current();
            $subjectIterator->next();

            $roleCollection = $this->getAllRoles($eachSubject->getName());
            $roleIterator = $roleCollection->getIterator();
            while($roleIterator->valid()) {
                $eachRole = $roleIterator->current();

                $permissionCollection = $this->_getAllPermissionsFor(
                    $eachRole->getId()
                );
                $eachRole->setPermissions($permissionCollection);

                $roleIterator->next();
            }
            $eachSubject->setRoles($roleCollection);
        }

        $applicationEntity->setSubjects($subjects);

        /*
        @todo remove
        */
        $asString = print_r($applicationEntity, true);
        //$logger->debug('The application is :'.$asString);

        return $applicationEntity;
    }

    /**
     * For application name
     * @param  [type] $name [description]
     * @return  array[subjectId]=array(
     *              'id'=>$subjectId,
     *              'name'=>$subjectName,
     *              'roles'=>$subjectRoles
     *         )
     *         $subjectRoles = array();
     *         where $subjectRoles[id] = array(
     *             'id'=>$id,
     *             'name'=>$name,
     *             'permissions'=>$permissions
     *         )
     *         $permissions = array()
     *         $permissions[id] = array(
     *             'id'=>$id,
     *             'resource'=>$name,
     *             'action'=>$action
     *         )
     */
    public function _getInformation($id)
    {
        $app = $this->getContainer();

        $sql = 'SELECT
                    application.id id,
                    application.name name
                FROM im_application application
                WHERE id = ?
        ';

        $result = $app['db']->fetchAssoc($sql,array($id));

        if (empty($result)) {
            throw new \RuntimeException(
                'Cannot found the application with id:'.$id
            );
        }

        $applicationId = $result['id'];
        $applicationName = $result['name'];

        //get all permissions for the application
        //$permissions = $this->getAllPermissions($applicationId);
        $subjects = $this->getAllSubjects($applicationId);

        $subjectInformations = array();
        $eachSubjectInformation;
        $eachPermissions;

        $eachSubjectId;
        $eachSubjectName;
        $eachRoles;
        $eachRoleId;
        /**
         * process the subject information
         */
        foreach ($subjects as $eachSubject) {
            $eachSubjectId = $eachSubject['id'];
            $eachSubjectName = $eachSubject['name'];

            $eachRoles = array();
            $eachRoles = $this->getAllRoles($eachSubjectName);

            foreach ($eachRoles as $roleInformation) {
                $eachRoleId = $roleInformation['id'];
                $eachPermissions = $this->_getAllPermissionsFor($eachRoleId);
                $roleInformation['permissions'] = $eachPermissions;
                $eachRoles[] = $roleInformation;
            }

            /**
             * Prepare the data
             */
            $eachSubjectInformation = array(
                'id'=>$eachSubjectId,
                'name'=>$eachSubjectName,
                'roles'=>$eachRoles
            );

            $subjectInformations[] = $eachSubjectInformation;
        }

        $result['subjects'] = $subjectInformations;

        return $result;
    }
    /**
     * Functions OK
     *
     * Get all subjects for application id
     * @param  [type] $applicationId [description]
     * @return [type]                [description]
     */
    private function _getAllSubjects($applicationId)
    {
        $container = $this->getContainer();
        $sql = '
            SELECT subject.id id, subject.name name
                FROM im_application application
                JOIN
                im_subject subject on application.id=subject.application_id
                WHERE application.id=?
        ';
        $result = $container['db']->fetchAll($sql, array($applicationId));
        $answer = new ArrayCollection();

        $eachSubject;
        $eachId;
        $eachName;
        foreach($result as $eachResult) {
            $eachId = $eachResult['id'];
            $eachName = $eachResult['name'];

            $eachSubject = new Subject($eachId, $eachName);

            $answer->add($eachSubject);
        }
        return $answer;
    }

    /**
     * Testing
     *
     * Get all roles for each subject
     * @param  [type] $roleId [description]
     * @return [type]         [description]
     */
    public function getAllRoles($subjectName)
    {
        $container = $this->getContainer();
        $sql = '
            SELECT role.id id , role.name role
                FROM
                im_subject subject JOIN im_subject_role subject_role
                ON subject.id = subject_role.subject_id
                JOIN im_role role ON
                role.id = subject_role.role_id
                WHERE subject.name = ?
        ';
        $result = $container['db']->fetchAll($sql, array($subjectName));

        $answer = new ArrayCollection();
        $eachId;
        $eachName;
        $eachEntity;
        foreach($result as $eachResult)
        {
            $eachId = $eachResult['id'];
            $eachName = $eachResult['role'];

            $eachEntity = new Role($eachId, $eachName);
            $answer->add($eachEntity);
        }
        return $answer;
    }

    private function _getAllPermissionsFor($roleId)
    {
        $container = $this->getContainer();

        $sql = '
            SELECT
                    role.id roleId,
                    permission.id id,
                    permission.resource resource,
                    permission.action resourceAction
            FROM im_role role
            JOIN im_permission_role permission_role
                ON role.id = permission_role.role_id
            JOIN im_permission permission
                ON permission.id = permission_role.permission_id
            WHERE role.id=?
        ';

        $dbResult = $container['db']->fetchAll($sql, array($roleId));

        $answer = new ArrayCollection();

        $eachPermission;
        foreach ($dbResult as $eachDbResult) {
            $eachPermission = new Permission(
                $eachDbResult['id'],
                $eachDbResult['resource'],
                $eachDbResult['resourceAction']
            );
            $answer->add($eachPermission);
        }
        return $answer;
    }
}
