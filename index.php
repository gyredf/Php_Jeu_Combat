<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
  <head>
    <title>TP : Mini jeu de combat</title>
    
    <meta http-equiv="Content-type" content="text/html; charset=iso-8859-1" />
  </head>
  <body>


    <?php
     //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
   

    function chargerClasse($classe)
    {
        require $classe . '.php'; // On inclut la classe correspondante au paramètre passé.
    }

    spl_autoload_register('chargerClasse');
    
    session_start();

    $db = new PDO('mysql:host=127.0.0.1;dbname=battlegame', 'root', '');
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $manager = new PersonnageManager($db);

     ////////////////////////////////////////////////////////////////////////////////////////////////////
   if (isset($_SESSION['perso'])) {
    $perso = $_SESSION['perso'];
    //print_r($perso);exit();
    $contenu = $perso->getId();

    if (!isset($contenu)) {
      print('une erreur est intervenu veuillez creer/utiliser un autre personnage');
      unset($_SESSION['perso']);
      print('erreur:' + $perso->getId());
    } else {
      $perso = $manager->getOne($perso->getId()); //met à jour les dmg
      print_r($perso);
    }
   }


   if (isset($_POST['creer']) && isset($_POST['nom'])) {
      $perso = new Personnage(array('nom' => $_POST['nom'], 'force' => 50, 'degats' => 0, 'niveau'=> 1, 'experience' => 0));
        if (!$perso->nomValide()) {
            $message = 'le nom choisi est invalide.';
            unset($perso);
        }else if ($manager->exists($perso->getNom())) {
            $message = 'le personnage est déjà pris.';
            unset($perso);
        }else{
          $manager->add($perso);
        }
    }
    else if (isset($_POST['utiliser']) && isset($_POST['nom'])) {
        if ($manager->exists($_POST['nom'])) {
          $perso = $manager->getOne($_POST['nom']);
        }else{
          $message = 'ce personnage n existe pas!';
        }
    }
    ////////////////////////////////////////////////////////////////////////////////////////////////////

  

    if (isset($_GET['deconnexion'])){
      session_destroy();
      header('location: .');
      exit();
    }


    if (isset($perso)) //si on crée un perso on le stock
    {
    //}
    

    ?>


      <p>Nombre de personnages créés : <?php echo $manager->count(); ?></p>
      <?php
      if (isset($message))// On a un message à afficher
      {
        echo '<p>'.$message.'</p>'; //si oui, on l'affiche
      }



      if (isset($_GET['frapper'])) {
         $idEn = intval($_GET['frapper']);
         $persoAFrapper = $manager->getOne($idEn);
         //print_r($persoAFrapper);
         $retour = $perso->frapper($persoAFrapper);
         switch ($retour) {
           case Personnage::CEST_MOI:
              echo "Vous ne pouvez pas vous frapper vous même";
             break;
            case Personnage::PERSONNAGE_TUE:
              $manager->delete($persoAFrapper);
              
              $manager->update($perso);
              break;
           case Personnage::PERSONNAGE_FRAPPE:
             echo 'il a pris des dmg';
             $manager->update($perso);
             $manager->update($persoAFrapper);
             break;
    
           default:
             # code...
             break;
         }
      }
     


      //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      ?>

      <p><a href="?deconnexion=1">Deconnexion</a></p>



    <!-- ///////////////////////////////////////////////////////////////////////////////////////////////////////// -->
    <fieldset>
    <legend>Mes informations</legend>
    <p>
      Nom: <?php echo htmlspecialchars($perso->getNom()); ?><br>
      Degats : <?php echo $perso->getDegats(); ?>
    </p>
    </fieldset>
    <!-- ///////////////////////////////////////////////////////////////////////////////////////////////////////// -->



    <!-- ///////////////////////////////////////////////////////////////////////////////////////////////////////// -->
    <fieldset>
      <legend>Qui frapper ?</legend>
      <p>
      <?php 
        $joueurs = $manager->getList($perso->getNom());
        if (empty($joueurs)) {
          echo 'Personne à frapper !';
        }else{
          foreach ($joueurs as $joueur) {
             echo "<a href='?frapper=". $joueur->getId()."'>'". htmlspecialchars($joueur->getNom())."'</a> (dégats : '".$joueur->getDegats()."')<br>";
          }
        }
      ?>
      </p>
    </fieldset>
    <!-- ///////////////////////////////////////////////////////////////////////////////////////////////////////// -->


    <?php
    }
    else
    {
  ?>
    <form action="" method="post">
      <p>
        Nom : <input type="text" name="nom" maxlength="50" />
        <input type="submit" value="Créer ce personnage" name="creer" />
        <input type="submit" value="Utiliser ce personnage" name="utiliser" />
      </p>
    </form>
    <?php
    }
    ?>
  </body>
  </html>
  <?php 
  if (isset($perso)) //si on crée un perso on le stock
    {
     $_SESSION['perso'] = $perso;
    }
  ?>


