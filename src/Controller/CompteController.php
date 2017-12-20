<?php
namespace App\Controller;

use App\Model\CompteModel;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;   // modif version 2.0

use Symfony\Component\HttpFoundation\Request;   // pour utiliser request

class CompteController implements ControllerProviderInterface {

	private $compteModel;

	public function index(Application $app) {
		return $this->connexionUser($app);
	}

	public function connexionUser(Application $app)
	{
		return $app["twig"]->render('login.html.twig');
	}

	public function validFormConnexionUser(Application $app, Request $req)
	{

		$app['session']->clear();
		$donnees['login']=$req->get('login');
		$donnees['password']=$req->get('password');
		$this->compteModel = new CompteModel($app);
		$data=$this->compteModel->verif_login_mdp_Utilisateur($donnees['login'],$donnees['password']);

		if($data != NULL)
		{
			$app['session']->set('droits', $data['droits']);  //dans twig {{ app.session.get('roles') }}
			$app['session']->set('username', $data['username']);
			$app['session']->set('logged', 1);
			$app['session']->set('id_compte', $data['id_compte']);

			return $app->redirect($app["url_generator"]->generate("accueil"));
		}
		else
		{
			$app['session']->set('erreur','mot de passe ou login incorrect');
			return $app["twig"]->render('login.html.twig');
		}
	}
	public function deconnexionSession(Application $app)
	{
		$app['session']->clear();
		$app['session']->getFlashBag()->add('msg', 'vous êtes déconnecté');
		return $app->redirect($app["url_generator"]->generate("accueil"));
	}



	public function connect(Application $app) {
		$controllers = $app['controllers_factory'];
		$controllers->match('/', 'App\Controller\CompteController::index')->bind('compte.index');
		$controllers->get('/login', 'App\Controller\CompteController::connexionUser')->bind('compte.login');
		$controllers->post('/login', 'App\Controller\CompteController::validFormConnexionUser')->bind('compte.validFormlogin');
		$controllers->get('/logout', 'App\Controller\CompteController::deconnexionSession')->bind('compte.logout');
		return $controllers;
	}
}