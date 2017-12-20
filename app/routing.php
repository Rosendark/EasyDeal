<?php
//***************************************
// Montage des controleurs sur le routeur

$app->mount("/", new App\Controller\IndexController($app));
$app->mount("/", new App\Controller\FooterController($app));


$app->mount("/produit", new App\Controller\ProduitController($app));
$app->mount("/connexion", new App\Controller\CompteController($app));
$app->mount("/Client", new App\Controller\ClientController($app));
$app->mount("/vendeur", new App\Controller\VendeurController($app));
$app->mount("/panier", new App\Controller\PanierController($app));
$app->mount("/reservation", new App\Controller\ReservationController($app));