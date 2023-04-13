<?php

use Locabraz\controllers\controllerClass\ContactController;

try {
    /** Récupérer Controller pour vue front */

    // Vues visiteurs et utilisateurs
    $view = new \Locabraz\controllers\UserController;

    // Vues admin
    $admin = new \Locabraz\controllers\AdminController;

    // Récupérer contrôleur contact
    require_once dirname(__FILE__) . '/controllerClass/Contact.php';





    // Vérifier si l'action est définie
    if (isset($_GET['action'])) {
        switch ($_GET['action']) {

                /** Vues visiteurs */
                //Pages menu
            case 'apartment':
                $view->apartmentPage();
                break;

            case 'news':
                $view->newsPage();
                break;

            case 'contact':
                $view->contactPage();
                break;

            case 'mentions':
                $view->mentionsPage();
                break;

                //Pages utilisateurs
            case 'account':
                $view->accountPage();
                break;

            case 'login':
                $view->loginPage();
                break;

            case 'profile':
                $view->profilePage();
                break;

            case 'register':
                $view->registerPage();
                break;

            case 'booking':
                $view->bookingPage();
                break;

                /** Vues admin */

            case 'dashboard':
                $admin->dashboard();
                break;

            case 'articleadmin':
                $admin->articleAdmin();
                break;

            case 'bookingadmin':
                $admin->bookingAdmin();
                break;

            case 'rentaladmin':
                $admin->rentalAdmin();
                break;

            case 'useradmin':
                $admin->userAdmin();
                break;

                //Formulaire de contact

            case 'form-contact':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $contact = new ContactController;
                    $contact->sendMessage();
                    $view->confirmationPage();
                    break;
                } else {
                    $view->contactPage();
                    break;
                }

            default:
                $view->homePage();
                break;
        }
    } else {
        $view->homePage();
    }
} catch (Exception $e) {
    require_once './src/views/frontpages/404.php';
} catch (Error $e) {
    require_once  './src/views/frontpages/error.php';
}
