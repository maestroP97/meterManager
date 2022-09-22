<?php
    require 'connection/sessionController.php';
    require 'actionController.php';
    if(!isset($user)){
        header("location:index.php");
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Gestionnaire de compteurs</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="css/bootstrap.min.css">
    <!--<link rel="stylesheet" href="css/fontawesome/css/all.css">-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <link rel="stylesheet" href="css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="css/dataTables.searchHighlight.css">

    <!--Export table button CSS-->
    <link rel="stylesheet" href="css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="css/bootstrap@4.5.2-dist-bootstrap.min">

    <style type="text/css">
        #alertId{
            display: none;
        }
        .dt-button{
            border-radius: 20%;
        }
        body{
            background-image: url(img/3.png);
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
        <a class="navbar-brand" href="home.php">
            <img src="img/notif.jpg" alt="APD Merter Manager" style="width:40px;">
            APD Meter Manager
        </a>

        <ul class="navbar-nav ml-auto">

            <li class="nav-item">
                <a class="nav-link" href="map.php" onclick="goToMap()">Voir la carte</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="entreprises/Entreprise.php">Entreprises</a>
            </li>
            <!--<li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle <?= $canAdmin ?> " href="#" id="navbardrop" data-toggle="dropdown">Entreprises</a>
                <div class="dropdown-menu text-align-left">
                    <a class="dropdown-item">Tout afficher</a>
                    <a href="" class="dropdown-divider"></a>
                    <a class="dropdown-item" href="entreprises/entreprise.php">Ajouter</a>
                </div>
            </li>-->

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbardrop" data-toggle="dropdown">
                    <img src="img/avatar.png"  style="width:30px; border-radius:50%;"><?= $usName; ?></a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item <?= $canAdmin ?> " href="" data-toggle='modal' data-target='#userModal'>Nouvel utilisateur</a>
                    <a href="" class="dropdown-item" data-toggle='modal' data-target='#resetPwdModal'>Mot de passe oublié?</a>
                    <a href="" class="dropdown-divider"></a>
                    <a class="dropdown-item" href="connection/logout.php">Déconnexion</a>
                </div>
            </li>
        </ul>
    </nav>
    <br>
    <div id="alertId">
        <div class="alert alert-success alert-dismissible d-flex justify-content-around">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong id="alertText"></strong>
        </div>
    </div>

    <div class="container-fluid">
        <div class="jumbotron">
            <div class="d-flex justify-content-end">
                <button type="button" id="btnAdd" data-toggle="modal" data-target="#docModal" class="btn btn-primary">Ajouter un compteur</button>
            </div>
            <br>
            <div id="tableContainer" class="table-responsive">
            </div>
        </div>
    </div>

    <iframe id="pdf" name="pdf" style="display: none;"></iframe>

    <script src="css/jquery.min.js"></script>
    <script src="css/popper.min.js"></script>
    <script src="css/bootstrap.min.js"></script>

    <script type="text/javascript"  src="css/jquery.dataTables.min.js"></script>
    <script type="text/javascript"  src="css/dataTables.buttons.min.js"></script>

    <script type="text/javascript"  src="css/dataTables.searchHighlight.min.js"></script>
    <script type="text/javascript"  src="css/jquery.highlight.js"></script>



    <!--Export table buttons-->
    <script type="text/javascript"  src="css/jszip.min.js"></script>
    <script type="text/javascript" src="css/pdfmake.min.js" ></script>
    <script type="text/javascript"  src="css/vfs_fonts.js"></script>
    <script type="text/javascript" src="css/buttons.html5.min.js"></script>
    <script type="text/javascript" src="css/buttons.print.min.js"></script>

    <script src="css/jquery.validate.min.js"></script>

    <?php
        // var_dump($_FILES);
        if(!empty($_FILES['photo']) && $_FILES['photo']["name"]!="")
        {
            $fichName = $_FILES["photo"]["name"];
            $fichName = str_replace(" ","_",$fichName);
            $destination = "uploadFiles/". $fichName;
            $extension = pathinfo($fichName, PATHINFO_EXTENSION);
            $file = $_FILES["photo"]["tmp_name"];
            if(!in_array($extension,["jpg","png","jpeg","JPG","PNG","JPEG"])){
                ?> 
                    <script>
                        alert("Type de fichier non supporté, vous devez sélectionner une image 'jpg', 'png' ou '.jpeg' ");
                    </script>
                <?php
            }
            else
            {
                if(!move_uploaded_file($file,$destination))
                {
                    ?> 
                    <script>
                        alert("Une erreur est subvenue lors de l'importation de l'image, veuillez éditer le compteur enregistré pour recommencer l'importation!");
                    </script>
                    <?php
                }
            }
        }
    ?>
    <script type="text/javascript">

        $(document).ready(function(){
            $("#reset-form").validate();
            $("#doc-form").validate();

            readData();

            $("#btnAdd").click(function(){
                $("#operation").val("add");
                $("#btnSave").val("Ajouter");
                document.getElementById("modalTitle").innerHTML = "Ajouter un compteur";
                var idAccount = <?= $idAccount; ?>;
                $('#libelle').val("");
                $('#lat').val("");
                $('#lon').val("");
                $('#numero').val("");
                $('#code').val("");
                $('#diametre').val("");
                $('#debit').val("");
                $('#consommation').val("");
                $.ajax({
                    url: "compteurs/compteurController.php",
                    type:'post',
                    data: {nouveau:idAccount},
                    success: function(data, status)
                    {
                        document.getElementById("docType").innerHTML=data;
                    }
                });
            });

            $("#cptForm").on("submit",function(){
                var photo = "";
                if(document.getElementById("photo").value){
                    photo = document.getElementById("photo").files[0].name;
                    photo = photo.replace(" ","_");
                }

                var libelle = $('#libelle').val();
                var description = $('#description').val();
                var lat = Number.parseFloat($('#lat').val());
                var lon = Number.parseFloat($('#lon').val());
                var entreprise = $('#entreprise').val();
                var numero = $('#numero').val();
                var status = $('#status').val();
                var code = $('#code').val();
                var secteur = $('#secteur').val();
                var diametre = $('#diametre').val();
                var debit = $('#debit').val();
                var conso = $('#consommation').val();
                var idEdit = $('#id').val();
                var operation = $('#operation').val();
                if (libelle!="" && lat !="" && lon!="" && entreprise!=""){
                    $.ajax({
                        url:"compteurs/compteurController.php",
                        type: 'POST',
                        data: {operation:operation, libelle:libelle, description:description, lat:lat, lon:lon, numero:numero, status:status, photo:photo, entreprise:entreprise, code:code, secteur:secteur, diametre:diametre, debit:debit, consommation:conso, idEdit:idEdit},
                        
                        success: function(data,status){
                            alert(data);
                            readData();
                        },
                        error: function(err){
                            alert(err);
                        }
                    });
                }
                else
                {
                    alert("Veuillez renseigner tous les champs obligatires");
                }
                alert("Operation encours, veuillez cliquez sur 'OK' pour continuer!")
            });
        });

        function readData(opt)
        {
            var readData = "readData";
            $.ajax({
                url: "compteurs/compteurController.php",
                type: 'post',
                data: {readData: readData},

                success: function(data, status){
                    $('#tableContainer').html(data);
                    $('[data-toggle="tooltip"]').tooltip();
                    // $("#tableDoc").DataTable({
                    //     "paging": true,
                    //     "processing":true,
                    //     "order": [[ 4, "asc" ]],
                    //     "dom": 'lBfrtip',
                    //     "buttons": [
                    //         'excel', 'pdf', 'print'
                    //     ]
                    // });
                   
                    $("#tableDoc").DataTable({
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
            $("#btnSave").val("Modifier");
            var idEdit = id;
            document.getElementById("modalTitle").innerHTML = "Modification du compteur: "+ idEdit;

            $.ajax({
                url: "compteurs/compteurController.php",
                type: "POST",
                data: {idEditItem:idEdit},
                success:function(data,status){
                    data = JSON.parse(data);
                    $('#libelle').val(data.Libelle);
                    $('#lat').val(Number.parseFloat(data.Lat));
                    $('#lon').val(Number.parseFloat(data.Lon));
                    $('#entreprise').val(data.Entreprise);
                    $('#description').val(data.Description);
                    $('#numero').val(data.NumeroCompteur);
                    $('#status').val(data.Etat);
                    $('#code').val(data.CodeCompteur);
                    $('#secteur').val(data.Secteur);
                    $('#diametre').val(data.DiametreNominal);
                    $('#debit').val(data.DebitNominal);
                    $('#consommation').val(data.ConsommationMensuelle);
                    document.getElementById("photo").Value= "../uploadFiles/"+data.ImagePath;
                }
            });
        }

        function deleteItem(id){
            var rep = confirm("Ce compteur va être supprimé définitivement. voulez-vous continuer l'opération?");
            if (rep){
                $.ajax({
                    url: "compteurs/compteurController.php",
                    type: 'post',
                    data: {idDelete: id},
                    success:function(data,status){
                        readData();
                    },
                    error: function(err){
                        alert(err);
                    }
                });
            }
        }

        function showItem(meterId){

            if (meterId!=""){
                document.cookie ="showId="+meterId+";";
                location.href="map.php";
            }
        }
        function goToMap(){
            document.cookie ="showId=0;";
        }

        function downloadDoc(fileName){
            // var downloadFile = fileName;
            if(fileName!="")
            {
                var url = "uploadFiles/"+fileName;
                var win = window.open(url, '_blank');
                // $.ajax({
                //     url: "documents/documentController.php",
                //     type: 'POST',
                //     data: {downloadFile: downloadFile},
                //     success: function(data, status){
                //         // readData();
                //         alert(downloadFile);
                //     }
                // });
            }
            else
            {
                alert("Ce document n'a pas été importé");
            }
        }

        function AddUser(){
            var uAccountId = document.getElementById("userAccount").value;
            var uName = $("#userName").val();
            var uPwd = $("#userPwd").val();
            var uCPwd = $("#userConfirmPwd").val();
            var action = "addUser";
            if(uName=="" || uPwd=="" || uCPwd==""){
                alert("Veuillez renseigner tous les champs svp!")
            }
            else if(uPwd == uCPwd)
            {
                $.ajax({
                    url:'actionController.php',
                    type:'post',
                    data: {action:action, uAccountId:uAccountId, userName:uName, pwd:uPwd},
                    success: function(data, status){
                        alert(data);
                        $("#userName").val("");
                        $("#userPwd").val("");
                        $("#userConfirmPwd").val("");
                        $("#userModal").modal('hide');
                    },
                    error: function(err){
                        alert(err);
                    }
                });
            }
            else
            {
                alert("Mot de passe incohérent");
            }
        }
        function resetPwd(){
                var uname = $("#fuName").val();
                var pwd = $("#fpwd").val();
                var cpwd = $("#fcpwd").val();

                if(uname=="" || pwd=="" || cpwd==""){
                    alert("Veuillez renseigner tous les champs svp!");
                }
                else if (pwd==cpwd){
                    $.ajax({
                        url: 'actionController.php',
                        type:'post',
                        data: {userName: uname, pwd:pwd, action:"editUser"},
                        success: function(data, status){
                            $("#fuName").val("");
                            $("#fpwd").val("");
                            $("#fcpwd").val("");
                            $("#resetPwdModal").modal('hide');
                            alert(data);
                        },
                        error:function(err){
                            alert(err);
                        }
                    });
                }
                else
                {
                    alert("Mot de passe incohérent!");
                }
            }

    </script>
</body>
</html>

<!-- Add and Edit form modal -->
<div id="docModal" class="modal fade">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 id="modalTitle" class="modal-title">Nouveau compteur</h4>
                <button type="button" class="close text-align-right" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form method="POST" id="cptForm" class="was-validated" enctype="multipart/form-data">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="entreprise">Entreprise</label>
                            <?= getEnterpriseList($con);?>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="libelle">Libelle du compteur</label>
                            <input type="text" class="form-control" id="libelle">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label for="numero">Numéro du compteur</label>
                            <input type="text" class="form-control" id="numero" required>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="code">Code du compteur</label>
                            <input type="text" class="form-control" id="code" required>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="status">Etat du compteur</label>
                            <select class='form-control' id='status' required>
                                <option value='Bon état' selected >Bon état</option>
                                <option value='Acceptable' >Acceptable</option>
                                <option value='Défectueux' >Défectueux</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="secteur">Secteur</label>
                            <select class='form-control' id='secteur' required>
                                <option value='Secteur 1' selected >Secteur 1</option>
                                <option value='Secteur 2' >Secteur 2</option>
                                <option value='Secteur 3' >Secteur 3</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label for="lat">Latitude</label>
                            <input type="number" class="form-control" id="lat" step="0.0000000001" placeholder="0.0000000000" required>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="lon">Longitude</label>
                            <input type="number" class="form-control" id="lon" step="0.0000000001" placeholder="0.0000000000" required>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="diametre">Diamètre nominal</label>
                            <input type="number" class="form-control" id="diametre" step="0.01" placeholder="0.00">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="debit">Debit nominal</label>
                            <input type="number" class="form-control" id="debit" step="0.01" placeholder="0.00">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label for="consommation">Consommation Mensuelle</label>
                            <input type="number" class="form-control" id="consommation" step="0.01" placeholder="0.00">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="photo">Photographie du compteur</label>
                            <input type="file" name="photo" id="photo" class="form-control"/>
                        </div>
                        <div class="form-group col-md-5">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" ></textarea>
                        </div>
                    </div>
                    <div>
                        <input type="hidden" name="id" id="id"/>
                        <input type="hidden" name="operation" id="operation"/>
                        
                        <button type="button" class="btn btn-danger cancel" data-dismiss="modal">Fermer</button>
                        <input type="submit" name="btnSave" id="btnSave" class="btn btn-primary float-right" value="Ajouter"/>
                    </div>
                </form>
            </div>
            
        </div>
    </div>
</div>

<!-- New user form modal -->
<div id="userModal" class="modal fade">
    <div class="modal-dialog">
        <form id="user-form" class="was-validated" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="notifModalTitle" class="modal-title">Nouvel utilisateur</h4>
                    <button type="button" class="close text-align-right" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <!--<label for="userAccount">Type de compte</label>-->
                    <?= getAccountTypeList($con);?>
                    <label>Nom d'utilisateur</label>
                    <input type="text" name="userName" id="userName" class="form-control" required/>
                    <label>Mot de passe</label>
                    <input type="password" name="userPwd" id="userPwd" class="form-control" required/>
                    <label>Confirmation du mot de passe</label>
                    <input type="password" name="userConfirmPwd" id="userConfirmPwd" class="form-control" required/>
                    <!--<label>Email</label>
                    <input type="email" name="userEmail" id="userEmail" class="form-control"/><br>-->
                </div>
                <div class="modal-footer">
                    <input type="button" onclick="AddUser()" class="btn btn-primary" value="Ajouter" />
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Fermer</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Formulaire de restauration du mot de passe / Reset password form -->
<div id="resetPwdModal" class="modal fade">
    <div class="modal-dialog">
        <form  class="was-validated" id="reset-form" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="resetModalTitle" class="modal-title">Restauration du mot de passe</h4>
                    <button type="button" class="close text-align-right" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <label>Nom d'utilisateur</label>
                    <input type="text" name="fuName" id="fuName" class="form-control" required/>
                    <label>Nouveau mot de de passe</label>
                    <input type="password" name="fpwd" id="fpwd" minlength="4" class="form-control" required/>
                    <label>Confirmation du mot de passe</label>
                    <input type="password" name="fcpwd" id="fcpwd"  minlength="4" class="form-control" required/>
                    <br>
                </div>
                <div class="modal-footer">
                    <input type="button" onclick="resetPwd()" class="btn btn-primary" value="Reinitialiser" />
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Fermer</button>
                </div>
            </div>
        </form>
    </div>
</div>
