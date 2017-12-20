<?php
namespace App\Model;

use Silex\Application;
use Doctrine\DBAL\Query\QueryBuilder;;

class CompteModel {

	private $db;

	public function __construct(Application $app) {
		$this->db = $app['db'];
	}



	public function verif_login_mdp_Utilisateur($login,$mdp){
		$sql = "SELECT id_compte,username,motDePasse,droits FROM Compte WHERE username = ? AND motdepasse = ?";
		$res=$this->db->executeQuery($sql,[$login,$mdp]);   //md5($mdp);
		if($res->rowCount()==1)
			return $res->fetch();
		else
			return false;
	}
	// public function verif_login_mdp_Utilisateur($login,$mdp){
	// 	$sql = "SELECT id,login,password,droit FROM users WHERE login = ? AND password = ?";
	// 	$res=$this->db->executeQuery($sql,[$login,$mdp]);   //md5($mdp);
	// 	if($res->rowCount()==1)
	// 		return $res->fetch();
	// 	else
	// 		return false;
	// }

    public function recupererId(Application $app){
        return $app['session']->get('id_compte');
    }

	public function getUser($user_id) {
		$queryBuilder = new QueryBuilder($this->db);
		$queryBuilder
			->select('*')
			->from('Compte')
			->where('id_compte = :idUser')
			->setParameter('idUser', $user_id);
		return $queryBuilder->execute()->fetch();

	}

    public function getAllCompte() {

        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('*')
            ->from('compte', 'c')
            ->addOrderBy('c.id_compte','ASC');
        return $queryBuilder->execute()->fetchAll();

    }

	public function deleteCompteUtilisateur($id_utilisateur){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->delete('Compte')
            ->where('id_utilisateur = :id_utilisateur')
            ->setParameter('id_utilisateur',(int)$id_utilisateur)
        ;
        return $queryBuilder->execute();
    }
}