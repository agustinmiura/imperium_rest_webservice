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
 * Set the error reporting and the time zone
 */
date_default_timezone_set('America/Buenos_Aires');
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('ROOT_PATH', realpath(__DIR__.'/..'));

$loader = require_once __DIR__.'/../vendor/autoload.php';
$app = require_once(ROOT_PATH.'/myCode/RestImperium/app.php');

use RestImperium\Sdk\Imperium as Imperium;

$id = 100;
$config = array(
    'applicationId'=>$id,
    'applicationKey'=>'key',
    'imperiumRestUrl'=>'http://imperium.rest:9030'
);
$imperiumApiObject = new Imperium($config);
$imperiumApiObject->init();

$isGranted = $imperiumApiObject->isGranted('user', 'ownAccount', 'read');

echo 'In example.php i see:';
var_dump($isGranted);
