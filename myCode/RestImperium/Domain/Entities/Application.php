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
use RestImperium\Domain\Entities\Subject as Subject;
use Doctrine\Common\Collections\ArrayCollection as ArrayCollection;
use stdClass as stdClass;

class Application
{

    /**
     *
     * @var int
     */
    private $id;

    /**
     *
     * @var String
     */
    private $name;

    /**
     *
     * @var String
     */
    private $description;

    /**
     *
     * @var Doctrine\Common\Collections\ArrayCollection
     */
    private $subjects;

    public function __construct($id, $name, $description)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;

        $this->subjects = new ArrayCollection();
    }

    public function setSubjects(ArrayCollection $subjects)
    {
        $this->subjects = $subjects;
    }

    /**
     *
     * @param \RestImperium\Domain\Entities\Subject $subject
     */
    public function addSubject(Subject $subject)
    {
        $this->subjects->add($subject);
    }

    /**
     * @return Doctrine\Common\Collections\ArrayCollection
     */
    public function getSubjects()
    {
        return ($this->subjects);
    }

    public function getSubjectWithCb($criteriaCb) 
    {
        $isSubjectCb = function(Subject $subject) use ($criteriaCb)
        {
            return ($criteriaCb($subject));
        };

        $subjects = $this->subjects;
        $collection = $subjects->filter($isSubjectCb);        
        $answer = null;
        if ($collection->count()>=1) {
            $answer = $collection->first();
        }
        return $answer;
    }

    private function _getSubjectArray()
    {
        $answer = array();

        $iterator = $this->subjects->getIterator();
        $eachSubject;
        $eachArray;
        while ($iterator->valid()) {
            $eachSubject = $iterator->current();
            $eachArray = $eachSubject->toArray();
            $iterator->next();
            $answer[] = $eachArray;
        }

        return $answer;
    }

    public function toArray()
    {

        return array(
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'subjects' => $this->_getSubjectArray()
        );
    }

    public static function createFromStdClass(stdClass $data)
    {   
        $id = $data->id;
        $name = $data->name;
        $description = $data->description;
        $entity = new Application($id, $name, $description);

        $subjectCollection = new ArrayCollection();
        $subjectsInformation = $data->subjects;

        $eachSubjectInformation;

        $id;
        $name;
        $subjectEntity;
        foreach ($subjectsInformation as $eachSubjectInformation) {
            $subjectEntity = Subject::parseFromStdClass($eachSubjectInformation);
            $subjectCollection->add($subjectEntity);
        }
        $entity->setSubjects($subjectCollection);

        return $entity;
    }

    public function __toString()
    {
        $string = 'Application (id:%s,name:%s,description:%s)';
        return sprintf($string, $this->id, $this->name, $this->description);
    }

}
