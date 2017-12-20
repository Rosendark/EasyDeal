<?php
namespace App\Controller;

use App\Model\CompteModel;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;

use Symfony\Component\HttpFoundation\Request;   // pour utiliser request

use App\Model\reservationModel;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security;


class ReservationController implements ControllerProviderInterface
{
    private $reservationModel;
    private $compteModel;

    public function index(Application $app) {
        return $this->showReservation($app);
    }

    public function showReservation(Application $app) {
        $this->reservationModel = new reservationModel($app);
        $reservation = $this->reservationModel->getAllReservation();
        return $app["twig"]->render('backOff/Reservation/showReservation.html.twig',['data'=>$reservation]);
    }


    public function addreservation(Application $app) {
        $this->typereservationModel = new TypereservationModel($app);
        $typereservation = $this->typereservationModel->getAllTypereservation();
        //  dump($typereservation);
        return $app["twig"]->render('backOff/reservation/addreservation.html.twig',['typereservation'=>$typereservation]);
    }

    public function validFormAddreservation(Application $app, Request $req) {
        if (isset($_POST['libelle_reservation']) and isset($_POST['prix_reservation']) and isset($_POST['quantite_reservation']) and isset($_POST['heure_debut_vente']) and isset($_POST['heure_fin_vente']) and isset($_POST['id_typereservation'])  ) {
            $donnees = [
                'libelle_reservation' => htmlspecialchars($_POST['libelle_reservation']),                    // echapper les entrées
                'prix_reservation' => htmlspecialchars($req->get('prix_reservation')),
                'quantite_reservation' => htmlspecialchars($req->get('quantite_reservation')),
                'heure_debut_vente' => htmlspecialchars($req->get('heure_debut_vente')),
                'heure_fin_vente' => $app->escape($req->get('heure_fin_vente')),
                'id_typereservation' => $app->escape($req->get('id_typereservation'))
            ];
            //verifier date
            if ((! preg_match("/^[A-Za-z ]{2,}/",$donnees['libelle_reservation']))) $erreurs['libelle_reservation']='nom du reservation composé de 2 lettres minimum';
            if(! is_numeric($donnees['prix_reservation']))$erreurs['prix_reservation']='veuillez saisir une valeur';
            if(! is_numeric($donnees['quantite_reservation']))$erreurs['quantite_reservation']='saisir une valeur numérique';
            //if (! preg_match("/[A-Za-z0-9]{2,}.(jpeg|jpg|png)/",$donnees['photo'])) $erreurs['photo']='nom de fichier incorrect (extension jpeg , jpg ou png)';

            if(! empty($erreurs))
            {
                $this->typereservationModel = new TypereservationModel($app);
                $typereservation = $this->typereservationModel->getAllTypereservation();
                return $app["twig"]->render('backOff/reservation/addreservation.html.twig',['donnees'=>$donnees,'erreurs'=>$erreurs,'typereservation'=>$typereservation]);
            }
            else
            {
                $this->reservationModel = new reservationModel($app);
                $this->reservationModel->insertreservation($donnees);
                return $app->redirect($app["url_generator"]->generate("reservation.index"));
            }

        }
        else
            return $app->abort(404, 'error Pb data form Add');
    }

    public function deleteReservation(Application $app, $id) {
        $this->reservationModel = new reservationModel($app);
        $donnees = $this->reservationModel->getReservation($id);
        return $app["twig"]->render('backOff/reservation/deleteReservation.html.twig',['donnees'=>$donnees]);
    }

    public function validFormDeleteReservation(Application $app, Request $req) {
        $id=$app->escape($req->get('id_reservation'));
        if (is_numeric($id)) {
            $this->reservationModel = new reservationModel($app);
            $this->reservationModel->deleteReservation($id);
            return $app->redirect($app["url_generator"]->generate("reservation.index"));
        }
        else
            return $app->abort(404, 'error Pb id form Delete');
    }


    public function editReservation(Application $app, $id) {
        $this->compteModel = new CompteModel($app);
        $compte = $this->compteModel->getAllCompte();
        $this->reservationModel = new reservationModel($app);
        $donnees = $this->reservationModel->getReservation($id);
        return $app["twig"]->render('backOff/Reservation/editReservation.html.twig',['compte'=>$compte,'donnees'=>$donnees]);
    }

    public function validFormEditReservation(Application $app, Request $req)
    {
        if (isset($_POST['id_compte']) && isset($_POST['heure_fin_vente'])) {
            $donnees = [
                'id_compte' => htmlspecialchars($req->get('id_compte')),                    // echapper les entrées
                'heure_fin_vente' => htmlspecialchars($req->get('heure_fin_vente')),
            ];

            if (!is_numeric($donnees['id_compte'])) $erreurs['id_compte'] = 'veuillez saisir un compte';
           if (!is_numeric($donnees['heure_fin_vente'])) $erreurs['heure_fin_vente'] = 'saisir une date';


            if (!empty($erreurs)) {
                $this->reservationModel = new reservationModel($app);
                $reservation = $this->reservationModel->getAllReservation();
                return $app["twig"]->render('backOff/reservation/editreservation.html.twig', ['donnees' => $donnees, 'erreurs' => $erreurs, 'reservation' => $reservation]);
            } else {
                $this->reservationModel = new reservationModel($app);
                $this->reservationModel->updateReservation($donnees);
                return $app->redirect($app["url_generator"]->generate("reservation.index"));
            }
        } else
            return $app->abort(404, 'error Pb id form edit');
    }

    public function connect(Application $app) {  //http://silex.sensiolabs.org/doc/providers.html#controller-providers
        $controllers = $app['controllers_factory'];

        $controllers->get('/', 'App\Controller\ReservationController::index')->bind('reservation.index');
        $controllers->get('/show', 'App\Controller\ReservationController::showReservation')->bind('reservation.showReservation');

        $controllers->get('/add', 'App\Controller\ReservationController::addReservation')->bind('reservation.addReservation');
        $controllers->post('/add', 'App\Controller\ReservationController::validFormAddReservation')->bind('reservation.validFormAddReservation');

        $controllers->get('/delete/{id}', 'App\Controller\ReservationController::deleteReservation')->bind('reservation.deleteReservation')->assert('id', '\d+');
        $controllers->delete('/delete', 'App\Controller\ReservationController::validFormDeleteReservation')->bind('reservation.validFormDeleteReservation');

        $controllers->get('/edit/{id}', 'App\Controller\ReservationController::editReservation')->bind('reservation.editReservation')->assert('id', '\d+');
        $controllers->put('/edit', 'App\Controller\ReservationController::validFormEditReservation')->bind('reservation.validFormEditReservation');

        return $controllers;
    }
}
