<?php
namespace App\Controller;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;   // modif version 2.0


class ContactController implements ControllerProviderInterface
{

    public function validFormContact(Application $app, Request $req)
    {
      //code verif contact
    }


    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        $controllers->post('/', 'App\Controller\ContactController::validFormContact')->bind('contact.validFormContact');

        return $controllers;
    }


}
