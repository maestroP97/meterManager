<?php 
    $hostName ="localhost"; //addresse du serveur
    $userName ="root"; //Nom d'utilisateur
    $pwd ="";   //Mot de passe pour se connecter au serveur
    $dbName ="meter_manager"; //Nom de la base de données

    $con = new mysqli($hostName,$userName,$pwd,$dbName); //Chaine de connexion à la base de données
    $con->set_charset("utf8");
    if($con->connect_error){
        die("Impossible de se connecter à la base de données (Could not connect to database) ".$con->connect_error);
    }
?>