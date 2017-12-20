<?php

namespace App\Model;

use Doctrine\DBAL\Query\QueryBuilder;
use Silex\Application;

class ProduitModel {

    private $db;

    public function __construct(Application $app) {
        $this->db = $app['db'];
    }
    // http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/query-builder.html#join-clauses
    public function getAllProduits() {

        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('*')
            ->from('Produits', 'p')
            ->innerJoin('p', 'typeProduits', 't', 'p.id_typeProduits=t.id_typeProduits')
            ->addOrderBy('p.id_produits','ASC')
            ->addOrderBy('p.libelle_produits', 'ASC');
        return $queryBuilder->execute()->fetchAll();

    }

    public function insertProduit($donnees) {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder->insert('Produits')
            ->values([
                'libelle_produits' => '?',
                'id_typeProduits' => '?',
                'prix_produits' => '?',
                'quantite_produits' => '?',
                'heure_debut_vente'=>'?',
               'heure_fin_vente' =>'?',
            ])
            ->setParameter(0, $donnees['libelle_produits'])
            ->setParameter(1, $donnees['id_typeProduits'])
            ->setParameter(2, $donnees['prix_produits'])
            ->setParameter(3, $donnees['quantite_produits'])
            ->setParameter(4, $donnees['heure_debut_vente'])
            ->setParameter(5, $donnees['heure_debut_vente'])
        ;
        echo $queryBuilder;
        return $queryBuilder->execute();
    }

    public function insertProduitV($donnees)
    {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder->insert('Produits')
            ->values([
                'libelle_produits' => '?',
                'id_typeProduits' => '?',
                'prix_produits' => '?',
                'quantite_produits' => '?',
                'heure_debut_vente' => '?',
                'heure_fin_vente' => '?',
                'id_compte' => '?'
            ])
            ->setParameter(0, $donnees['libelle_produits'])
            ->setParameter(1, $donnees['id_typeProduits'])
            ->setParameter(2, $donnees['prix_produits'])
            ->setParameter(3, $donnees['quantite_produits'])
            ->setParameter(4, $donnees['heure_debut_vente'])
            ->setParameter(5, $donnees['heure_fin_vente'])
            ->setParameter(6, $donnees['id_compte'] );
        echo $queryBuilder;
        return $queryBuilder->execute();
    }


    function getProduit($idProduit) {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('*')
            ->from('Produits')
            ->where('id_produits= :id_produits')
            ->setParameter('id_produits', $idProduit);
        return $queryBuilder->execute()->fetch();
    }

    public function updateProduit($donnees) {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->update('Produits')
            ->set('libelle_produits', '?')
            ->set('id_typeProduits','?')
            ->set('prix_produits','?')
            ->set('quantite_produits','?')
            ->set('heure_debut_vente','?')
            ->set('heure_fin_vente','?')
            ->where('id_produits= ?')
            ->setParameter(0, $donnees['libelle_produits'])
            ->setParameter(1, $donnees['id_typeProduits'])
            ->setParameter(2, $donnees['prix_produits'])
            ->setParameter(3, $donnees['quantite_produits'])
            ->setParameter(4, $donnees['heure_debut_vente'])
            ->setParameter(5, $donnees['heure_fin_vente'])
            ->setParameter(6, $donnees['id_produits']);
        echo $queryBuilder;

        return $queryBuilder->execute();


    }

    public function deleteProduit($id_produits) {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->delete('Produits')
            ->where('id_produits = :id_produits')
            ->setParameter('id_produits',(int)$id_produits)
        ;
        return $queryBuilder->execute();
    }



}