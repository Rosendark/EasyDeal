<?php

namespace App\Model;

use Doctrine\DBAL\Query\QueryBuilder;
use Silex\Application;

class UtilisateurModel{

    private $db;

    public function __construct(Application $app) {
        $this->db = $app['db'];
    }


 public function getAllClients(){

     $queryBuilder = new QueryBuilder($this->db);

     $queryBuilder = new QueryBuilder($this->db);
     $queryBuilder
         ->select('*')
         ->from('Utilisateur', 'u')
         ->innerJoin('u', 'Compte', 'c', 'u.id_utilisateur=c.id_utilisateur')
         ->where('c.droits= :droits')
         ->addOrderBy('u.id_utilisateur','ASC')
         ->addOrderBy('u.nom_utilisateur', 'ASC')
         ->setParameter('droits', 'ROLE_CLIENT');
     return $queryBuilder->execute()->fetchAll();
 }

 public function getProfil($id_compte){

     $queryBuilder = new QueryBuilder($this->db);
     $queryBuilder
         ->select('*')
         ->from('Utilisateur', 'u')
         ->innerJoin('u', 'Compte', 'c', 'u.id_utilisateur=c.id_utilisateur')
         ->innerJoin('u', 'Entreprise', 'e', 'u.id_entreprise=e.id_entreprise')
         ->where('c.id_compte= :id_compte')
         ->setParameter('id_compte', $id_compte);
     return $queryBuilder->execute()->fetchAll();

 }

    public function getAllVendeurs(){

        $queryBuilder = new QueryBuilder($this->db);

        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('*')
            ->from('Utilisateur', 'u')
            ->innerJoin('u', 'Compte', 'c', 'u.id_utilisateur=c.id_utilisateur')
            ->where('c.droits= :droits')
            ->addOrderBy('u.id_utilisateur','ASC')
            ->addOrderBy('u.nom_utilisateur', 'ASC')
            ->setParameter('droits', 'ROLE_VENDEUR');
        return $queryBuilder->execute()->fetchAll();
    }

public function deleteUtilisateur($id_utilisateur){
    $queryBuilder = new QueryBuilder($this->db);
    $queryBuilder
        ->delete('Utilisateur')
        ->where('id_utilisateur = :id_utilisateur')
        ->setParameter('id_utilisateur',(int)$id_utilisateur)
    ;
    return $queryBuilder->execute();


}


}