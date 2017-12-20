<?php

namespace App\Model;

use Doctrine\DBAL\Query\QueryBuilder;
use Silex\Application;

class TypeProduitModel {

    private $db;

    public function __construct(Application $app) {
        $this->db = $app['db'];
    }


    public function getAllTypeProduits() {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('p.id_typeProduits', 'p.libelle_typeProduits')
            ->from('TypeProduits', 'p')
            ->addOrderBy('p.libelle_typeProduits', 'ASC');
        return $queryBuilder->execute()->fetchAll();
    }
}