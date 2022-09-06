<?php
    session_start();
    require "dbConnection.php";

    $user= $_SESSION['UserName'];
    $stmt= $con->prepare("SELECT u.id, u.UserName, u.Pwd, u.Compte as idCompte, c.Libelle as LibCompte, c.CanAdministrated FROM users u, comptes c WHERE u.Compte=c.id AND UserName=?");
    $stmt->bind_param('s',$user);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_array(MYSQLI_ASSOC);

    $idUser = $res["id"];
    $usName = $res["UserName"];
    $pwd = $res["Pwd"];
    $idAccount = $res["idCompte"];
    $accountType = $res["LibCompte"];
    $canAdmin = "invisible";
    $canSee ="";
    if ($res["CanAdministrated"]==1) $canAdmin="";
    
    $cmd = "SELECT * FROM comptes WHERE id='$idAccount'";


?>