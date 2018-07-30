<?php

set_time_limit(10);

function SearchFilm ($film, $genre, $gj, $g, $distrib, $dj, $d, $date, $conn, $limit) {
    $film = TestWords($film, 'titre');
    $stmt = $conn->prepare("SELECT *$g$d FROM film $gj $dj WHERE (film.titre LIKE '%$film%') $genre $distrib $date");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $nb_page = ceil(count($rows)/$limit);
    $start = ($_GET["page"]-1) * $limit;
    $stmt = $conn->prepare("SELECT *$g$d FROM film $gj $dj WHERE (film.titre LIKE '%$film%') $genre $distrib $date ORDER BY film.annee_prod DESC, film.titre LIMIT $start, $limit");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    HtmlInput($rows);
    if($nb_page > 1) {
        HtmlPage($nb_page, $limit);
    }
}

function SearchFilmRand ($conn) {
    $stmt = $conn->prepare("SELECT * FROM film ORDER BY RAND() LIMIT 5");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "
    <h3>Films Aléatoires</h3>
    ";
    HtmlInput($rows);
}

function TestWords($a, $b) {
    if(count(explode(' ', $a)) > 1){
        $array = explode(' ', $a);
        for ($i = 0; $i < count($array); $i++) {
            if($i == 0) {
                $a=$array[$i];
            }
            else {
                $a.="%' OR $b LIKE '%$array[$i]";
            }
        }
    }
    if(count(explode('+', $a)) > 1){
        $array = explode('+', $a);
        for ($i = 0; $i < count($array); $i++) {
            if($i == 0) {
                $a=$array[$i];
            }
            else {
                $a.="%' OR $b LIKE '%$array[$i]";
            }
        }
    }
    return $a;
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
    if(isset($_GET["film"])) {
        $film = $_GET["film"];
    }
    else {
        $film=$_POST["film"];
    }
    if(isset($_GET["genre"])) {
        $genre = "&genre=" . $_GET["genre"];
    }
    elseif(isset($_POST["genre"])) {
        $genre = "&genre=" . $_POST["genre"];
    }
    else {
        $genre = "";
    }
    if(isset($_GET["distrib"])) {
        $distrib = "&distrib=" . $_GET["distrib"];
    }
    elseif(isset($_POST["distrib"])) {
        $distrib = "&distrib=" . $_POST["distrib"];
    }
    else {
        $distrib = "";
    }
    if(isset($_GET["date"])) {
        $date = "&date=" . $_GET["date"];
    }
    elseif(isset($_POST["date"])) {
        $date = "&date=" . $_POST["date"];
    }
    else {
        $date = "";
    }
    echo "
    <div class='center'>
    <div class='pagination'>
    <a href='index.php?page=$previous&film=$film$genre$distrib$date&limit=$limit'>&lsaquo;</a>
    ";
    for($i=1; $i <= $nb_page; $i++){
        if($i == $_GET["page"]){
            $il = "<u style=\"color: red;\">$i</u>";
        }
        else {
            $il = $i;
        }
        echo "
        <a href='index.php?page=$i&film=$film$genre$distrib$date&limit=$limit'>$il</a>
        ";
    }
    echo "
    <a href='index.php?page=$next&film=$film$genre$distrib$date&limit=$limit'>&rsaquo;</a>
    </div>
    </div>
    ";
}

function HtmlInput ($rows) {
    $regex = "/(src=\")(.*?)(\" width)/";
    echo "
    <main>
    ";
    foreach($rows as $row) {
        $titre = $row["titre"];
        $titre = preg_replace("/ /", "+", $titre);
        $html=file_get_contents("https://www.google.com/search?q=" . $titre . "+" . $row["annee_prod"] . "+affiche&tbm=isch&source=lnt&tbs=isz:lt,islt:xga&sa=X&ved=0ahUKEwiJo9v03vHbAhVDaRQKHdL2B7AQpwUIHg&biw=1920&bih=911&dpr=1");
        preg_match($regex, $html, $matches);
        $img = $matches[2];
        $idf = $row["id_film"];
        if(substr($row['resum'], -1) != '.'){
            $row['resum'] .= '...<br/><a href="#">read more</a>';
        }
        if($row['duree_min'] != 0 && isset($row['duree_min'])) {
            $heure = floor($row["duree_min"]/60);
            $min = $row["duree_min"]%60;
            $duree = $heure . "h et " . $min . "min";
        }
        else {
            $duree = 'Inconnue';
        }
        if($row['annee_prod'] != 0 && isset($row['annee_prod'])) {
            $annee = $row['annee_prod'];
        }
        else {
            $annee = 'Inconnue';
        }
        if($row['date_debut_affiche'] != 0 && isset($row['date_debut_affiche'])) {
            $arr = explode('-', $row['date_debut_affiche']);
            $arr = array_reverse($arr);
            $debut = implode('/', $arr);
        }
        else {
            $debut = '?';
        }
        if($row['date_fin_affiche'] != 0 && isset($row['date_fin_affiche'])) {
            $arr = explode('-', $row['date_fin_affiche']);
            $arr = array_reverse($arr);
            $fin = implode('/', $arr);
        }
        else {
            $fin = '?';
        }
        if(isset($row['fgenre'])) {
            $genre = "<u>Genre :</u> " . $row['fgenre'] . "<br/>";
        }
        else {
            $genre = "";
        }
        if(isset($row['fdistrib'])) {
            $distrib = "<u>Distributeur :</u> " . $row['fdistrib'] . "<br/>";
        }
        else {
            $distrib = "";
        }
        echo "<div class='film'>
        <h2>" . $row["titre"] . "<br/></h2>
        <img src='$img'><br/>
        <p><u>ID du film :</u> $idf<br/>
        $genre$distrib
        <u>Durée :</u> " . $duree . "<br/>
        <u>Année de Production</u> : " . $annee . "<br/>
        <u>A l'affiche :</u><br/>Du " . $debut . " au " . $fin . "<br/>
        <u>Résumé :</u> " . $row["resum"] . "<br/></p>
        </div>
        ";
    }
    echo "
    </main>
    ";
    if(count($rows) == 0) {
        echo "<h3> Aucun film ne correspond à la recherche. </h3>";
    }
}

if(isset($_GET["film"])) {
    $_GET["film"] = trim($_GET["film"], ' ');
    $film = preg_replace('/\s\s+/', ' ', $_GET["film"]);
    $_GET["film"] = $film;
    if(isset($_GET['genre'])&& $_GET["genre"] != 'NULL'){
        $genre = "AND (genre.nom LIKE '%" . $_GET['genre'] . "%') AND (genre.id_genre = film.id_genre)";
        $gj = "INNER JOIN genre";
        $g = ", genre.id_genre, genre.nom AS 'fgenre'";
    }
    else {
        $genre = "";
        $gj = "";
        $g = "";
    }
    if(isset($_GET['distrib'])&& $_GET["distrib"] != 'NULL'){
        $distrib = "AND (distrib.id_distrib = film.id_distrib) AND (distrib.nom LIKE '%" . $_GET['distrib'] . "%')";
        $dj = "INNER JOIN distrib";
        $d = ", distrib.id_distrib, distrib.nom AS 'fdistrib'";
    }
    else {
        $distrib = "";
        $dj = "";
        $d = "";
    }
    if(isset($_GET['date'])&& $_GET["date"] != ''){
        $date = "AND ('" . $_GET['date'] . "' BETWEEN film.date_debut_affiche AND film.date_fin_affiche)";
    }
    else {
        $date = "";
    }

    SearchFilm($film, $genre, $gj, $g, $distrib, $dj, $d, $date, $conn, $_GET["limit"]);
}

elseif((isset($_POST["film"]) && $_POST["film"] != "") || (isset($_POST["distrib"]) && $_POST["distrib"] != "") || (isset($_POST["genre"]) && $_POST["genre"] != "") || (isset($_POST["date"]) && $_POST["date"] != "")) {
    if (isset($_POST["film"]) && $_POST["film"] != "") {
    $_POST["film"] = trim($_POST["film"], ' ');
    $film = preg_replace('/\s\s+/', ' ', $_POST["film"]);
    $_POST["film"] = $film;
    }
    else {
        $film = "";
    }
    if(isset($_POST['genre'])&& $_POST["genre"] != 'NULL'){
        $genre = "AND (genre.nom LIKE '%" . $_POST['genre'] . "%') AND (genre.id_genre = film.id_genre)";
        $gj = "INNER JOIN genre";
        $g = ", genre.id_genre, genre.nom AS 'fgenre'";
    }
    else {
        $genre = "";
        $gj = "";
        $g = "";
    }
    if(isset($_POST['distrib'])&& $_POST["distrib"] != 'NULL'){
        $distrib = "AND (distrib.id_distrib = film.id_distrib) AND (distrib.nom LIKE '%" . $_POST['distrib'] . "%')";
        $dj = "INNER JOIN distrib";
        $d = ", distrib.id_distrib, distrib.nom AS 'fdistrib'";
    }
    else {
        $distrib = "";
        $dj = "";
        $d = "";
    }
    if(isset($_POST['date'])&& $_POST["date"] != ''){
        $date = "AND ('" . $_POST['date'] . "' BETWEEN film.date_debut_affiche AND film.date_fin_affiche)";
    }
    else {
        $date = "";
    }

    SearchFilm($film, $genre, $gj, $g, $distrib, $dj, $d, $date, $conn, $_POST["limit"]);
}
else {
    SearchFilmRand($conn);
}

?>