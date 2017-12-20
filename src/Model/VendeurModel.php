<?php

namespace App\Model;

use Doctrine\DBAL\Query\QueryBuilder;
use Silex\Application;

class VendeurModel
{

    private $db;

    public function __construct(Application $app)
    {
        $this->db = $app['db'];
    }

    // http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/query-builder.html#join-clauses
    public function getAllProduitsByVendeur($id)
    {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('*')
            ->from('Produits','p')
            ->innerJoin('p','Compte','c','p.id_compte = c.id_compte')
            ->where('p.id_compte='.$id.'');

        return $queryBuilder->execute()->fetchAll();

    }



    function getProduit($idProduit)
    {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('*')
            ->from('Produits')
            ->where('id_produits= :id_produits')
            ->setParameter('id_produits', $idProduit);
        return $queryBuilder->execute()->fetch();
    }

    public function updateProduit($donnees)
    {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->update('Produits')
            ->set('libelle_produits', '?')
            ->set('id_typeProduits', '?')
            ->set('prix_produits', '?')
            ->set('quantite_produits', '?')
            ->set('heure_debut_vente', '?')
            ->set('heure_fin_vente', '?')
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

    public function deleteProduit($id_produits)
    {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->delete('Produits')
            ->where('id_produits = :id_produits')
            ->setParameter('id_produits', (int)$id_produits);
        return $queryBuilder->execute();
    }


}



