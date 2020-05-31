<?php

//require(fichier.php);    inclut le fichier php avec le notre
//créer des fonctions qui contiennent des require pour charger des éléménets à un moment t, et ne pas saturer la mémoire
//this pour l'objet   ,  le Self pour la classe
//accès à une constante   NomClasse::NomConst
//fecth = on avance le curseur de un

Class PersonnageManager{

		private $_db;

		public function __construct($db){
			$this->setDb($db);
		}

		public function add(Personnage $perso)
		{	
		  //facto, secu
		  $request = $this->_db->prepare('INSERT INTO personnages SET nom = :nom, `force` = :force, degats = :degats, niveau = :niveau, experience = :experience;');

		  $request->bindValue(':nom', $perso->getNom(), PDO::PARAM_STR);
		  $request->bindValue(':force', $perso->getForce(), PDO::PARAM_INT);
		  $request->bindValue(':degats', $perso->getDegats(), PDO::PARAM_INT);
		  $request->bindValue(':niveau', $perso->getNiveau(), PDO::PARAM_INT);
		  $request->bindValue(':experience', $perso->getExperience(), PDO::PARAM_INT);

		  $request->execute();
		  $perso->setId($this->_db->lastInsertId());
		}


		public function delete(Personnage $perso)
		{
		  // Exécute une requête de type DELETE.
			$this->_db->exec('DELETE FROM personnages WHERE id = '.$perso->getId().';');
		}

		public function exists($info){
			if (is_int($info))
			{
				$q = $this->_db->prepare('Select COUNT(*) FROM personnages WHERE id = :id');
				$q->execute(array(':id' => $info));

				return(bool) $q->fetchColumn();
			}

			$q = $this->_db->prepare('Select COUNT(*) FROM personnages WHERE nom = :nom');
			$q->execute(array(':nom' => $info));

			return(bool) $q->fetchColumn();
		}


		public function getOne($info)
		{
		  // Exécute une requête de type SELECT avec une clause WHERE, et retourne un objet Personnage.
			if (is_int($info))
			{
				$q = $this->_db->query('SELECT id, nom, degats FROM personnages WHERE id = '.$info);
				$donnees = $q->fetch(PDO::FETCH_ASSOC);
			
				return new Personnage($donnees);
			}
			else
			{	
				print('info est une chaîne de caractère');
				$q = $this->_db->prepare('SELECT id, nom, degats FROM personnages WHERE nom = :nom');
				
				if ($q->errorCode() > 0) {
		            echo "<br/>Une erreur SQL est intervenue : ";
		            print_r($q->errorInfo()[2]);
		        }
				

				$q->execute(array(':nom' => $info));


				return new Personnage($q->fetch(PDO::FETCH_ASSOC));
			}
		}


		public function getList(){
        $persos = array();

        $request = $this->_db->query('SELECT * FROM personnages ORDER BY nom;');
        while ($ligne = $request->fetch()) {
            $persos[] = new Personnage($ligne);
        }

        return $persos;
   		}





		public function update(Personnage $perso){
        $request = $this->_db->prepare('UPDATE personnages SET nom = :nom, `force` = :force, `degats` = :degats, `niveau` = :niveau, `experience` = :experience WHERE `id` = :id ;');

        $request->bindValue(':nom', $perso->getNom(), PDO::PARAM_STR);
        $request->bindValue(':force', $perso->getForce(), PDO::PARAM_INT);
        $request->bindValue(':degats', $perso->getDegats(), PDO::PARAM_INT);
        $request->bindValue(':niveau', $perso->getNiveau(), PDO::PARAM_INT);
        $request->bindValue(':experience', $perso->getExperience(), PDO::PARAM_INT);
        $request->bindValue(':id', $perso->getId(), PDO::PARAM_INT);

        $request->execute();
    }



		public function setDb(PDO $db){
			$this->_db= $db;
		}


		public function count(){
			$q = $this->_db->query('SELECT COUNT(*) FROM personnages');
			$donnees = $q->fetch(PDO::FETCH_ASSOC);

			return implode($donnees);
		}

}


?>

