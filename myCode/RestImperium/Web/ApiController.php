<?php
namespace RestImperium\Web;

use Silex\Application as Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use RestImperium\Web\AbstractController as AbstractController;

class ApiController extends AbstractController
{
    public function main()
    {
        /**
        *@todo remove
        */
        $logger=\Logger::getLogger('myLogger');
        $logger->debug('Started main method');
        $logger->debug('main method executed');
        /*
        *
        */

        $app = $this->getApplication();
        $request = $app['request'];
        $headerBag = $request->headers;

        $applicationKey = $headerBag->get('application-key', '');
        $applicationName = $headerBag->get('application-name', '');

        $applicationService = $app['service.application'];
        $applicationFound = $applicationService->getApplicationBy(
            $applicationKey,
            $applicationName
        );

        if ($applicationFound===null) {
            throw new \RuntimeException('Application not found : ');
        }

        $information = $applicationService->getInformation($applicationName);


        $answer = array(
            'success'=>true,
            'msg'=>'Hello world',
            'application-key'=>$headerBag->get('application-key', ''),
            'application-password'=>$headerBag->get('application-name', '')
        );

        return $app->json($answer);
    }
}
/*
2013-05-20T20:17:57-03:00 [DEBUG] myLogger: The params are : Array
(
    [applicationkey] => Array
        (
            [0] => key
        )

    [applicationname] => Array
        (
            [0] => sample
        )

    [accept] => Array
        (
            [0] =>
        )

    [host] => Array
        (
            [0] => imperium.rest:9030
        )

)
*/