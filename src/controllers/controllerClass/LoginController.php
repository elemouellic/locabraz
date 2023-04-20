<?php

namespace Locabraz\controllers\controllerClass;

// use Locabraz\controllers\MainController;
use Locabraz\controllers\UserController;
use Locabraz\models\modelClass\Login;

/**
 * *****Liste des méthodes*****
 * userAdmin(Contrôleur pour vue admin)
 * loginUser (Connexion de l'utilisateur)
 * createUser (créer nouvel utilisateur et l'ajouter dans la base de données)
 * createUserByAdmin (créer utilisateur par dashboard admin)
 * upgradeUser (mise à jour des informations de l'utilisateur connecté)
 * removeUser (supprimer compte utilisateur)
 * obtainAllUsers (récupérer comptes utilisateurs)
 */



class LoginController extends UserController
{

    /** Vue admin */
    public function userAdmin(): void
    {
        $controller = new LoginController();
        $users = $controller->obtainAllUsers();
        require_once $this->getViewAdmin('useradmin');
    }

    /**Page de connexion */

    public function loginUser()
    {

        $email = $_POST['email'];
        $password = $_POST['password'];

        $user = new Login();
        $user->userLogin($email, $password);

        // Démarrage de la session
        session_start();

        $_SESSION['email'] = $email;
        $_SESSION['loggedin'] = true;

        //Redirection vers le compte utilisateur ou sur la page login
        if (isset($_SESSION['admin']) && $_SESSION['admin']) {
            header("Location: " . $_ENV['SITE_URL'] . "?action=dashboard");
        } elseif ($_SESSION['loggedin']) {
            header("Location: " . $_ENV['SITE_URL'] . "?action=account");
        } else {
            header("Location: " . $_ENV['SITE_URL'] . "?action=login");
        }
    }

    public function logOut()
    {
        // Démarrage de la session
        session_start();

        // Suppression de toutes les variables de session
        $_SESSION = array();

        // Destruction de la session
        session_destroy();
        setcookie('PHPSESSID', '', time() - 3600, '/');

        // Redirection vers la page de login
        header("Location: " . $_ENV['SITE_URL'] . "?action=login");
    }



    /**Créer un utilisateur */

    public function createUser()
    {
        $email = $_POST['email'];
        $name = $_POST['name'];
        $firstname = $_POST['firstname'];
        $phone = $_POST['phone'];
        $address = $_POST['address'];
        $zipcode = $_POST['zipcode'];
        $password = $_POST['password'];

        $user = new Login();
        $user->insertUser($email, $name, $firstname, $phone, $address, $zipcode, $password);

        //Redirection vers le compte utilisateur
        header("Location: " . $_ENV['SITE_URL'] . "?action=account");
    }

    /**Créer un utilisateurpar le dashboard **/

    public function createUserByAdmin()
    {
        $email = $_POST['email'];
        $name = $_POST['name'];
        $firstname = $_POST['firstname'];
        $phone = $_POST['phone'];
        $address = $_POST['address'];
        $zipcode = $_POST['zipcode'];
        $password = $_POST['password'];


        $user = new Login();
        $user->insertUser($email, $name, $firstname, $phone, $address, $zipcode, $password);

        //Redirection vers le compte utilisateur
        header("Location: " . $_ENV['SITE_URL'] . "?action=useradmin");
    }

    /** Mettre à jour les informations de l'utilisateur **/

    public function upgradeUser()
    {
        // Vérification si l'utilisateur est connecté
        session_start();
        if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
            header("Location: " . $_ENV['SITE_URL'] . "?action=login");
            exit;
        }

        $email = $_SESSION['email'];
        $name = $_POST['name'];
        $firstname = $_POST['firstname'];
        $phone = $_POST['phone'];
        $address = $_POST['address'];
        $zipcode = $_POST['zipcode'];
        $password = $_POST['password'];

        try {
            $user = new Login();
            $user->updateUser($email, $name, $firstname, $phone, $address, $zipcode, $password);
        } catch (\Exception $e) {
            echo 'Erreur : ' . $e->getMessage();
            return;
        }

        // Redirection vers le compte utilisateur
        header("Location: " . $_ENV['SITE_URL'] . "?action=account");
    }

    /** Mettre à jour les informations de l'utilisateur par le dashboard **/

    public function upgradeUserByAdmin()
    {
        $email = $_POST['email'];
        $name = $_POST['name'];
        $firstname = $_POST['firstname'];
        $phone = $_POST['phone'];
        $address = $_POST['address'];
        $zipcode = $_POST['zipcode'];


        $user = new Login();
        $user->updateUser($name, $firstname, $phone, $address, $zipcode, $email);

        //Redirection vers le compte utilisateur
        header("Location: " . $_ENV['SITE_URL'] . "?action=useradmin");
    }

    /** Supprimer un utilisateur **/
    public function removeUser()
    {
        // Vérification que l'utilisateur est connecté
        session_start();
        if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin']) {
            header("Location: " . $_ENV['SITE_URL'] . "?action=login");
            exit;
        }

        // Récupération de l'email de l'utilisateur à supprimer
        $email = $_SESSION['email'];

        // Suppression de l'utilisateur
        $user = new Login();
        $user->deleteUser($email);

        // Fermeture de la session et redirection vers la page de login
        session_unset();
        session_destroy();
        header("Location: " . $_ENV['SITE_URL'] . "?action=login");
    }

    /** Supprimer un utilisateur **/
    public function removeUserByAdmin()
    {

        // Récupération de l'email de l'utilisateur à supprimer
        $email = $_POST['email'];

        // Suppression de l'utilisateur
        $user = new Login();
        $user->deleteUser($email);

        header("Location: " . $_ENV['SITE_URL'] . "?action=useradmin");
    }

    /** Récupérer comptes utilisateurs */

    public function obtainAllUsers()
    {
        $user = new Login();
        $users = $user->getAllUsers();

        return $users;
    }
}
