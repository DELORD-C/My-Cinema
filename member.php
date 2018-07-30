<!DOCTYPE html>
<html>

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<title>Espace Membre</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB"
crossorigin="anonymous">
<link rel="stylesheet" type="text/css" href="css/reset.css" />
<link rel="stylesheet" type="text/css" href="css/style.css" />
<link rel="icon" type="image/png" href="src/favicon.png" />
</head>

<body>
<?php
include_once('php/config.php');
if(isset($_SESSION["admin"]) && $_SESSION["admin"] == 'cinema2018') {
    function CheckPost ($idmembre, $conn) {
        $nom = trim($_POST["nom"]);
        $prenom = $_POST["prenom"];
        $abonnement = $_POST["abonnement"];
        switch ($abonnement) {
            case "VIP":
            $abo="0";
            break;

            case "GOLD":
            $abo="1";
            break;

            case "Classic":
            $abo="2";
            break;

            case "pass day":
            $abo="3";
            break;

            case "malsch":
            $abo="4";
            break;

            default:
            $abo="pass day";
            break;
        }
        $dateabo = $_POST["dateabo"];

        if(preg_match('/[a-zA-Z]{3,30}/', $nom)) {

            $stmt = $conn->prepare("UPDATE fiche_personne SET nom = '$nom' WHERE id_perso = '$idmembre'");
            $stmt->execute();

        }

        else {
            echo "
            <div class='error'>Merci de renseigner un nom valide.</div>
            ";
        }

        if(preg_match('/[a-zA-Z]{3,30}/', $prenom)) {
            $stmt = $conn->prepare("UPDATE fiche_personne SET prenom = '$prenom' WHERE id_perso = '$idmembre'");
            $stmt->execute();
        }
        else {
            echo "
            <div class='error'>Merci de renseigner un prénom valide.</div>
            ";
        }

        $stmt = $conn->prepare("UPDATE membre SET id_abo = '$abo' WHERE id_fiche_perso = '$idmembre'");
        $stmt->execute();

        $stmt = $conn->prepare("UPDATE membre SET date_abo = '$dateabo' WHERE id_fiche_perso = '$idmembre'");
        $stmt->execute();

        if($_POST["addid"] != "") {
            $addid=$_POST["addid"];
            if($_POST["adddate"] != "") {
                $adddate=$_POST["adddate"];
                $addavis="";
                if(isset($_POST["addavis"])) {
                    $addavis=$_POST["addavis"];
                }
                $stmt = $conn->prepare("INSERT INTO historique_membre (id_membre, id_film, date, Avis) VALUES (\"$idmembre\", \"$addid\", \"$adddate\", \"$addavis\")");
                $stmt->execute();
                $stmt = $conn->prepare("UPDATE membre SET id_dernier_film = $addid WHERE id_fiche_perso = $idmembre");
                $stmt->execute();
                $_POST['addfilm']="";
            }
            else {
                echo "
                <div class='error'>Merci de renseigner la date de visionnage.</div>
                ";
            }
        }
    }

    $idmembre = $_GET["id"];

    if(isset($_POST["addid"]) &&_POST["addid"] != "") {
        CheckPost($idmembre, $conn);
    }
    $stmt = $conn->prepare("SELECT *, fiche_personne.nom AS 'nomm', abonnement.nom AS 'nomabo', abonnement.id_abo FROM fiche_personne INNER JOIN membre INNER JOIN abonnement WHERE (id_perso = '$idmembre') AND (id_perso = id_fiche_perso AND abonnement.id_abo = membre.id_abo)");
    $stmt->execute();
    $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $nom = $row[0]["nomm"];
    $prenom = $row[0]["prenom"];
    $abonnement = $row[0]["nomabo"];
    $dateabo = $row[0]['date_abo'];
    $duree_abo = $row[0]['duree_abo'];
    $stmt = $conn->prepare("SELECT * FROM historique_membre WHERE id_membre = $idmembre ORDER BY date DESC");
    $stmt->execute();
    $hist = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "
    <div class='administration'>
    <a class='precedent' href='members.php?page=1&membre=$prenom&limit=5'>Retour</a>
    <form action=\"member.php?id=$idmembre\" method=\"post\" name='form'>
    <table>
    <div class='titre'>
    <u>Fiche Client</u>
    </div>
    <tr>
    <td>NOM</td>
    <td><input type='text' name='nom' value='$nom'></td>
    </tr>
    <tr>
    <td>Prénom</td>
    <td><input type='text' name='prenom' value='$prenom'></td>
    </tr>
    <tr>
    <td>Abonnement</td>
    <td>
    <select name=\"abonnement\" value='$abonnement'>
    <option selected hidden value='$abonnement'>$abonnement</option>
    <option value='VIP'>VIP</option>
    <option value='GOLD'>GOLD</option>
    <option value='Classic'>Classic</option>
    <option value='pass day'>pass day</option>
    <option value='malsch'>malsch</option>
    <option value='Aucun'>Aucun</option>
    </select>
    </td>
    </tr>
    <tr>
    <td>Date d'abonnement</td>
    <td><input type='date' name='dateabo' value=$dateabo></td>
    </tr>
    <tr>
    <td>Durée d'abonnement</td>
    <td>$duree_abo</td>
    </tr>
    <tr>
    <td><input type=submit value='valider'></td>
    </tr>
    </table>
    <div class='titre'>
    <u>Historique films</u>
    </div>
    <table class='historique'>
    <tr class='titres'>
    <td>Film</td>
    <td>Date de visionnage</td>
    <td>Avis</td>
    </tr>
    <tr>
    ";
    if(!isset($_POST["addfilm"]) || $_POST['addfilm'] == "") {
        echo "
        <td>Ajouter un film à l'historique<br/><br/><input type='text' name='addfilm' placeholder='Titre du Film'><br/><br/><input type='submit' class='boutton' value='Recherche'></td>
        <td></td>
        </tr>
        ";
    }
    else {
        $a = $_POST['addfilm'];
        echo "
        <td>Sélectionnez le film<br/>
        <select name='addid'>
        <option value=''></option>
        ";
        $stmt = $conn->prepare("SELECT * FROM film WHERE (titre LIKE '%$a%') ORDER BY annee_prod DESC");
        $stmt->execute();
        $tfilms = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($tfilms as $key => $value) {
            $titre=$value['titre'];
            $id=$value['id_film'];
            echo "
            <option value='$id'>$titre</option>
            ";
        }

        echo "

        <br/><br/><input type='submit' class='boutton' value='Ajouter'></td>
        <td>Sélectionnez la date<br/><input type='date' name='adddate'></td>
        <td><textarea class='textarea' maxlength='205' name='addavis' placeholder=\"Entrez ici l'avis du client concernant le film. (facultatif)\"></textarea></td>
        <input type='hidden' name='addfilm' value='$a'>
        ";
    }


    foreach ($hist as $key => $value) {
        $id = $value["id_film"];
        $date = implode('/', array_reverse(explode('-', (explode(' ' ,$value["date"])[0]))));
        $avis = $value["Avis"];
        $stmt = $conn->prepare("SELECT * FROM film WHERE id_film = $id ORDER BY annee_prod DESC");
        $stmt->execute();
        $films = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $titre=$films[0]['titre'];
        if(isset($_GET["delete"])) {
            if(($_GET["delete"]) == $id) {
                $stmt = $conn->prepare("DELETE FROM historique_membre WHERE id_film = $id AND id_membre = $idmembre");
                $stmt->execute();
                header("Refresh:0");
            }
        }

        echo "
        <tr>
        <td>ID du film : $id<br/>$titre<br/><br/><br/><a class='boutton' href=\"member.php?id=$idmembre&delete=$id\">Supprimer</a></td>
        <td>$date</td>
        <td>$avis</td>
        </tr>
        ";
    }
    echo "
    </table>
    </form>
    </div>
    ";
}

else {
    echo "<p class='error'>Accès Refusé</p>";
}
?>