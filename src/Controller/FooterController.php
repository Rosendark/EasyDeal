<?php
namespace App\Controller;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;   // modif version 2.0


class FooterController implements ControllerProviderInterface
{
    public function indexFaq(Application $app) {
        return $this->showFaq($app);
    }

    public function showFaq(Application $app) {
        return $app["twig"]->render('faq.html.twig');
    }

    public function indexContact(Application $app) {
        return $this->showContact($app);
    }

    public function showContact(Application $app) {
        return $app["twig"]->render('contact.html.twig');
    }



    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        $controllers->get('/faq', 'App\Controller\FooterController::indexFaq')->bind('faq.show');
        $controllers->get('/contact', 'App\Controller\FooterController::indexContact')->bind('contact.show');

        return $controllers;
    }


}
