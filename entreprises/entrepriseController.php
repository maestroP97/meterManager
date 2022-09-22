<?php
    require "../connection/sessionController.php";
    require "../connection/dbconnection.php";

    extract($_POST);
    extract($_GET);

    $get = $_GET;
    $post = extract($_POST);

    if(isset($readData))
    {
        $data ='<table class="table table-hover table-condensed" id="tableEntreprise">
                    <thead class=" thead thead-danger">
                        <tr class="table-primary">
                            <th >ID</th>
                            <th >DENOMINATION</th>
                            <th>TYPE UTILISATEUR</th>
                            <th>ADRESSE MAIL</th>
                            <th >TELEPHONE</th>
                            <th >ADRESSE</th>
                            <th >AJOUTE LE</th>
                            <th >AJOUTE PAR</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>';
        // $statut = "Archivé";
        $docList = "SELECT d.id, d.Nom, d.TypeUtilisateur, d.AdresseMail, d.Localisation, d.Telephone, d.CreateAt, u.UserName FROM users u, entreprises d WHERE d.UserId = u.id ;";
        // if($filter=="Archivé")
        // {
        //     $docList = "SELECT d.id, t.id as idType, t.Libelle, d.Description, d.Path, d.CreateAt, d.Deadline, d.Amende, d.Statu, d.Montant, u.UserName FROM users u, documents d, type_documents t WHERE d.Libelle = t.id AND d.CreateBy = u.id AND d.Statu ='$statut' ;";
        // }
        // elseif($filter!="")
        // {            
        //     $docList = "SELECT d.id, t.Libelle, t.id as idType, d.Description, d.Path, d.CreateAt, d.Deadline, d.Amende, d.Statu, d.Montant, u.UserName FROM users u, documents d, type_documents t WHERE d.Libelle = t.id AND d.CreateBy = u.id AND d.id ='$filter' ;";
        // }
        $result = mysqli_query($con, $docList);
        if(mysqli_num_rows($result)>0)
        {
            while($doc =  mysqli_fetch_array($result))
            {
                $data .="<tr id='".$doc['id']."'>

                        <td>".$doc['id']."</td>
                        <td>".$doc['Nom']."</td>
                        <td>".$doc['TypeUtilisateur']."</td>
                        <td>".$doc['AdresseMail']."</td>
                        <td>".$doc['Telephone']."</td>
                        <td>".$doc['Localisation']."</td>
                        <td>".$doc['CreateAt']."</td>
                        <td>".$doc['UserName']."</td>

                        <td width='16%'>
                            <i onclick='editItem(".$doc["id"].")' data-toggle='modal' data-target='#accountModal' data-placement='top' title='Modifier cette entreprise' class='btn btn-info fa fa-pencil' style='font-size:11px;'></i>
                            <i onclick='deleteItem(".$doc["id"].")' data-toggle='tooltip' data-placement='top' title='Supprimer cette entreprise' class='btn btn-danger fa fa-trash' style='font-size:11px;' ></i>
                        </td>
                    </tr>";
            }
        }
        else
        {

        }
        $data .='   </tbody>
                </table>';   
                die($data);
        echo $data;     
    }
    elseif(isset($operation) && isset($Nom) && !empty($Type))
    {
        $Localisation = $con->real_escape_string($Localisation);
        
        if($operation=="add"){
            $cmd = "SELECT id FROM entreprises WHERE Nom = '$Nom' AND Localisation='$Localisation' AND TypeUtilisateur='$Type' AND Telephone='$Telephone'";
            $result = mysqli_query($con,$cmd);
            if(mysqli_num_rows($result)<=0)
            {
                
                $cmd = "INSERT INTO entreprises (Nom, Localisation, TypeUtilisateur, AdresseMail, Telephone, UserId) VALUES ('$Nom','$Localisation','$Type','$Secteur','$Telephone','$idUser')";
                if(mysqli_query($con,$cmd)){
                    $operation="";
                    echo "Entreprise ajoutée!";
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
                echo "Cet entreprise existe déjà";
            }
        }
        elseif ($operation=="edit")     
        {
            $cmd = "SELECT id FROM entreprises WHERE Nom = '$Nom' AND Localisation='$Localisation' AND TypeUtilisateur='$Type' AND AdresseMail='$Secteur' AND Telephone='$Telephone' AND id<>'$idEdit'";
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

                $cmd = "UPDATE entreprises SET Nom='$Nom', Localisation='$Localisation', TypeUtilisateur='$Type', AdresseMail='$Secteur', Telephone='$Telephone', EditAt='$editDate', EditedBy = '$idUser' WHERE id='$idEdit'";
                if(mysqli_query($con,$cmd)){
                    echo " Modification effectuée!";
                }
                else
                {
                    echo "Echec de modification";
                }
            }
            else
            {
                echo "Cet entreprise existe déjà";
            }
        }
    }
    elseif(isset($_POST["idEditItem"])){
        $cmd = "SELECT * FROM entreprises d WHERE d.id=$idEditItem";
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
        $cmd = "SELECT COUNT(id) FROM compteurs WHERE Entreprise='$idDelete'";
        $res = mysqli_query($con,$cmd);
        $fRes = $res->fetch_all(MYSQLI_ASSOC);
        $nbre = $fRes[0]["COUNT(id)"];
        if(intval($nbre)>0)
        {
            $cmd = "DELETE FROM compteurs WHERE Entreprise='$idDelete'";
            if(mysqli_query($con,$cmd))
            {
                $cmd = "DELETE FROM entreprises WHERE id='$idDelete'";
                if(mysqli_query($con,$cmd))
                    echo "Suppression terminée!"; 
                else 
                    echo "Erreur de suppression";
            }
        }
        else
        {
            $cmd = "DELETE FROM entreprises WHERE id='$idDelete'";
            if(mysqli_query($con,$cmd))
                echo "Suppression terminée!"; 
            else 
                echo "Erreur de suppression";
        }
    }
    else
    {
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