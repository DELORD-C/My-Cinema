<?php

include_once("php/config.php");

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="Index du site.">
    <meta name="keywords" content="Cinema, My, Films, Distributeur, Abonnement, Durée, Acteurs">
    <title>My Cinema</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB"
        crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="css/reset.css" />
    <link rel="stylesheet" type="text/css" href="css/style.css" />
    <link rel="icon" type="image/png" href="src/favicon.png" />
</head>

<body>

    <header>

        <a href="index.php">
            <img src="src/logo.png" alt="logo">
        </a>
        <div class="search">
            <form action="index.php?page=1" method="post">
                <input type="text" name="film" placeholder="Rechercher un film.">
                <input type="submit" value="Rechercher" class="recherche">
                <p>Ex. : Charlie et la chocolaterie, Interstellar, Inception...</p>
                <a class="btn btn-primary" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
                    Recherche Avancée +
                </a>
                <div class="collapse" id="collapseExample">
                    <div class="card card-body">
                    <select class="advanceds" name="genre" value='NULL'>
                        <option value='NULL'>Genre</option>
                        <?php
                        $stmt = $conn->prepare("SELECT nom FROM genre ORDER BY nom");
                        $stmt->execute();
                        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($rows as $key => $val) {
                            $nom = $val['nom'];
                            echo "
                            <option value='$nom'>$nom</option>
                            ";
                        }
                        ?>
                    </select>
                    <select class="advanceds" name="distrib" value='NULL'>
                        <option value='NULL'>Distributeur</option>
                        <?php
                        $stmt = $conn->prepare("SELECT nom FROM distrib ORDER BY nom");
                        $stmt->execute();
                        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($rows as $key => $val) {
                            $nom = $val['nom'];
                            echo "
                            <option value='$nom'>$nom</option>
                            ";
                        }
                        ?>
                    </select>
                        <div class='limit'>
                        Date de Projection (DD/MM/YYYY) :
                        <input id="adate" type="date" name="date" placeholder="Date de projection">
                        </div>
                        <div class='limit'>
                        Nombre d'éléments affichés (1-100)
                        <input id='limit' type="number" name="limit" value="5" min="1" max="100">
                        </div>
                    </div>
                </div>
                <?php
            if(isset($_SESSION["admin"]) && ($_SESSION["admin"] == 'cinema2018' || $_SESSION["admin"] == 'jolan')) {
                echo "
            </form>
            <form class=\"formmembre\" action=\"members.php?page=1\" method=\"post\">
                <input type=\"text\" name=\"membre\" placeholder=\"Rechercher un membre.\">
                <input type=\"submit\" value=\"Rechercher\" class=\"recherche\">
                <p>Ex. : Eddy Michel, Batman, Mac Rond, Jean-Michel Apeuprès...</p>
                <a class=\"btn btn-primary\" data-toggle=\"collapse\" href=\"#collapseExamples\" role=\"button\" aria-expanded=\"false\" aria-controls=\"collapseExamples\">
                    Recherche Avancée +
                </a>
                <div class=\"collapse\" id=\"collapseExamples\">
                    <div class=\"card card-body\">
                        <div class='limit'>
                        Nombre d'éléments affichés (1-100)
                        <input id='limit' type=\"number\" name=\"limit\" value=\"5\" min=\"1\" max=\"100\">
                        </div>
                    </div>
                </div>";
                }
                echo"
            </form>";
                ?>
        </div>


    </header>