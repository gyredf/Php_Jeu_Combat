<?php

//require(fichier.php);    inclut le fichier php avec le notre
//créer des fonctions qui contiennent des require pour charger des éléménets à un moment t, et ne pas saturer la mémoire
//this pour l'objet   ,  le Self pour la classe
//accès à une constante   NomClasse::NomConst
//fecth = on avance le curseur de un


class Personnage
{
    private $_id = null;
    private $_force = 100;
    private $_experience = 0;
    private $_degats = 0;
    private $_nom = "unknow";
    private $_niveau = 0;

    const CEST_MOI = 1;
    const PERSONNAGE_TUE = 2;
    const PERSONNAGE_FRAPPE = 3;

    public function __construct(array $ligne){
    	$this->hydrate($ligne);
    }


    public function nomValide(){
    	return !empty($this->_nom);
    }


    public function hydrate(array $ligne){
      foreach ($ligne as $key => $value) {
       $method = 'set'.ucfirst($key);

       if (method_exists($this, $method)) {
         $this->$method($value);
       }
      }
    }

    public function setNiveau($niveau)
    {
      $niveau = (int) $niveau;
      
      if ($niveau >= 1 && $niveau <= 100)
      {
        $this->_niveau = $niveau;
      }
    }
    
    public function setExperience($experience)
    {
      $experience = (int) $experience;
      
      if ($experience >= 1 && $experience <= 100)
      {
        $this->_experience = $experience;
      }
    }
  

    // Liste des setters  
    public function setId($id)
    {
      $id = (int) $id;
      
      // On vérifie ensuite si ce nombre est bien strictement positif.
      if ($id > 0)
      {
        // Si c'est le cas, c'est tout bon, on assigne la valeur à l'attribut correspondant.
        $this->_id = $id;
      }
    }

    public function setNom($nom)
    {
      // On vérifie qu'il s'agit bien d'une chaîne de caractères.
      if (is_string($nom))
      {
        $this->_nom = $nom;
      }
    }

     public function setDegats($degats)
    {
      $degats = (int) $degats;

      if ($degats > 0)
      {
        $this->_degats = $degats;
      }
    }

      public function setForce($force)
    {
      $force = (int) $force;

      if ($force > 0)
      {
        $this->_force = $force;
      }
    }
  
  
    public function frapper(Personnage $persoAFrappe){ //le Personnage devant le param est juste là pour imposer un type à passer
      if ($persoAFrappe->getId() == $this->_id){
        return self::CEST_MOI;
      }else{
        return $persoAFrappe->recevoirDegats();
      }
    }

    public function getDegats(){
        return $this->_degats;
    }

    public function getId()
    {
      return $this->_id;
    }

    public function getForce()
    {
      return $this->_force;
    }

    public function getExperience(){
        return $this->_experience;
    }


    public function getNom(){
        return $this->_nom;
    }

    public function getNiveau(){
        return $this->_niveau;
    }

    public function incExperience($exp){
            $this->_experience += $exp;
    }

    public function recevoirDegats()
    {
       $this->_degats += 5;
      if ($this->_degats >= 100) {
        return self::PERSONNAGE_TUE;
      } else {
        return self::PERSONNAGE_FRAPPE;
      }
    }

}


?>

