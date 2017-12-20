<?php
namespace App\Controller;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;

use Symfony\Component\HttpFoundation\Request;   // pour utiliser request

use App\Model\ProduitModel;
use App\Model\TypeProduitModel;
use App\Model\PanierModel;
use App\Model\CompteModel;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Security;


class ProduitController implements ControllerProviderInterface
{
    private $produitModel;
    private $typeProduitModel;
    private $compteModel;
    private $panierModel;

    public function index(Application $app) {
        return $this->showProduits($app);
    }

    public function showProduits(Application $app) {
        $this->produitModel = new ProduitModel($app);
        $produits = $this->produitModel->getAllProduits();
        return $app["twig"]->render('backOff/Produit/showProduits.html.twig',['data'=>$produits]);
    }

    public function showProduitsClient(Application $app) {
        $this->produitModel = new ProduitModel($app);
        $this->compteModel= new CompteModel($app);

        $produits = $this->produitModel->getAllProduits();

        $this->panierModel = new PanierModel($app);
        $this->compteModel= new CompteModel($app);
        $id=$this->compteModel->recupererId($app);
        $panier = $this->panierModel->readUnPanier($id);
        return $app["twig"]->render('frontOff/showProduitsClient.html.twig',['data'=>$produits ,'panier'=>$panier]);
    }


    public function addProduit(Application $app) {
        $this->typeProduitModel = new TypeProduitModel($app);
        $typeProduits = $this->typeProduitModel->getAllTypeProduits();
      //  dump($typeProduits);
        return $app["twig"]->render('backOff/Produit/addProduit.html.twig',['typeProduits'=>$typeProduits]);
    }

    public function validFormAddProduit(Application $app, Request $req) {
        if (isset($_POST['libelle_produits']) and isset($_POST['prix_produits']) and isset($_POST['quantite_produits']) and isset($_POST['heure_debut_vente']) and isset($_POST['heure_fin_vente']) and isset($_POST['id_typeProduits'])  ) {
            $donnees = [
                'libelle_produits' => htmlspecialchars($_POST['libelle_produits']),                    // echapper les entrées
                'prix_produits' => htmlspecialchars($req->get('prix_produits')),
                'quantite_produits' => htmlspecialchars($req->get('quantite_produits')),
                'heure_debut_vente' => htmlspecialchars($req->get('heure_debut_vente')),
                'heure_fin_vente' => $app->escape($req->get('heure_fin_vente')),
                'id_typeProduits' => $app->escape($req->get('id_typeProduits'))
            ];
            //verifier date
            if ((! preg_match("/^[A-Za-z ]{2,}/",$donnees['libelle_produits']))) $erreurs['libelle_produits']='nom du produit composé de 2 lettres minimum';
            if(! is_numeric($donnees['prix_produits']))$erreurs['prix_produits']='veuillez saisir une valeur';
            if(! is_numeric($donnees['quantite_produits']))$erreurs['quantite_produits']='saisir une valeur numérique';
            //if (! preg_match("/[A-Za-z0-9]{2,}.(jpeg|jpg|png)/",$donnees['photo'])) $erreurs['photo']='nom de fichier incorrect (extension jpeg , jpg ou png)';

            if(! empty($erreurs))
            {
                $this->typeProduitModel = new TypeProduitModel($app);
                $typeProduits = $this->typeProduitModel->getAllTypeProduits();
                return $app["twig"]->render('backOff/Produit/addProduit.html.twig',['donnees'=>$donnees,'erreurs'=>$erreurs,'typeProduits'=>$typeProduits]);
            }
            else
            {
                $this->produitModel = new ProduitModel($app);
                $this->compteModel = new CompteModel($app);
                $id=$this->compteModel->recupererId($app);
                $donnees['id_compte']=$id;
                $this->produitModel->insertProduitV($donnees);
                return $app->redirect($app["url_generator"]->generate("produit.index"));
            }

        }
        else
            return $app->abort(404, 'error Pb data form Add');
    }

    public function deleteProduit(Application $app, $id) {
        $this->typeProduitModel = new TypeProduitModel($app);
        $typeProduits = $this->typeProduitModel->getAllTypeProduits();
        $this->produitModel = new ProduitModel($app);
        $donnees = $this->produitModel->getProduit($id);
        return $app["twig"]->render('backOff/Produit/deleteProduit.html.twig',['typeProduits'=>$typeProduits,'donnees'=>$donnees]);
    }

    public function validFormDeleteProduit(Application $app, Request $req) {
        $id=$app->escape($req->get('id_produits'));
        if (is_numeric($id)) {
            $this->produitModel = new ProduitModel($app);
            $this->produitModel->deleteProduit($id);
            return $app->redirect($app["url_generator"]->generate("produit.index"));
        }
        else
            return $app->abort(404, 'error Pb id form Delete');
    }


    public function editProduit(Application $app, $id) {
        $this->typeProduitModel = new TypeProduitModel($app);
        $typeProduits = $this->typeProduitModel->getAllTypeProduits();
        $this->produitModel = new ProduitModel($app);
        $donnees = $this->produitModel->getProduit($id);
        return $app["twig"]->render('backOff/Produit/editProduit.html.twig',['typeProduits'=>$typeProduits,'donnees'=>$donnees]);
    }

    public function validFormEditProduit(Application $app, Request $req)
    {
        if (isset($_POST['libelle_produits']) && isset($_POST['id_typeProduits'])
            and isset($_POST['heure_fin_vente']) and isset($_POST['heure_debut_vente'])
            and isset($_POST['prix_produits']) and isset($_POST['quantite_produits'])
            and isset($_POST['id_produits'])) {
            $donnees = [
                'libelle_produits' => htmlspecialchars($req->get('libelle_produits')),                    // echapper les entrées
                'id_typeProduits' => htmlspecialchars($req->get('id_typeProduits')),  //$app['request']-> ne focntionne plus
                'heure_fin_vente' => htmlspecialchars($req->get('heure_fin_vente')),
                'heure_debut_vente' => $app->escape($req->get('heure_debut_vente')),  //$req->query->get('photo')-> ne focntionne plus
                'prix_produits' => $app->escape($req->get('prix_produits')),//$req->query->get('photo')
                'quantite_produits' => $app->escape($req->get('quantite_produits')),
                'id_produits' => $app->escape($req->get('id_produits'))
            ];

            if ((!preg_match("/^[A-Za-z ]{2,}/", $donnees['libelle_produits']))) $erreurs['libelle_produits'] = 'nom composé de 2 lettres minimum';
            if (!is_numeric($donnees['id_typeProduits'])) $erreurs['id_typeProduits'] = 'veuillez saisir une valeur';
            if (!is_numeric($donnees['prix_produits'])) $erreurs['prix_produits'] = 'saisir une valeur numérique';
            // if (! preg_match("/[A-Za-z0-9]{2,}.(jpeg|jpg|png)/",$donnees['photo'])) $erreurs['photo']='nom de fichier incorrect (extension jpeg , jpg ou png)';
            if (!is_numeric($donnees['quantite_produits'])) $erreurs['quantite_produits'] = 'saisir une valeur numérique';
            if (!is_numeric($donnees['id_produits'])) $erreurs['id_produits'] = 'saisir une valeur numérique';
            $contraintes = new Assert\Collection(
                [
                    'id_produits' => [new Assert\NotBlank(), new Assert\Type('digit')],
                    'id_typeProduits' => [new Assert\NotBlank(), new Assert\Type('digit')],
                    'quantite_produits' => [new Assert\NotBlank(), new Assert\Type('digit')],
                    'libelle_produits' => [
                        new Assert\NotBlank(['message' => 'saisir une valeur']),
                        new Assert\Length(['min' => 2, 'minMessage' => "Le nom doit faire au moins {{ limit }} caractères."])
                    ],
                    //http://symfony.com/doc/master/reference/constraints/Regex.html

                    'prix_produits' => new Assert\Type(array(
                        'type' => 'numeric',
                        'message' => 'La valeur {{ value }} n\'est pas valide, le type est {{ type }}.',
                    ))
                ]);
            $errors = $app['validator']->validate($donnees, $contraintes);  // ce n'est pas validateValue

            if ( $errors != 0 && !empty($erreurs)) {
                $this->typeProduitModel = new TypeProduitModel($app);
                $typeProduits = $this->typeProduitModel->getAllTypeProduits();
                return $app["twig"]->render('backOff/Produit/editProduit.html.twig', ['donnees' => $donnees, 'errors' => $errors, 'erreurs' => $erreurs, 'typeProduits' => $typeProduits]);
            } else {
                $this->ProduitModel = new ProduitModel($app);
                $this->ProduitModel->updateProduit($donnees);
                return $app->redirect($app["url_generator"]->generate("produit.index"));
            }
        } else
            return $app->abort(404, 'error Pb id form edit');
    }

    public function connect(Application $app) {  //http://silex.sensiolabs.org/doc/providers.html#controller-providers
        $controllers = $app['controllers_factory'];

        $controllers->get('/', 'App\Controller\produitController::index')->bind('produit.index');
        $controllers->get('/show', 'App\Controller\produitController::showProduits')->bind('produit.showProduits');

        $controllers->get('/add', 'App\Controller\produitController::addProduit')->bind('produit.addProduit');
        $controllers->post('/add', 'App\Controller\produitController::validFormAddProduit')->bind('produit.validFormAddProduit');

        $controllers->get('/delete/{id}', 'App\Controller\produitController::deleteProduit')->bind('produit.deleteProduit')->assert('id', '\d+');
        $controllers->delete('/delete', 'App\Controller\produitController::validFormDeleteProduit')->bind('produit.validFormDeleteProduit');

        $controllers->get('/edit/{id}', 'App\Controller\produitController::editProduit')->bind('produit.editProduit')->assert('id', '\d+');
        $controllers->put('/edit', 'App\Controller\produitController::validFormEditProduit')->bind('produit.validFormEditProduit');

        $controllers->get('/showClient', 'App\Controller\produitController::showProduitsClient')->bind('produitClient.show');


        return $controllers;
    }
}
