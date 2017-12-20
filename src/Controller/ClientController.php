<?php
namespace App\Controller;

use App\Model\ClientModel;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;   // pour utiliser request
use App\Model\PanierModel;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Security;

use App\Model\UtilisateurModel;
use App\Model\CompteModel;

class ClientController implements ControllerProviderInterface
{
   private $clientModel;
   private $comteModel;

    public function index(Application $app) {

        return $this->showClient($app);
    }


    public function showClient(Application $app) {
        $this->clientModel=new UtilisateurModel($app);
        $clients=$this->clientModel->getAllClients();
        return $app["twig"]->render('backOff/Client/showClient.html.twig',['data'=>$clients]);
    }


    public function afficherProfil(Application $app){
        $this->clientModel=new UtilisateurModel($app);
        $this->comteModel=new CompteModel($app);
        $id=$this->comteModel->recupererId($app);
        $profil=$this->clientModel->getProfil($id);
        return $app["twig"]->render('frontOff/profilClient.html.twig',['profil'=>$profil]);

    }
    public function addClient(Application $app){
        $this->clientModel = new ClientModel($app);
        $client = $this->clientModel->getAllClient();
        return $app["twig"]->render('backOff/Client/addClient.html.twig',['client'=>$client]);
    }


    public function addFormClient(Application $app){


    }

    public function editClient(Application $app,$idProduit){

    }


    public function editFormClient(Application $app){



    }

    public function deleteClient(Application $app,$id)
    {
        $id_utilisateur=$id;
        $this->comteModel =new CompteModel($app);
        $this->comteModel->deleteCompteUtilisateur($id_utilisateur);
        $this->clientModel=new UtilisateurModel($app);
        $this->clientModel->deleteUtilisateur($id_utilisateur);
        return $app->redirect($app["url_generator"]->generate("client.index"));
    }

    public function connect(Application $app) {  //http://silex.sensiolabs.org/doc/providers.html#controller-providers
        $controllers = $app['controllers_factory'];

        $controllers->get('/', 'App\Controller\ClientController::index')->bind('client.index');
        $controllers->get('/addClient/','App\Controller\ClientController::addClient')->bind('client.add');
        $controllers->post('/addClient/ ','App\Controller\ClientController::addFromClient')->bind('client.addFromAdd');

        $controllers->get('/showClient', 'App\Controller\ClientController::showClient')->bind('client.show');
        $controllers->get('/deleteClient/{id}', 'App\Controller\ClientController::deleteClient')->bind('client.delete')->assert('id', '\d+');
        $controllers->get('/monProfil', 'App\Controller\ClientController::afficherProfil')->bind('client.profil');
        $controllers->delete('/deleteClient', 'App\Controller\ClientController::validFormDeleteClient')->bind('client.validFormDelete');

        return $controllers;
    }
}
