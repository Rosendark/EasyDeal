<?php
namespace App\Controller;

use App\Model\ReservationModel;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;   // pour utiliser request
use App\Model\VendeurModel;
use Symfony\Component\Security;

use App\Model\TypeProduitModel;
use App\Model\UtilisateurModel;
use App\Model\CompteModel;
use App\Model\ProduitModel;

class VendeurController implements ControllerProviderInterface
{
    private $vendeurModel;
    private $compteModel;
    private $reservationModel;
    private $typeProduitModel;
    private $produitModel;

    public function index(Application $app) {
        return $this->showVendeur($app);
    }


    public function showVendeur(Application $app) {
        $this->vendeurModel = new VendeurModel($app);
        $this->compteModel=new CompteModel($app);
        $id=$this->compteModel->recupererId($app);
        $vendeurs = $this->vendeurModel->getAllProduitsByVendeur($id);
        return $app["twig"]->render('backOff/Vendeur/showVendeur.html.twig',['vendeurs'=>$vendeurs]);
    }

    public function showReservationVendeur(Application $app) {
        $this->vendeurModel = new VendeurModel($app);
        $this->compteModel=new CompteModel($app);
        $this->reservationModel = new ReservationModel($app);
        $id=$this->compteModel->recupererId($app);
        $reservation = $this->reservationModel->getAllReservationByVendeur($id);
        return $app["twig"]->render('backOff/Vendeur/showReservationVendeur.html.twig',['vendeurs'=>$reservation]);
    }


    public function addVendeur(Application $app){
        $this->typeProduitModel = new TypeProduitModel($app);
        $typeProduits = $this->typeProduitModel->getAllTypeProduits();
        return $app["twig"]->render('backOff/Vendeur/addVendeur.html.twig',['typeProduits'=>$typeProduits]);
    }

    public function addformVendeur(Application $app, Request $req)
    {
        if (isset($_POST['libelle_produits']) and isset($_POST['prix_produits']) and isset($_POST['quantite_produits']) and isset($_POST['heure_debut_vente']) and isset($_POST['heure_fin_vente']) and isset($_POST['id_typeProduits']) ) {
            $donnees = [
                'libelle_produits' => htmlspecialchars($_POST['libelle_produits']),                    // echapper les entrées
                'prix_produits' => htmlspecialchars($req->get('prix_produits')),
                'quantite_produits' => htmlspecialchars($req->get('quantite_produits')),
                'heure_debut_vente' => htmlspecialchars($req->get('heure_debut_vente')),
                'heure_fin_vente' => $app->escape($req->get('heure_fin_vente')),
                'id_typeProduits' => $app->escape($req->get('id_typeProduits'))
            ];
            if ((! preg_match("/^[A-Za-z ]{2,}/",$donnees['libelle_produits']))) $erreurs['libelle_produits']='nom du produit composé de 2 lettres minimum';
            if(! is_numeric($donnees['prix_produits']))$erreurs['prix_produits']='veuillez saisir une valeur';
            if(! is_numeric($donnees['quantite_produits']))$erreurs['quantite_produits']='saisir une valeur numérique';

            //if (! preg_match("/[A-Za-z0-9]{2,}.(jpeg|jpg|png)/",$donnees['photo'])) $erreurs['photo']='nom de fichier incorrect (extension jpeg , jpg ou png)';

            if(! empty($erreurs))
            {
                $this->typeProduitModel = new TypeProduitModel($app);
                $typeProduits = $this->typeProduitModel->getAllTypeProduits();
                return $app["twig"]->render('backOff/Vendeur/addVendeur.html.twig',['donnees'=>$donnees,'erreurs'=>$erreurs,'typeProduits'=>$typeProduits]);
            }
            else
            {
                $this->produitModel = new ProduitModel($app);
                $this->compteModel = new CompteModel($app);
                $id=$this->compteModel->recupererId($app);
                $donnees['id_compte']=$id;
                $this->produitModel->insertProduitV($donnees);
                
                return $app->redirect($app["url_generator"]->generate("vendeur.index"));
            }

        }
        else
            return $app->abort(404, 'error Pb data form Add');
    }

    public function editVendeur(Application $app){


    }

    public function deleteVendeur(Application $app,$id)
    {
        $id_utilisateur=$id;
        $this->vendeurModel =new UtilisateurModel($app);
        $this->comteModel =new CompteModel($app);
        $this->comteModel->deleteCompteUtilisateur($id_utilisateur);
        $this->vendeurModel->deleteUtilisateur($id_utilisateur);
        return $app->redirect($app["url_generator"]->generate("vendeur.index"));
    }

    public function connect(Application $app) {  //http://silex.sensiolabs.org/doc/providers.html#controller-providers
        $controllers = $app['controllers_factory'];

        $controllers->get('/', 'App\Controller\VendeurController::index')->bind('vendeur.index');

        $controllers->get('/add/','App\Controller\VendeurController::addVendeur')->bind('vendeur.add');
        $controllers->post('/add/ ','App\Controller\VendeurController::addformVendeur')->bind('vendeur.addformVendeur');

        $controllers->get('/show', 'App\Controller\VendeurController::showVendeur')->bind('vendeur.show');
        $controllers->get('/showReservation', 'App\Controller\VendeurController::showReservationVendeur')->bind('vendeur.showReservation');

        $controllers->get('/delete/{id}', 'App\Controller\VendeurController::deleteVendeur')->bind('vendeur.delete')->assert('id', '\d+');
        $controllers->delete('/delete', 'App\Controller\VendeurController::validFormDeleteVendeur')->bind('vendeur.validFormDelete');

        return $controllers;
    }
}
