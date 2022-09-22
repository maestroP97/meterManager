
<?php
     
    require 'connection/sessionController.php';
    require 'actionController.php';
    if(!isset($user)){
        header("location:index.php");
    }
    $filter = 0; 
    if($_COOKIE['showId']){
        $filter = $_COOKIE["showId"];
    }

    // print($filter);

    $docList = "SELECT d.id, d.Libelle, e.Nom, d.NumeroCompteur, d.CodeCompteur, d.Secteur, e.TypeUtilisateur, d.Etat, d.Description, d.Lat, d.Lon, d.ImagePath FROM compteurs d, entreprises e WHERE d.Entreprise = e.id;";
        
    $result = $con->query($docList);
    
    while( $row = $result->fetch_assoc() ){
        $name = $row['Libelle']; 
        $longitude = $row['Lon'];                              
        $latitude = $row['Lat'];
        $img = $row['ImagePath'];
        $state = $row['Etat'];
        $Type = $row['TypeUtilisateur'];
        $id = $row['id'];
        $sect = $row['Secteur'];
        $select=1;
        if($filter==$id) $select=0;
        /* Each row is added as a new array */
        $locations[]=array( 'name'=>$name, 'lat'=>$latitude, 'lng'=>$longitude, 'img'=>$img, 'etat'=>$state, 'type'=>$Type, 'selected'=>$select, 'secteur'=>$sect );
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionnaire de compteurs</title>

    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    

    <style type="text/css">
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
        #map{
            height:90%;
            width:100%;
        }
    </style>
    <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=AIzaSyDrMTfBa9NXyO3izpTE1hrR96YGxmMin4g"></script> 
</head>
<body>
    <nav class="navbar navbar-expand-sm navbar-dark">
        <a class="navbar-brand" href="home.php">
            <img src="img/notif.jpg" alt="APD Meter manager" style="width:40px;">
            APD Meter Manager
        </a>

        <ul class="navbar-nav ml-auto">

            <li class="nav-item">
                <a class="nav-link" id="listDoc" href="home.php">Compteurs</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="entreprises/Entreprise.php">Entreprises</a>
            </li>

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbardrop" data-toggle="dropdown">
                <img src="img/avatar.png"  style="width:30px; border-radius:50%;"><?= $usName; ?></a>
                <div class="dropdown-menu text-align-left">
                    <a class="dropdown-item" href="connection/logout.php">Déconnexion</a>
                </div>
            </li>
        </ul>
    </nav>
    
    <div class="container-fluid">
        <div class="row">
            <div class="col-3">
                <label style="font-weight: bold; font-size: medium;">Afficher les wayspoints du</label>
            </div>
            <div class="col-3">
                <label for="secteur1"> Secteur 1</label>
                <input type="checkbox" onchange="showPoints()" name="secteur1" id="secteur1" checked>
            </div>
            <div class="col-3">
                <label for="secteur2"> Secteur 2</label>
                <input type="checkbox" onchange="showPoints()" name="secteur2" id="secteur2" checked>
            </div>
            <div class="col-3">
                <label for="secteur3"> Secteur 3</label>
                <input type="checkbox" onchange="showPoints()" name="secteur3" id="secteur3" checked>
            </div>
        </div>
    </div>
    <div id="map"></div>

    <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=AIzaSyB6NNLu6LlwYSihd463rNqvgzFDej9gVhs"></script> 
    <script type="text/javascript">
        var map;
        var Markers = {};
        var infowindow;        
        var locations = [
            <?php for($i=0;$i<sizeof($locations);$i++){ $j=$i+1;?>
            [
                '<?php if($locations[$i]['etat']=="Bon état") {echo $locations[$i]['name'];} else {echo $locations[$i]['name']." - ".$locations[$i]['etat'];} ?>',

                // '<h2 class="label">Here is direction of Store <br> <img style="width:250px;" src="img/3.png"/></h2>',
                '<h4 class="label"> <?php echo $locations[$i]['name']." - ".$locations[$i]['etat'];?></h4> <br> <img id="myImg" src="<?php echo 'uploadFiles/'.$locations[$i]['img'];?>" alt="Aucune image" width="500" height="300"><div id="myModal" class="modal"><img class="modal-content" id="img01"><div id="caption"></div></div>',
                <?php echo $locations[$i]['lat'];?>,
                <?php echo $locations[$i]['lng'];?>,
                <?php echo $locations[$i]['selected'];?>,
                '<?php echo $locations[$i]['etat'];?>',
                '<?php echo $locations[$i]['type'];?>',
                '<?php echo $locations[$i]['secteur'];?>'
            ]<?php if($j!=sizeof($locations)) echo ","; }?>
        ];
        console.log(locations);
        var origin = new google.maps.LatLng("4.06199", "9.69965");
        function initialize(locations) {
            var mapOptions = {
                zoom: 16,
                center: origin,
                mapTypeId: 'satellite'
            };
            map = new google.maps.Map(document.getElementById('map'), mapOptions);
            infowindow = new google.maps.InfoWindow();
            for(i=0; i<locations.length; i++) {
                var position = new google.maps.LatLng(locations[i][2], locations[i][3]);
                var marker = new google.maps.Marker({
                    position: position,
                    map: map,
                    label: {
                        color: 'white',
                        text: locations[i][0],
                    }
                    
                });
                if(locations[i][5]!="Défectueux"){
                    if(locations[i][6]=="Utilisateur portuaire"){
                        marker.setIcon('img/green-dot.png');
                    }
                    else if(locations[i][6]=="Utilisateur amodiateur"){
                        marker.setIcon('img/blue-dot.png');
                    }
                }
                google.maps.event.addListener(marker, 'click', (function(marker, i) {
                    return function() {
                        infowindow.setContent(locations[i][1]);
                        // infowindow.setOptions({maxWidth: 200});
                        infowindow.open(map, marker);
                    }
                }) (marker, i));
                Markers[locations[i][4]] = marker;
            }
            locate(0);
        }
        function locate(marker_id) {
            var myMarker = Markers[marker_id];
            var markerPosition = myMarker.getPosition();
            map.setCenter(markerPosition);
            google.maps.event.trigger(myMarker, 'click');
        }
        google.maps.event.addDomListener(window, 'load', initialize(locations));

        function showPoints(){
            var sect1 = document.getElementById('secteur1').checked;
            var sect2 = document.getElementById('secteur2').checked;
            var sect3 = document.getElementById('secteur3').checked;

            var newList = [];
            var tabSec1 = [];
            var tabSec2 = [];
            var tabSec3 = [];

            if(sect1==true)
                tabSec1 = locations.filter(l=> {if(l[7]=="Secteur 1") return l});
            if(sect2==true)
                tabSec2 = locations.filter(l=> {if(l[7]=="Secteur 2") return l});
            if(sect3==true)
                tabSec3 = locations.filter(l=> {if(l[7]=="Secteur 3") return l});
            newList = tabSec1.concat(tabSec2).concat(tabSec3);
            
            initialize(newList);
        }
    </script>
</body>
</html>
