<?php

namespace App\Model;

use Doctrine\DBAL\Query\QueryBuilder;
use Silex\Application;

class ReservationModel {

    private $db;

    public function __construct(Application $app) {
        $this->db = $app['db'];
    }
    // http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/query-builder.html#join-clauses
    public function getAllReservation() {

        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('r.id_reservation, r.prix_reservation, r.heure_fin_vente, r.id_compte')
            ->from('reservation', 'r')
            ->addOrderBy('r.id_reservation','ASC');
        return $queryBuilder->execute()->fetchAll();

    }
    public function getAllReservationByVendeur($id)
    {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('r.id_reservation, r.prix_reservation, r.heure_fin_vente,r.id_compte')
            ->from('reservation', 'r')
            ->where('r.id_compte='.$id.'')
            ->addOrderBy('r.id_reservation','ASC');
        return $queryBuilder->execute()->fetchAll();

    }

    public function insertReservation($donnees) {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder->insert('Reservation')
            ->values([
                'libelle_Reservation' => '?',
                'id_typeReservation' => '?',
                'prix_Reservation' => '?',
                'quantite_Reservation' => '?',
                'heure_debut_vente'=>'?',
                'heure_fin_vente' =>'?'
            ])
            ->setParameter(0, $donnees['libelle_Reservation'])
            ->setParameter(1, $donnees['id_typeReservation'])
            ->setParameter(2, $donnees['prix_Reservation'])
            ->setParameter(3, $donnees['quantite_Reservation'])
            ->setParameter(4, $donnees['heure_debut_vente'])
            ->setParameter(5, $donnees['heure_debut_vente'])
        ;
        echo $queryBuilder;
        return $queryBuilder->execute();
    }

    function getReservation($id_reservation) {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('*')
            ->from('reservation')
            ->where('id_reservation= :id_reservation')
            ->setParameter('id_reservation', $id_reservation);
        return $queryBuilder->execute()->fetch();
    }

    public function updateReservation($donnees) {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->update('reservation')
            ->set('id_compte', '?')
            ->set('heure_fin_vente','?')
            ->where('id_reservation= ?')
            ->setParameter(0, $donnees['id_compte'])
            ->setParameter(1, $donnees['heure_fin_vente'])
            ->setParameter(2, $donnees['id_reservation']);
        echo $queryBuilder;

        return $queryBuilder->execute();


    }

    public function deleteReservation($id_reservation) {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->delete('reservation')
            ->where('id_reservation = :id_reservation')
            ->setParameter('id_reservation',(int)$id_reservation)
        ;
        return $queryBuilder->execute();
    }



}