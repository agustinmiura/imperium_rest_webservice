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
use Silex\Application;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\SessionServiceProvider as SessionServiceProvider;
use Silex\Provider\DoctrineServiceProvider as DoctrineServiceProvider;
use Silex\Provider\MonologServiceProvider as MonologProvider;
use Silex\Provider\ValidatorServiceProvider as ValidatorProvider;
use Monolog\Logger as MonologLogger;
use Symfony\Component\HttpFoundation\Request as Request;
use RestImperium\Helper\DoctrineConfigurator;
use RestImperium\Domain\Service\ApplicationService as ApplicationService;

/**
 * Setup Log4php
 */
include_once ROOT_PATH . '/vendor/apache/log4php/src/main/php/Logger.php';
\Logger::configure(ROOT_PATH . '/config/log4php.ini');

$app = new Application();

$config = parse_ini_file(ROOT_PATH . '/config/parameters.ini');
$app['config'] = $config;

$inDebug = (isset($config['application.debug'])) ?
    (intval($config['application.debug'])===1) : false;

if ($inDebug===true) {
    $app['debug'] = true;
}

$app->register(new UrlGeneratorServiceProvider());
$app->register(new ValidatorServiceProvider());
$app->register(new ServiceControllerServiceProvider());
$app->register(new SessionServiceProvider());

$monologLevel = ($inDebug===true) ? (MonologLogger::DEBUG) 
    : (MonologLogger::ERROR);
$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => $config['monolog.file'],
    'monolog.level' => $monologLevel
));

$app->register(new ValidatorProvider());

$app->register(new DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver' => 'pdo_mysql',
        'dbname' => $config['dbName'],
        'host' => $config['dbUrl'],
        'user' => $config['dbUser'],
        'password' => $config['dbPassword'],
        'charset' => 'UTF8',
        'port' => $config['dbPort']
    )
));

$app['doctrine.orm.em'] = $app->share(function() use ($app) {
    $doctrineConfigurator = new DoctrineConfigurator();
    $em = $doctrineConfigurator->createEm($app['config']);
    return $em;
});

$app['service.application'] = $app->share(function() use ($app) {
    $service = new ApplicationService($app);
    return $service;
});

return $app;
