<?php

namespace Locabraz\models\modelClass;

use Locabraz\models\DbConnector;

/**
 * *****Liste des méthodes*****
 * insertRental (créer nouvelle location et l'insérer dans la base de données)
 * udpateRental (mettre à jour les informations dans la base de données)
 * deleteRental (supprimer une location de la base de données)
 * getAllRentals (récupérer toutes les locations)
 * getFourRentals (récupérer les 4 dernières locations)
 * getRentalPhotos (récupérer les photos liées à la location)
 */

class Rental extends DbConnector
{

   /** Créer une location **/
   public function insertRental($type, $rooms, $description, $photolinks, $photoalts)
   {
       $db = self::dbConnect();
   
       $req = $db->prepare(
           "INSERT INTO rentals (
           type,
           rooms,
           description
           ) 
           VALUES (?, ?, ?)"
       );
       $req->execute([$type, $rooms, $description]);
       
   
       foreach ($photolinks['tmp_name'] as $index => $tmp_name) {
           $target_dir = "public/img/";
           $target_file = $target_dir . basename($photolinks["name"][$index]);
           $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
           $newfilename = uniqid() . '.' . $imageFileType;
           $target_file = $target_dir . $newfilename;
   
           move_uploaded_file($tmp_name, $target_file);
   
           $photoalt = $photoalts[$index];
   
           $req = $db->prepare(
               "INSERT INTO rentalgallerie (
               photolink,
               alt
               ) 
               VALUES (?, ?)"
           );
           $req->execute([$target_file, $photoalt]);
       }
   }
   

   /** Mettre à jour une location **/
   public function updateRental($id, $type, $rooms, $description, $photoIds)
   {
      $db = self::dbConnect();

      $req = $db->prepare(
         "UPDATE rentals 
        SET type = ?, 
        rooms = ?, 
        description = ?
        WHERE idRentals = ?"
      );
      $req->execute([$type, $rooms, $description, $id]);

      $req = $db->prepare(
         "DELETE FROM representer 
        WHERE idRentals = ?"
      );
      $req->execute([$id]);

      foreach ($photoIds as $photoId) {
         $req = $db->prepare(
            "INSERT INTO representer (
            idRentals,
            idPhotorental
            ) 
            VALUES (?, ?)"
         );
         $req->execute([$id, $photoId]);
      }
   }

   /** Supprimer une location **/
   public function deleteRental($id)
   {
      $db = self::dbConnect();

      $req = $db->prepare(
         "DELETE FROM rentals 
        WHERE idRentals = ?"
      );
      $req->execute([$id]);
   }

   /** Récupérer toutes les locations **/
   public function getAllRentals()
   {
      $db = self::dbConnect();

      $req = $db->prepare("SELECT * FROM rentals");
      $req->execute();

      $rentals = $req->fetchAll();

      if (!$rentals) {
         throw new \Exception('Aucune location trouvée');
      }

      return $rentals;
   }

   /** Récupérer les quatre dernières locations **/

   public function getFourRentals()
   {
      $db = self::dbConnect();

      $req = $db->prepare("SELECT * FROM rentals ORDER BY id ASC LIMIT 4");
      $req->execute();

      $rentals = $req->fetchAll();

      if (!$rentals) {
         throw new \Exception('Aucune location trouvée');
      }

      return $rentals;
   }

   /** Récupérer les photos d'une location **/
   public function getRentalPhotos($id)
   {
      $db = self::dbConnect();

      $req = $db->prepare(
         "SELECT r.idRentals, g.idPhotorental, g.photolink, g.alt
      FROM rentals r 
      JOIN representer rep ON r.idRentals = rep.idRentals 
      JOIN rentalgallerie g ON rep.idPhotorental = g.idPhotorental
      WHERE r.idRentals = ?"
      );

      $req->execute([$id]);

      $photos = $req->fetchAll();

      if (!$photos) {
         throw new \Exception('Aucune photo trouvée pour cette location');
      }

      return $photos;
   }
}
