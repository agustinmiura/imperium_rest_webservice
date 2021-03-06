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
namespace RestImperium\Helper;

use Doctrine\ORM\EntityManager as EntityManager;
use Doctrine\ORM\Configuration as Configuration;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;

use RestImperium\Helper\SqlLogger as SqlLogger;

class DoctrineConfigurator
{
    public function __construct()
    {

    }

    public function createEmForProduction($dbConfig)
    {
        $classLoader = new \Doctrine\Common\ClassLoader('Doctrine');
        $classLoader->register();

        $classLoader = new \Doctrine\Common\ClassLoader('Entities', ROOT_PATH.'/myCode/RestImperium/Domain/Entities');
        $classLoader->register();
        $classLoader = new \Doctrine\Common\ClassLoader('Proxies', ROOT_PATH.'/runtime/proxy');
        $classLoader->register();


        $config = new \Doctrine\ORM\Configuration();
        $config->setProxyDir(ROOT_PATH.'/runtime/proxy');
        $config->setProxyNamespace('Proxies');

        $config->setAutoGenerateProxyClasses(false);

        $paths = array(
            ROOT_PATH.'/myCode/RestImperium/Domain/Entities'
        );
        $driverImpl = $config->newDefaultAnnotationDriver($paths);
        $config->setMetadataDriverImpl($driverImpl);
        $cache = new \Doctrine\Common\Cache\ApcCache();
        $config->setMetadataCacheImpl($cache);
        $config->setQueryCacheImpl($cache);

        $connectionOptions = array(
            'driver'   => 'pdo_mysql',
            'host'     => $dbConfig['dbUrl'],
            'dbname'   => $dbConfig['dbName'],
            'user'     => $dbConfig['dbUser'],
            'port'     => $dbConfig['dbPort'],
            'password' => $dbConfig['dbPassword'],
            'charset'  => 'UTF8'
        );

        $em = \Doctrine\ORM\EntityManager::create($connectionOptions, $config);
        $platform = $em->getConnection()->getDatabasePlatform();
        $platform->registerDoctrineTypeMapping('enum', 'string');

        $helperSet = new \Symfony\Component\Console\Helper\HelperSet(array(
             'db' => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($em->getConnection()),
             'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($em)
        ));

        return $em;
    }

    public function createEmForDevelopment($dbConfig)
    {
        return $this->createEm($dbConfig);
    }

    public function createEm($dbConfig)
    {
        $classLoader = new \Doctrine\Common\ClassLoader('Doctrine');
        $classLoader->register();

        $classLoader = new \Doctrine\Common\ClassLoader('Entities', ROOT_PATH.'/myCode/RestImperium/Domain/Entities');
        $classLoader->register();
        $classLoader = new \Doctrine\Common\ClassLoader('Proxies', ROOT_PATH.'/runtime/proxy');
        $classLoader->register();


        $config = new \Doctrine\ORM\Configuration();
        $config->setProxyDir(ROOT_PATH.'/runtime/proxy');
        $config->setProxyNamespace('Proxies');

        $config->setAutoGenerateProxyClasses(true);

        $paths = array(
            ROOT_PATH.'/myCode/RestImperium/Domain/Entities'
        );
        $driverImpl = $config->newDefaultAnnotationDriver($paths);
        $config->setMetadataDriverImpl($driverImpl);
        $cache = new \Doctrine\Common\Cache\ArrayCache();
        $config->setMetadataCacheImpl($cache);
        $config->setQueryCacheImpl($cache);

        $logQueries = (isset($dbConfig['doctrine.debug.sql']))
        ? ($dbConfig['doctrine.debug.sql']==1) : false;
        if ($logQueries) {
            $config->setSQLLogger(new SqlLogger());
        }

        $connectionOptions = array(
            'driver'   => 'pdo_mysql',
            'host'     => $dbConfig['dbUrl'],
            'dbname'   => $dbConfig['dbName'],
            'user'     => $dbConfig['dbUser'],
            'port'     => $dbConfig['dbPort'],
            'password' => $dbConfig['dbPassword'],
            'charset'  => 'UTF8'
        );

        $em = \Doctrine\ORM\EntityManager::create($connectionOptions, $config);
        $platform = $em->getConnection()->getDatabasePlatform();
        $platform->registerDoctrineTypeMapping('enum', 'string');

        $helperSet = new \Symfony\Component\Console\Helper\HelperSet(array(
             'db' => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($em->getConnection()),
             'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($em)
        ));

        return $em;
    }

    public function createHelperSet($app) {
        $em = $this->createEm($app);
        $platform = $em->getConnection()->getDatabasePlatform();
        $platform->registerDoctrineTypeMapping('enum', 'string');

        $helperSet = new \Symfony\Component\Console\Helper\HelperSet(array(
             'db' => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($em->getConnection()),
             'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($em)
        ));
        return $helperSet;
    }

}
