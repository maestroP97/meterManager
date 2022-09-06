<?php 
    require '../connection/dbConnection.php';

    extract($_POST);

    if(isset($read)){
        $cmd = "SELECT * FROM comptes";
        $result = mysqli_query($con,$cmd);
        
        $data ='<table class="table table-hover table-condensed table-primary" id="tableAccount">
                    <thead class=" thead thead-danger">
                        <tr class="table-primary">
                            <th >ID</th>
                            <th >Libellé</th>
                            <th>Administrateur</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>';
        if(mysqli_num_rows($result)>0)
        {
            while($cpte =  mysqli_fetch_array($result))
            {
                $objCpte = json_encode($cpte);
                $data .="<tr>
                        <td>".$cpte['id']."</td>
                        <td>".$cpte['Libelle']."</td>";
                        if(intval($cpte["CanAdministrated"])>0)
                        {
                            $data .="<td>Oui</td>";
                        }
                        else
                        {
                            $data .= "<td>Non</td>";
                        }
                $data .="<td>
                        <i onclick='editItem(".$cpte["id"].")' data-toggle='modal' data-target='#accountModal' class='btn btn-info fas fa-pen'></i>
                        <i onclick='deleteItem(".$cpte["id"].")' data-toggle='tooltip' data-placement='top' title='Supprimer' class='btn btn-danger fa fa-trash'></i>
                        <i onclick='configAccount(".$objCpte.")' data-toggle='modal' data-target='#manageModal' class='btn btn-success fa fa-cog'></i>
                    </td>
                </tr>";
            }
            
        }
        $data .='   </tbody>
                </table>';   
        echo $data;
    }
    elseif(isset($_POST["idDelete"]))
    {
        $cmd = "DELETE FROM comptes WHERE id='$idDelete'";
        if(mysqli_query($con,$cmd))
            echo "Suppression effectuée (Delete successful)!"; 
        else 
            echo "Erreur de suppression (Something wrong)";
    }
    elseif(isset($_POST["operation"]) && isset($_POST["idEdit"]) && $operation=="edit" && $libelle!="")
    {
        $libelle = $con->real_escape_string($libelle);
        if (checkIfExist($con, $libelle, $canAdmin)<1)
        {
            $cmd = "UPDATE comptes set Libelle='$libelle', CanAdministrated='$canAdmin' WHERE id='$idEdit'";
            if(mysqli_query($con,$cmd))
                echo "Modification effectuée (updated successfuly)!"; 
            else 
                echo "Erreur de modification (Something wrong)";
        }
        else
        {
            echo "Cet enregistrement existe déjà";
        }
    }
    elseif(isset($_POST["operation"]) && $operation =="add")
    {
        if (checkIfExist($con, $libelle, "")<1)
        {
            $libelle = $con->real_escape_string($libelle);
            $query = "INSERT INTO comptes (Libelle, CanAdministrated) VALUES ('$libelle', '$canAdmin')";
            if(mysqli_query($con,$query))
            {
                $cmd2 = "SELECT id FROM comptes WHERE Libelle ='$libelle' AND CanAdministrated='$canAdmin'";
                $res = mysqli_query($con,$cmd2);
                $result = mysqli_fetch_array($res);

                $cmd3 = "SELECT * FROM type_documents";
                $res2 = mysqli_query($con,$cmd3);
                if(mysqli_num_rows($res)>0)
                {
                    while($result2 = mysqli_fetch_array($res2))
                    {
                        $idModule = $result2['id'];
                        $idCompte = $result['id'];
                        $cmd3 = "INSERT INTO droits (Module,Compte) VALUES ('$idModule','$idCompte')";
                        mysqli_query($con,$cmd3);
                        echo "Ajout effectuée (Added successfuly)!"; 
                    }
                }
            }
            else 
            {
                echo "Erreur d'enregistrement (Something wrong)";
            }
        }
        else
        {
            echo "Cet enregistrement existe déjà";
        }
    }
    elseif(isset($idEdit))
    {
        $cmd2 = "SELECT * FROM comptes WHERE id ='$idEdit'";
        $res = mysqli_query($con,$cmd2);
        $result = mysqli_fetch_array($res);
        echo json_encode($result);
    }
    elseif(isset($idManage))
    {
        $cmd2 = "SELECT d.id, t.Libelle Module, d.Lire, d.Ajouter, d.Modifier, d.Supprimer FROM droits d, type_documents t WHERE d.Module = t.id AND  d.Compte ='$idManage'";
        $res = mysqli_query($con,$cmd2);
        if(mysqli_num_rows($res)>0)
        {
            $data = "<h4>Compte: ".$libelle."</h4>
                <table class='table table-hover table-secondary' id='tableRight'>
                    <thead class=' thead thead-danger'>
                        <tr class='table-primary'>
                            <th>Type de document</th>
                            <th>Voir</th>
                            <th>Créer</th>
                            <th>Editer</th>
                            <th>Supprimer</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    ";
            while($result = mysqli_fetch_array($res))
            {
                $supprimer = "";
                $modifier = "";
                $ajouter= "";
                $lire = "";
                if (intval($result["Supprimer"])==1)$supprimer ="checked";
                if (intval($result["Lire"])==1)$lire ="checked";
                if (intval($result["Modifier"])==1)$modifier ="checked";
                if (intval($result["Ajouter"])==1)$ajouter ="checked";
                $data .="<tr>
                            <td>".$result['Module']."</td>
                            <td><input type='checkbox' id='rightRead".$result["id"]."' ".$lire."></input></td>
                            <td><input type='checkbox' id='rightAdd".$result["id"]."' ".$ajouter."></input></td>
                            <td><input type='checkbox' id='rightEdit".$result["id"]."' ".$modifier."></input></td>
                            <td><input type='checkbox' id='rightDelete".$result["id"]."' ".$supprimer."></input></td>
                            <td><i onclick='saveRight(".$result["id"].")' data-toggle='tooltip' data-placement='top' title='Valider' class='btn btn-info fa fa-check'></i></td>
                        </tr>
                        ";
            }
            $data .='</tbody>
                </table>';
            echo $data;
        }
    }
    elseif(isset($createData) && isset($readData) && isset($updateData) && isset($deleteData))
    {
        $cmd = "UPDATE droits SET Lire='$readData', Modifier='$updateData', Ajouter='$createData', Supprimer='$deleteData' WHERE id='$idRight'";
        if (mysqli_query($con,$cmd)){
            echo 'Mise à jour des droits du compte effectuée';
        }
        else
        {
            echo 'Erreur de mise à jour';
        }
    }

    function checkIfExist($con, $lib, $canAdmin)
    {
        $lib = $con->real_escape_string($lib);
        $cmd2 = "SELECT * FROM comptes WHERE Libelle ='$lib'";
        if ($canAdmin !="")$cmd2 = "SELECT * FROM comptes WHERE Libelle ='$lib' AND CanAdministrated='$canAdmin'";
        $res = mysqli_query($con,$cmd2);
        return mysqli_num_rows($res);
    }
?>