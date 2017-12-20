<?php

namespace App\Model;

use Doctrine\DBAL\Query\QueryBuilder;
use Silex\Application;

class PanierModel {

    private $db;

    public function __construct(Application $app) {
        $this->db = $app['db'];
    }

    public function readUnPanier($idUtilisateur){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('*')
            ->from('Panier','pan')
            ->innerJoin('pan','Produits','prod','pan.id_produits=prod.id_produits')
            ->innerJoin('pan','Utilisateur','us','pan.utilisateur_id=us.id_utilisateur')
            ->where('pan.utilisateur_id='.$idUtilisateur.'');

        return $queryBuilder->execute()->fetchAll();

    }

    public function updatePanier($donnees){
        $queryBuilder = new QueryBuilder($this->db);
      $queryBuilder->update('Panier')
            ->set('quantite_panier','"'.$donnees['quantite_panier'].'"')
            ->set('heure_fin_vente', '"'.$donnees['heure_fin_vente'].'"')
            ->set('prix_panier', ''.$donnees['prix_panier'].'')
            ->set('utilisateur_id', '"'.$donnees['utilisateur_id'].'"')
            ->set('id_produits', '"'.$donnees['id_produits'].'"')
            ->set('id_reservation', 'null')
            ->where('id_panier='.$donnees['id_panier'].'')
        ;
        echo $queryBuilder;
        return $queryBuilder->execute();
    }

    public function readUnPanierSuppr($id_panier){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('*')
            ->from('Panier','pan')
            ->innerJoin('pan','Produits','prod','pan.id_produits=prod.id_produits')
            ->where('pan.id_panier='.$id_panier.'');
        return $queryBuilder->execute()->fetch();

    }

    public function deletePanier($id_panier){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder->delete('Panier')
            ->where('id_panier='.$id_panier.'')
        ;
        return $queryBuilder->execute();
    }

    public function ajouterDansPanier($donnees){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder->insert('Panier')
            ->values([
                'quantite_panier' => '?',
                'prix_panier' => '?',
                'heure_fin_vente' => '?',
                'utilisateur_id' => '?',
                'id_produits' => '?'

            ])
            ->setParameter(0, $donnees['quantite_panier'])
            ->setParameter(1, $donnees['prix_panier'])
            ->setParameter(2, $donnees['heure_fin_vente'])
            ->setParameter(3, $donnees['utilisateur_id'])
            ->setParameter(4, $donnees['id_produits'])
        ;
        echo $queryBuilder;
        return $queryBuilder->execute();
    }
    public function recupererProduitPanier($donnees){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('pan.id_produits,pan.quantite_panier,pan.id_panier')
            ->from('Panier','pan')
            ->innerJoin('pan','Produits','prod','pan.id_produits=prod.id_produits')
            ->where('prod.id_produits='.$donnees['id_produits'].' and pan.utilisateur_id='.$donnees['utilisateur_id'].'');
        return $queryBuilder->execute()->fetch();

    }

}