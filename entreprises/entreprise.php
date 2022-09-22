<?php
    // require '../connection/sessionController.php';
    require 'entrepriseController.php';
    if(!isset($user)){
        header("location:../index.php");
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionnaire de compteurs</title>

    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- <link rel="stylesheet" href="../css/fontawesome/css/all.css"> -->
    <link rel="stylesheet" href="../css/jquery.dataTables.min.css">

    <style type="text/css">
         body{
            background-image: url(../img/3.png);
            -webkit-background-size: cover;
            background-position: center center;
            background-size: cover;
            height: 100vh;
            padding: 0px;
            margin: 0px;
        }
        .navbar{
            background: rgb(0,124,247,0.5);
        }
        nav a{
            color: #fff;
            font-size: large;
            font-weight: bold;
            /* font-family: poppins; */
        }
        .jumbotron{
            background: rgb(192,230,246,0.7);
        }
        .modal-header{
            background-color: #007CF7 !important;
            color: #fff !important;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-sm navbar-dark">
        <a class="navbar-brand" href="../home.php">
            <img src="../img/notif.jpg" alt="APD Meter manager" style="width:40px;">
            APD Meter Manager
        </a>

        <ul class="navbar-nav ml-auto">

            <li class="nav-item">
                <a class="nav-link" onclick="goToMap()" >Voir la carte</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" id="listDoc" href="../home.php">Compteurs</a>
            </li>

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbardrop" data-toggle="dropdown">
                <img src="../img/avatar.png"  style="width:30px; border-radius:50%;"><?= $usName; ?></a>
                <div class="dropdown-menu text-align-left">
                    <a class="dropdown-item" href="../connection/logout.php">Déconnexion</a>
                </div>
            </li>
        </ul>
    </nav>

    <div class="container-fluid">
        <div class="jumbotron">

            <div class="d-flex justify-content-end">
                <button type="button" id="btnAdd" data-toggle="modal" data-target="#accountModal" class="btn btn-primary">Ajouter une entreprise</button>
            </div>
            <br>
            <div id="tableCompte" class="table-responsive">
            </div>
        </div>
    </div>

    <script src="../css/jquery.min.js"></script>
    <script src="../css/popper.min.js"></script>
    <script src="../css/bootstrap.min.js"></script>

    <script type="text/javascript"  src="../css/jquery.dataTables.min.js"></script>
    <script type="text/javascript"  src="../css/dataTables.buttons.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function(){

            readData();

            $("#btnAdd").click(function(){
                $("#operation").val("add");
                $("#btnSave").val("Ajouter");
                
                $('#nom').val("");
                $('#localisation').val("");
                $('#secteur').val("");
                $('#telephone').val("");
                document.getElementById("modalTitle").innerHTML = "Nouvelle entreprise";
            });

            $("#account-form").submit(function(event){
                var Type = $('#type').val();
                var Secteur = $('#secteur').val();
                var Nom = $('#nom').val();
                var Localisation = $('#localisation').val();
                var Telephone = $('#telephone').val();
                var idEdit = $('#id').val();
                var operation = $('#operation').val();
                var data = {operation:operation, Nom:Nom, Type:Type, Secteur:Secteur, Localisation:Localisation, Telephone:Telephone, idEdit:idEdit};
                if (Nom!=""){
                    $.ajax({
                        url:"entrepriseController.php",
                        type: 'POST',
                        data: data,
                        success: function(rep){
                            alert(rep);
                            readData();
                            $("#accountModal").modal('hide');
                        },
                       error: function(err){
                           alert(err);
                       } 
                    });
                }else{
                    console.log("erreur de donnees");
                }
                event.preventDefault();
            });
        });
        function readData()
        {
            var read = "readData";
            $.ajax({
                url: "entrepriseController.php",
                type: 'post',
                data: {readData: read},

                success: function(data, status){
                    $('#tableCompte').html(data);
                    $('[data-toggle="tooltip"]').tooltip();
                    $("#tableEntreprise").DataTable({
                        "paging": true,
                        "processing":true,
                        "order": [[ 1, "asc" ]]
                    });
                },
                error: function(err){
                    alert(err);
                }
            });
        }

        function editItem(id){
            $("#operation").val("edit");
            $("#id").val(id);
            $("#btnSave").val("Modifier");
            var idEdit = id;
            document.getElementById("modalTitle").innerHTML = "Modification: "+ idEdit;

            $.ajax({
                url: "entrepriseController.php",
                type: "POST",
                data: {idEditItem:idEdit},
                success:function(data,status){
                    var dat = JSON.parse(data);
                    $('#nom').val(dat.Nom);
                    $('#type').val(dat.TypeUtilisateur);
                    $('#secteur').val(dat.AdresseMail);
                    $('#localisation').val(dat.Localisation);
                    $('#telephone').val(dat.Telephone);
                }
            });
        }

        function deleteItem(id){

            var rep = confirm("Cet entreprise ainsi que tous les compteurs qui y sont liés seront supprimer. voulez-vous continuer?");
            if (rep){
                $.ajax({
                    url: "entrepriseController.php",
                    type: 'post',
                    data: {idDelete: id},
                    success:function(data,status){
                        readData();
                    }
                });
            }
        }
        function goToMap(){
            document.cookie ="showId=0;";
            location.href="../map.php"
        }

    </script>
</body>
</html>

<!-- Account form modal -->
<div id="accountModal" class="modal fade">
    <div class="modal-dialog">
        <form id="account-form" class="was-validated" method="POST" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="modalTitle" class="modal-title">Nouvelle entreprise</h5>
                    <button type="button" class="close text-align-right" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">

                    <label>Type</label>
                    <select class='form-control' id='type' required>
                        <option value='Utilisateur amodiateur' selected >Utilisateur amodiateur</option>
                        <option value='Utilisateur portuaire' >Utilisateur portuaire</option>
                    </select>

                    <label>Raison sociale / Dénomination fonctionnelle</label>
                    <input type="text" name="nom" id="nom" class="form-control" required/>
                    
                    <label>Adresse mail</label>
                    <input type="email" class='form-control' id='secteur' />
                    
                    <label>Adresse</label>
                    <input type="text" name="localisation" id="localisation" class="form-control"/>

                    <label>Telephone</label>
                    <input type="number" name="telephone" id="telephone" class="form-control"/>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="id" id="id"/>
                    <input type="hidden" name="operation" id="operation"/>
                    <input type="submit" name="btnSave" id="btnSave" class="btn btn-primary" value="Ajouter" />
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Fermer</button>
                </div>
            </div>
        </form>
    </div>
</div>
