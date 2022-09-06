<?php 
    require "connection/dbConnection.php";

    extract($_POST);

    if(isset($_POST["action"]) && $_POST["action"] == "login" ){
        session_start();

        $usName = $_POST["user-name"];
        $pwd = sha1($_POST["pwd"]);

        $stmt = $con->prepare("SELECT * FROM users WHERE UserName=? AND Pwd=?");
        $stmt->bind_param('ss',$usName,$pwd);
        $stmt->execute();
        $getUser = $stmt->fetch();
        if($getUser!=null){
            $_SESSION['UserName']=$usName;
            echo 'ok';

            if(!empty($_POST['remember'])){
                setcookie('userName',$_POST['user-name'],time()+(10*365*24*60*60));
                setcookie('pwd',$_POST['pwd'],time()+(10*365*24*60*60));
            }
            else
            {
                if(isset($_COOKIE['userName'])){
                    setcookie('userName',"");
                }
                if(isset($_COOKIE['pwd'])){
                    setcookie('pwd',"");
                }
            }
        }
        else
        {
            echo 'Erreur de connexion! vérifiez vos informations';
        }
    }
    else if(isset($action) && $action == "addUser" )
    {
        $userName = check_input($userName);
        $pwd = check_input($pwd);
        $pwd = sha1($pwd);
        
        $query = $con->prepare("SELECT UserName FROM users WHERE UserName=?");
        $query->bind_param('s',$userName);
        $query->execute();
        $result = $query->get_result()->fetch_array(MYSQLI_ASSOC);
        if($result["UserName"]==$userName){
            $action == "";
            echo('Nom d\'utilisateur invalide');
        }
        else
        {
            $stmt = "INSERT INTO users (UserName, Pwd, Compte) VALUES('$userName','$pwd','$uAccountId')";
            
            if(mysqli_query($con,$stmt)){
                $action == "";
                echo('Utilisateur enregistré!');
            }
            else
            {
                $action == "";
                echo('Erreur d\'enregistrement, veuillez recommencer');
            }
        }
    }
    else if(isset($_POST["userName"]) && $action == "editUser")
    {
        $stmt = "SELECT * FROM users WHERE UserName='$userName'";
        $res = mysqli_query($con,$stmt);
        if(mysqli_num_rows($res)>0){
            $pwd = check_input($pwd);
            $pwd = sha1($pwd);
            $query = "UPDATE users SET Pwd='$pwd' WHERE UserName='$userName'";
            if (mysqli_query($con,$query))
            {
                echo'Mot de passe reinitialisé';
            }
            else
            {
                echo'Erreur de modification';
            }
        }
    }
    else{
        // echo "Erreur de données";
    }

    function getEnterpriseList($con)
    {
        $cmd = "SELECT * FROM entreprises";
        $res = mysqli_query($con,$cmd);
        
        $liste ="<select class='form-control' id='meterEnteprise' required>";
        if (mysqli_num_rows($res)>0){
            while($result = mysqli_fetch_array($res)){
                $liste .="<option value='".$result['id']."' >".$result["Nom"]."</option>";
            }
        }
        $liste .="</select>";
        return $liste;
    }

    function getAccountTypeList($con)
    {
        $cmd = "SELECT * FROM comptes";
        $res = mysqli_query($con,$cmd);
        
        $liste ="<select hidden class='form-control' id='userAccount'>";
        if (mysqli_num_rows($res)>0){
            while($result = mysqli_fetch_array($res)){
                $liste .="<option value='".$result['id']."' >".$result["Libelle"]."</option>";
            }
        }
        $liste .="</select>";
        return $liste;
    }

    function check_input($data){
        $data=trim($data);
        $data=stripslashes($data);
        $data=htmlspecialchars($data);
        return $data;
    }
?>