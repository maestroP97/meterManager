<?php
    require "../connection/sessionController.php";
    require "../connection/dbconnection.php";

    extract($_POST);
    extract($_GET);

    

    if(isset($_POST["readData"]))
    {
        $data ='<table class="table table-hover table-primary" id="tableDoc">
                    <thead class=" thead thead-danger">
                        <tr class="table-primary">
                            <th >#</th>
                            <th >LIBELLE</th>
                            <th >ENTREPRISE</th>
                            <th >NUMERO COMPTEUR</th>
                            <th >CODE COMPTEUR</th>
                            <th >SECTEUR</th>
                            <th >TYPE</th>
                            <th >ETAT</th>
                            <th >LATITUDE</th>
                            <th >LONGITUDE</th>
                            <th >DIAMETRE NOMINAL (DN)</th>
                            <th >DEBIT NOMINAL (Q)</th>
                            <th >CONSO. MENSUELLE</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>';
        $statut = "Archivé";
        $docList = "SELECT d.id, d.Libelle, e.Nom, d.NumeroCompteur, d.CodeCompteur, d.Secteur, e.TypeUtilisateur, d.Etat, d.Description, d.Lat, d.Lon, d.DiametreNominal, d.DebitNominal, d.ConsommationMensuelle, d.CreateAt, u.UserName FROM users u, compteurs d, entreprises e WHERE d.Entreprise = e.id AND d.UserId = u.id;";
        
        $result = mysqli_query($con, $docList);
        if(mysqli_num_rows($result)>0)
        {
            while($doc =  mysqli_fetch_array($result))
            {
                $color = "table-active";
                // $s = $doc['Status'];
                
                // if ($s=="EnAttente")
                // {
                //     $color = "table-success";
                // }
                // elseif ($s=="Urgent")
                // {
                //     $color = "table-warning";
                // }
                // elseif($s=="Illisible")
                // {
                //     $color = "table-danger";
                // }
                $data .="<tr class='" . $color ."' id='".$doc['id']."'>

                    <td>".$doc['id']."</td>
                    <td>".$doc['Libelle']."</td>
                    <td>".$doc['Nom']."</td>
                    <td>".$doc['NumeroCompteur']."</td>
                    <td>".$doc['CodeCompteur']."</td>
                    <td>".$doc['Secteur']."</td>
                    <td>".$doc['TypeUtilisateur']."</td>
                    <td>".$doc['Etat']."</td>
                    <td>".$doc['Lat']."</td>
                    <td>".$doc['Lon']."</td>
                    <td>".$doc['DiametreNominal']."</td>
                    <td>".$doc['DebitNominal']."</td>
                    <td>".$doc['ConsommationMensuelle']."</td>

                    <td width='16%'>
                        <i onclick='showItem(\"".$doc["id"]."\")' data-toggle='tooltip' data-placement='top' title='Afficher le compteur sur la carte' class='btn btn-secondary fa fa-eye' style='font-size:10px;'></i>
                        <i onclick='editItem(".$doc["id"].")' data-toggle='modal' data-target='#docModal' data-toggle='tooltip' data-placement='top' title='Editer ce compteur' class='btn btn-info fa fa-pencil' style='font-size:10px;'></i>
                        <i onclick='deleteItem(".$doc["id"].")' data-toggle='tooltip' data-placement='top' title='Supprimer le compteur' class='btn btn-danger fa fa-trash' style='font-size:10px;' ></i>
                    </td>
                </tr>";
            }
            
        }
        $data .='   </tbody>
                </table>';   
        echo $data;     
    }
    elseif(isset($operation) && isset($_POST["libelle"]) && !empty($libelle) && isset($_POST["entreprise"]))
    {
        $description = $con->real_escape_string($description);

        if($operation=="add"){
            $cmd = "SELECT id FROM compteurs WHERE  Libelle = '$libelle' AND Entreprise = '$entreprise' AND Lat='$lat' AND Lon = '$lon' AND NumeroCompteur = '$numero' AND CodeCompteur = '$code'";
            $result = mysqli_query($con,$cmd);
            if(mysqli_num_rows($result)<=0)
            {
                $cmd = "INSERT INTO compteurs (Libelle, NumeroCompteur, Etat, Entreprise, Description, Lat, Lon, ImagePath, UserId, CodeCompteur, Secteur, DiametreNominal, DebitNominal, ConsommationMensuelle) VALUES ('$libelle','$numero','$status','$entreprise','$description','$lat','$lon','$photo','$idUser','$code', '$secteur', '$diametre', '$debit', '$consommation')";
                if(mysqli_query($con,$cmd)){
                    echo "Compteur ajouté!";
                }
                else
                {
                    $operation="";
                    echo "Erreur d'enregistrement".$cmd;
                }
            }
            else
            {
                $operation="";
                echo "Ce compteur existe déjà";
            }
        }
        elseif ($operation=="edit")     
        {
            $cmd = "SELECT id FROM compteurs WHERE  Libelle = '$libelle' AND Entreprise = '$entreprise' AND Lat='$lat' AND Lon = '$lon' AND NumeroCompteur = '$numero' AND CodeCompteur = '$code' AND id<>'$idEdit'";
            $result = mysqli_query($con,$cmd);
            if(mysqli_num_rows($result)<=1)
            {
                $cm = "select now() as dateTime;";
                $r = mysqli_query($con,$cm);
                $editDate = "";
                while($doc =  mysqli_fetch_array($r))
                {
                    $editDate = $doc["dateTime"];
                }
                
                $cmd = "UPDATE compteurs SET Libelle = '$libelle', Entreprise = '$entreprise', NumeroCompteur = '$numero', Etat = '$status', Description='$description', Lat='$lat', Lon = '$lon', CodeCompteur='$code', Secteur='$secteur', DiametreNominal='$diametre', DebitNominal='$debit', ConsommationMensuelle='$consommation', EditAt='$editDate', EditedBy = '$idUser', ImagePath='$photo' WHERE id='$idEdit'";
                if($photo=="")$cmd = "UPDATE compteurs SET Libelle = '$libelle', Entreprise = '$entreprise', NumeroCompteur = '$numero', Etat = '$status', Description='$description', Lat='$lat', Lon = '$lon', CodeCompteur='$code', Secteur='$secteur', DiametreNominal='$diametre', DebitNominal='$debit', ConsommationMensuelle='$consommation', EditAt='$editDate', EditedBy = '$idUser' WHERE id='$idEdit'";
                if(mysqli_query($con,$cmd)){
                    echo "Modification effectuée!";
                }
                else
                {
                    echo "Echec de modification";
                }
            }
            else
            {
                echo "Ce compteur existe déjà";
            }
        }
    }
    elseif(isset($_POST["idEditItem"])){
        $cmd = "SELECT * FROM compteurs d WHERE d.id=$idEditItem";
        if(!$result = mysqli_query($con, $cmd)){
            exit(mysqli_error($con));
        }
        $rep= array();

        if(mysqli_num_rows($result)>0)
        {
            $rep = mysqli_fetch_array($result);
        }
        else
        {
            $rep["status"]=200;
            $rep["message"]="Aucune donnée trouvée!";
        }
        echo(json_encode($rep));
    }
    elseif(isset($_POST["idDelete"]))
    {
        $cmd = "DELETE FROM compteurs WHERE id='$idDelete'";
        if(mysqli_query($con,$cmd))
            echo "Suppression effectuée!"; 
        else 
            echo "Erreur de suppression";
    }
    else
    {
        print("Server error");
        $rep["status"]=200;
        $rep["message"]="Invalid Request!";
    }

    function download($name){
        $path = "../uploadFiles/".$name;
        header("Content-Type: application/octect-stream");
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachement; filename=".basename($path));
        header("Expires: 0");
        header("Cache-Control: must-revalidate");
        header("Pragma:public");
        header("Content-Length:". filesize("../uploadFiles/".$name));
        
        readfile(("../uploadFiles/".$name));
        echo "fichier téléchargé";
        // exit;
    }
?>