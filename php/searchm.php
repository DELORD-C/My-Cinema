<?php
if(isset($_SESSION["admin"]) && $_SESSION["admin"] == 'cinema2018') {
    function SearchMember ($a, $conn, $limit) {
        $stmt = $conn->prepare("SELECT * FROM fiche_personne INNER JOIN membre WHERE fiche_personne.id_perso = membre.id_fiche_perso AND ((prenom LIKE '%$a%') OR (nom LIKE '%$a%')) ORDER BY nom DESC, prenom DESC");
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $nb_page = ceil(count($rows)/$limit);
        $start = ($_GET["page"]-1) * $limit;
        $stmt = $conn->prepare("SELECT * FROM fiche_personne INNER JOIN membre WHERE fiche_personne.id_perso = membre.id_fiche_perso AND ((prenom LIKE '%$a%') OR (nom LIKE '%$a%')) ORDER BY nom DESC, prenom DESC LIMIT $start, $limit");
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        HtmlInput($rows, $conn);
        if($nb_page > 1) {
            HtmlPage($nb_page, $limit);
        }
    }

    function SearchMembers ($a, $b, $conn, $limit) {
        $stmt = $conn->prepare("SELECT * FROM fiche_personne INNER JOIN membre WHERE fiche_personne.id_perso = membre.id_fiche_perso AND (((prenom LIKE '%$a%') AND (nom LIKE '%$b%')) OR ((prenom LIKE '%$b%') AND (nom LIKE '%$a%'))) ORDER BY prenom DESC");
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        HtmlInput($rows, $conn);
    }

    function HtmlInput ($rows, $conn) {
        $regex = "/(src=\")(.*?)(\" width)/";
        echo "
        <main>
        ";
        foreach($rows as $row) {
            $nom = $row["nom"];
            $prenom = $row["prenom"];
            $ville = $row["ville"];
            $cp = $row["cpostal"];
            $idmembre = $row["id_perso"];
            $date = implode('/', array_reverse(explode('-', (explode(' ' ,$row['date_inscription'])[0]))));
            $id = $row['id_dernier_film'];
            $stmt = $conn->prepare("SELECT * FROM film WHERE id_film = $id");
            $stmt->execute();
            $films = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $titre = ($films[0]["titre"]);
            $datef = ($films[0]["annee_prod"]);
            echo "<div class='unmembre'>
            <h2>" . strtoupper($nom) . " " . ucfirst($prenom) . "<br/></h2>
            <a href='member.php?id=$idmembre'><img src=\"src/profil.png\"></a>
            <p>
            VILLE : $ville<br/>
            Code Postal : $cp<br/>
            Inscrit depuis le : " . $date . "<br/>
            Dernier film vu : $titre ($datef)<br/>
            </div>
            ";
        }
        echo "
        </main>
        ";
        if(count($rows) == 0) {
            echo "<h3> Aucun membre ne correspond à la recherche. </h3>";
        }
    }

    function HtmlPage ($nb_page, $limit) {
        $previous = $_GET["page"]-1;
        if($previous <= 1) {
            $previous = 1;
        }
        $next = $_GET["page"]+1;
        if($next >= $nb_page) {
            $next = $nb_page;
        }
        if(isset($_GET["membre"])) {
            $membre = $_GET["membre"];
        }
        else {
            $membre=$_POST["membre"];
        }
        echo "
        <div class='center'>
        <div class='pagination'>
        <a href='members.php?page=$previous&membre=$membre&limit=$limit'>&lsaquo;</a>
        ";
        for($i=1; $i <= $nb_page; $i++){
            if($i == $_GET["page"]){
                $il = "<u style=\"color: red;\">$i</u>";
            }
            else {
                $il = $i;
            }
            echo "
            <a href='members.php?page=$i&membre=$membre&limit=$limit'>$il</a>
            ";
        }
        echo "
        <a href='members.php?page=$next&membre=$membre&limit=$limit'>&rsaquo;</a>
        </div>
        </div>
        ";
    }

    if(isset($_GET["membre"]) && $_GET["membre"] != "") {
        $a = $_GET["membre"];
        $a = trim($a, ' ');
        $a = preg_replace('/\s\s+/', ' ', $a);
        if (count(explode(' ', $a)) >= 2) {
            $array = explode(' ', $a);
            for ($i = 0; $i < count($array); $i++){
                if($i == 0) {
                    $a = $array[$i];
                }
                else if ($i == 1) {
                    $b = $array[$i];
                }
            }
            SearchMembers ($a, $b, $conn, $_GET["limit"]);
        }
        else {
            SearchMember ($a, $conn, $_GET["limit"]);
        }
    }
    elseif(isset($_POST["membre"]) && $_POST["membre"] != "") {
        $a = $_POST["membre"];
        $a = trim($a, ' ');
        $a = preg_replace('/\s\s+/', ' ', $a);
        if (count(explode(' ', $a)) >= 2) {
            $array = explode(' ', $a);
            for ($i = 0; $i < count($array); $i++){
                if($i == 0) {
                    $a = $array[$i];
                }
                else if ($i == 1) {
                    $b = $array[$i];
                }
            }
            SearchMembers ($a, $b, $conn, $_POST["limit"]);
        }
        else {
            SearchMember ($a, $conn, $_POST["limit"]);
        }
    }
    else {
        echo "
        <main>
        </main>
        <h3>Merci de renseigner le nom et/ou prénom du membre à rechercher.</h3>
        ";
    }
}
else {
    echo "<p class='error'><br/>Accès Refusé</p>";
}

?>