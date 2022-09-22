<?php
    require '../connection/sessionController.php';
    require 'compteController.php';
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
    <link rel="stylesheet" href="../css/fontawesome/css/all.css">
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
                <a class="nav-link" id="listDoc" href="../home.php">Acceuil</a>
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
                <button type="button" id="btnAdd" data-toggle="modal" data-target="#accountModal" class="btn btn-info">Nouveau type</button>
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
                document.getElementById("modalTitle").innerHTML = "Nouveau type de compte";
            });

            $("#btnSave").click(function(){
                var libelle = $('#libelle').val();
                var canAdmin = 0;
                if (document.getElementById("canAdmin").checked == true){canAdmin =1;}
                var idEdit = $('#id').val();
                var operation = $('#operation').val();
                if (libelle!=""){
                    $.ajax({
                        url:"compteController.php",
                        type: 'post',
                        data: {operation, libelle, canAdmin, idEdit},

                        success: function(data,status){
                            // readData();       
                        }
                    });
                }
            });
        });
        function readData()
        {
            var read = "readData";
            $.ajax({
                url: "compteController.php",
                type: 'post',
                data: {read: read},

                success: function(data, status){
                    $('#tableCompte').html(data);
                    $('[data-toggle="tooltip"]').tooltip();
                    $("#tableAccount").DataTable({
                        "paging": true,
                        "processing":true,
                        "order": [[ 1, "asc" ]]
                    });
                }
            });
        }

        function editItem(id){
            $("#operation").val("edit");
            $("#id").val(id);
            $("#btnSave").val("Modifier (edit)");
            var idEdit = id;
            document.getElementById("modalTitle").innerHTML = "Modification: "+ idEdit;

            $.ajax({
                url: "compteController.php",
                type: "POST",
                data: {idEdit:idEdit},
                success:function(data,status){
                    var dat = JSON.parse(data);
                    $('#libelle').val(dat.Libelle);
                    document.getElementById("canAdmin").checked=Number.parseInt(dat.CanAdministrated)==1;
                }
            });
        }

        function deleteItem(id){

            var rep = confirm("Cet enregistrement va être supprimer. voulez-vous continuer?");
            if (rep){
                $.ajax({
                    url: "compteController.php",
                    type: 'post',
                    data: {idDelete: id},
                    success:function(data,status){
                        readData();
                    }
                });
            }
        }
        function configAccount(obj)
        {
            $.ajax({
                url: "compteController.php",
                type: 'post',
                data: {idManage: obj.id, libelle:obj.Libelle},
                success:function(data,status){
                    $('#rightData').html(data);
                }
            });
        }
        function saveRight(idRight)
        {
            var d =0; if(document.getElementById("rightDelete"+idRight).checked) d=1;
            var e =0; if(document.getElementById("rightEdit"+idRight).checked) e=1;
            var a =0; if( document.getElementById("rightAdd"+idRight).checked) a=1;
            var r =0; if( document.getElementById("rightRead"+idRight).checked) r=1;

            $.ajax({
                url: "compteController.php",
                type:'post',
                data: {createData:a, readData:r, updateData:e, deleteData:d, idRight:idRight},
                success:function(data,status){
                    alert(data);
                }
            });
        }
    </script>
</body>
</html>

<!-- Account form modal -->
<div id="accountModal" class="modal fade">
    <div class="modal-dialog">
        <form method="post" id="account-form" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="modalTitle" class="modal-title">Nouveau type de compte (new account type)</h5>
                    <button type="button" class="close text-align-right" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <label>Libellé (title)</label>
                    <input type="text" name="libelle" id="libelle" class="form-control" required/>
                    <div class="form-check">
                        <input type="checkbox" name="canAdmin" id="canAdmin" class="form-check-input"/>
                        <label class="form-check-label" for="canAdmin">Peut administrer (is and administrator)</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="id" id="id"/>
                    <input type="hidden" name="operation" id="operation"/>
                    <input type="submit" name="btnSave" id="btnSave" class="btn btn-primary" value="Ajouter (add)" />
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Fermer (close)</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Manage account's right form modal -->
<div id="manageModal" class="modal fade">
    <div class="modal-dialog">
        <form method="post" id="manage-form" enctype="multipart/form-data">
            <div class="modal-content modal-dialog-scrollable"  style="height: 600px;">
                <div class="modal-header">
                    <h5 id="modalTitleManage" class="modal-title">Gestion des droits d'accès</h5>
                    <button type="button" class="close text-align-right" data-dismiss="modal">&times;</button>
                </div>
                <p style="padding: 2px; margin: 5px; font-size:15px; font-style:italic; color:chocolate">Pour chaque type de document, cochez ou décochez une case pour octroyer ou révoquer le droit; puis, cliquez sur validé pour enregistrer les modifications.</p>
                <div class="modal-body" id="rightData">
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </form>
    </div>
</div>