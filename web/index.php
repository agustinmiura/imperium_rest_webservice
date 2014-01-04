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
date_default_timezone_set('America/Buenos_Aires');
error_reporting(E_ALL);

define('ROOT_PATH', realpath(__DIR__.'/..'));

$loader = require_once __DIR__.'/../vendor/autoload.php';

$app = require_once(ROOT_PATH.'/myCode/RestImperium/app.php');

require_once(ROOT_PATH.'/myCode/RestImperium/controllers.php');

$app->run();

