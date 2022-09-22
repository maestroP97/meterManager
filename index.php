<?php
    session_start();
    if(isset($_SESSION['UserName'])){
        header("location:home.php");
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="author" content="Aubin Tchuenkam">
    <meta http-equiv="X-UA-Compatible"content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionnaire de compteurs</title>
    <script>
        addEventListener("load", function () {
            setTimeout(hideURLbar, 0);
        }, false);

        function hideURLbar() {
            window.scrollTo(0, 1);
        }
    </script>

    <link rel="stylesheet" href="css/bootstrap.min.css">

    <style type="text/css">
        /* Make the image fully responsive */
        .carousel-inner img {
            width: 100%;
            height:100vh;
        }
        a{
            color: #fff;
            font-weight: bold;
            font-size: large;
        }
        .navbar{
            background: rgb(0,124,247,0.5);
        }
        footer{
            background: rgb(0,0,0,0.3);
            text-align: center;
            font-size: 12px;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        }
        .myCarousel{
            -webkit-background-size: cover;
            background-position: center center;
            background-size: cover;
            height: 100vh;
            padding: 0px;
            margin: 0px;
        }
        .carousel-caption{
            padding-bottom: 250px;
            font-family: poppins;
        }
        li{
                font-size: 30px;
                text-align: justify;
        }
        .carous3{
            /* color: black; */
            padding-bottom: -100px;
        }
        .carousel-caption h2{
            font-size: 50px;
            font-weight: bold;
        }
        .carousel-caption h3{
            font-size: 50px;
        }
        #alert, #forgot-box, #loader{
            display: none;
        }
        .appliName{
            color: var(color-primary);
        }
        .modal-header{
            background-color: #007CF7 !important;
            color: #fff !important;
        }
    </style>
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-sm navbar-dark fixed-top">
            <div class="container-fluid">
                <a class="navbar-brand" href="index.php">
                    <img src="img/notif.jpg" alt="APD Meter Manager" style="width:40px;">
                    APD Meter Manager
                </a>

                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="" id="loginBtn" data-toggle="modal" data-target="#login-modal">Se connecter</a>
                    </li>
                </ul>
            </div>   
        </nav>

        <div id="myCarousel" class="carousel slide" data-ride="carousel">
            <!-- Indicators -->
            <ul class="carousel-indicators">
                <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
                <li data-target="#myCarousel" data-slide-to="1"></li>
                <li data-target="#myCarousel" data-slide-to="2"></li>
            </ul>

            <!-- The slideshow -->
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="img/3.png">
                    <div class="carousel-caption">
                        <h2>BIENVENUE SUR <span class="appliName" style="color: #4484EB;">APD METER MANAGER</span> <br>
                        votre système de gestion des compteurs</h2>
                    </div>
                </div>
                <!--<div class="carousel-item">
                    <img src="img/4.jpeg">
                    <div class="carousel-caption">
                        <h2>Ce système vous permet entre autre de:
                            <li>Rechercher aisemment un document parmi la multitude enregistrés </li>
                            <li>Sauvegarder et télécharger un document </li>
                            <li>Rester notifier quant aux dates d'échéances des différents documents </li>
                        </h2>
                    </div>
                </div>-->
                <div class="carousel-item">
                    <img src="img/téléchargement.jpeg">
                    <div class="carousel-caption">
                        <!--<h2 class="carous3">Vous pourrez également :</h2>
                            <li class="carous3">Manager les différents comptes utilisateurs en toute simplicité </li>
                            <li class="carous3">Archiver et consulter les documents déjà réglés.</li>
                            <i class="carous3"><h3>Laissez-vous séduire par la simplicité et l'innovation dans la recherche de document.</h3></i>
    -->
                    </div>
                </div>
            </div>
            
            <!-- Left and right controls -->
            <a class="carousel-control-prev" href="#myCarousel" data-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </a>
            <a class="carousel-control-next" href="#myCarousel" data-slide="next">
                <span class="carousel-control-next-icon"></span>
            </a>
        </div>
    </header>


    <script src="css/jquery-3.5.1.min.js"></script>
    <script src="css/popper.min.js"></script>
    <script src="css/bootstrap.min.js"></script>
    <script src="css/jquery.validate.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function(){
            
            $("#login-form").validate();
            $("#forgot-form").validate();

            // Envoie des données de connexion via ajax
            $("#login").click(function(e){
                if (document.getElementById("login-form").checkValidity()){
                    $("#loader").show();
                    e.preventDefault();
                $.ajax({
                        url: "actionController.php",
                        method: "post",
                        data: $("#login-form").serialize() + "&action=login",
                        success:function(response){
                            if(response==='ok'){
                                window.location='home.php';
                                $("#loader").hide();
                            }
                            else
                            {
                                console.log(response);
                                alert("Erreur de connexion, nom d'utilisateur ou mot de passe incorrect. vérifiez vos informations");
                                $("#loader").hide();
                            }
                        }
                    });
                }
                return true;
            });

        });

    </script>
</body>
</html>

 <!-- Formulaire de connexion / Connection form -->
 <div id="login-modal" class="modal fade bg-transparent">
 <div class="modal-dialog">
        <form method="post" id="login-form" class=" p-2" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="modalTitle" class="modal-title">Authentification</h4>
                    <button type="button" class="close text-align-right" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <input type="text" name="user-name" class="form-control" placeholder="Nom d'utilisateur"
                        value="<?php if(isset($_COOKIE['userName'])){ echo $_COOKIE['userName'];} ?>" required minlength="2">
                    </div>
                    <div class="text-center" id="loader">
                        <img src="img/preloader.gif" style="width:50px; height:50px;">
                    </div>
                    <div class="form-group">
                        <input type="password" name="pwd" class="form-control" placeholder="Mot de passe" 
                        value="<?php if(isset($_COOKIE['pwd'])){ echo $_COOKIE['pwd'];} ?>" required minlength="4">
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" name="remember" class="custom-control-input" 
                                <?php if(isset($_COOKIE['userName'])){ ?> checked <?php } ?> id="customCheck">
                            <label for="customCheck" class="custom-control-label">Se souvenir de moi</label>
                            <!-- <a href="#" class="float-right" id="forgot">Mot passe oublié?</a> -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="submit" class="btn btn-primary btn-block" value="Se connecter" name="login" id="login">
                </div>
            </form>
        </div>
    </div>