<?php
namespace App\Controller;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;   // pour utiliser request
use App\Model\PanierModel;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Security;

use App\Model\ProduitModel;
use App\Model\CompteModel;

class PanierController implements ControllerProviderInterface
{
    private $panierModel;
    private $produitModel;
    private $compteModel;

    public function index(Application $app) {

        return $this->showPanierClient($app);
    }


    public function showPanierClient(Application $app) {
        $this->panierModel = new PanierModel($app);
        $this->compteModel=new CompteModel($app);
        $id=$this->compteModel->recupererId($app);
        $panier = $this->panierModel->readUnPanier($id);
        return $app["twig"]->render('frontOff/showPanierClient.html.twig',['data'=>$panier]);
    }

    public function addPanierClient(Application $app,$idProduit){
        
    $this->produitModel=new ProduitModel($app);
    $data= $this->produitModel->getProduit($idProduit);
        return $app["twig"]->render('frontOff/addPanierClient.html.twig',['data'=>$data]);   
    }


    public function addFromPanierClient(Application $app){


        if(isset($_POST['quantite_produits']) && isset($_POST['id_produits'])){
        $donnees['quantite_produits']=htmlspecialchars($_POST['quantite_produits']);
        $data['id_produits']=htmlspecialchars($_POST['id_produits']);
       
        
        if(! is_numeric($donnees['quantite_produits']))$erreurs['quantite_produits']='veuillez saisir une valeur';
        $this->produitModel=new ProduitModel($app);
        $this->panierModel = new PanierModel($app);
        $this->compteModel=new CompteModel($app);

        $id=$this->compteModel->recupererId($app);
        $data=$this->produitModel->getProduit($data['id_produits']);

        $donnees['utilisateur_id']=$id;

        $donnees['id_produits']=$data['id_produits'];
        $donnees['heure_fin_vente']=$data['heure_fin_vente'];


        $panierProd=$this->panierModel->recupererProduitPanier($donnees);
        print_r($panierProd);
        //die();
        if(empty($erreurs)){
    
            if (!empty($panierProd)){
                $donnees['id_panier']=$panierProd['id_panier'];
                //echo $donnees['quantite'];
                $donnees['quantite_panier']=$panierProd['quantite_panier']+$donnees['quantite_produits'];
                $donnees['prix_panier']=$data['prix_produits'] * $donnees['quantite_panier'];
                print_r($donnees);
                //die();
                $this->panierModel->updatePanier($donnees);
                return $app->redirect($app["url_generator"]->generate("produitClient.show"));
                
            }else{
                $donnees['quantite_panier']=$donnees['quantite_produits'];
                $donnees['prix_panier']=$data['prix_produits'] * $donnees['quantite_panier'];
                print_r($donnees);
                $this->panierModel->ajouterDansPanier($donnees);
               // die();
                return $app->redirect($app["url_generator"]->generate("produitClient.show"));
            }
        }else { 
         
            return $app["twig"]->render('frontOff/addPanierClient.html.twig',['donnees'=>$donnees,'data'=>$data,'erreurs'=>$erreurs]);
        }
    }
        else{
            return $app->abort(404, 'error Pb id form AddPanier');
        }
        

    }
    //faire la route pour show pour rester sur chaud et non retourner sur le show pnier juste un coper coller en modifiant la route je le ferai tkt
    public function deleteProduitDansPanier(Application $app,$id) {

        $this->panierModel = new PanierModel($app);
        $panier = $this->panierModel->readUnPanierSuppr($id);

        return $app["twig"]->render('frontOff/deletePanierClient.html.twig',['panier'=>$panier]);
    }

    public function validFormDeletePanier(Application $app, Request $req) {
        $id=$app->escape($req->get('id_panier'));
        if (is_numeric($id)) {
            $this->panierModel = new PanierModel($app);
            $this->panierModel->deletePanier($id);
            return $app->redirect($app["url_generator"]->generate("panier.index"));
        }
        else
            return $app->abort(404, 'error Pb id form Delete');
    }

    public function connect(Application $app) {  //http://silex.sensiolabs.org/doc/providers.html#controller-providers
        $controllers = $app['controllers_factory'];

        $controllers->get('/', 'App\Controller\PanierController::index')->bind('panier.index');
        $controllers->get('/addPanier/{idProduit}','App\Controller\PanierController::addPanierClient')->bind('panier.add');
        $controllers->post('/addPanier/ ','App\Controller\PanierController::addFromPanierClient')->bind('panier.addFromAddPanier');
       
        $controllers->get('/showPanier', 'App\Controller\PanierController::showPanierClient')->bind('panier.show');
       
        $controllers->get('/delete/{id}', 'App\Controller\PanierController::deleteProduitDansPanier')->bind('panier.deleteProduit')->assert('id', '\d+');
        $controllers->delete('/delete', 'App\Controller\PanierController::validFormDeletePanier')->bind('panier.validFormDeletePanier');
        
        return $controllers;
    }
}
