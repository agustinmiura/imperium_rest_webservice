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
use Symfony\Component\HttpFoundation\Request as Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use RestImperium\Web\ApiController as ApiController;

$app["controller.api"] = $app->share(function() use ($app) {
    $controller = new ApiController($app);
    return $controller;
});

$app->error(function (\Exception $e, $code) use ($app) {
    $logger = \Logger::getRootLogger();
    $logger->debug(PHP_EOL . "<---------------------------------------->");
    $logger->debug(PHP_EOL . "Exception happened");
    $logger->debug(PHP_EOL . "Code:" . $code);
    $logger->debug(PHP_EOL . "Message:" . $e->getMessage());
    $logger->debug(PHP_EOL . "<---------------------------------------->");
    $logger->debug(PHP_EOL . "Stack trace" . $e->getTraceAsString());
    $logger->debug(PHP_EOL . "<---------------------------------------->");

    $page = 404 == $code ? '404.html' : '500.html';

    return $app->json(array(
        'success'=>false,
        'code'=>$code,
        'message'=>$e->getMessage(),
        'trace'=>$e->getTraceAsString()
    ));
});

$app->get('/api/application/get', function() use ($app) {
    $request = $app['request'];
    $applicationService = $app['service.application'];

    /**
     * Get application id and private key
     */
    $headers = $request->headers;
    $applicationId = $headers->get('application-id', -1);
    $applicationKey = $headers->get('application-key', 'key');

    $application = $applicationService->getApplicationBy(
        $applicationId,
        $applicationKey
    );

    if ($application===null) {
        throw new \RuntimeException('Application not found');
    }

    $applicationEntity =  $applicationService->getInformation($applicationId);
    $appArray = $applicationEntity->toArray();

    return $app->json(array(
        'success'=>true,
        'information'=>$appArray
    ));
});

