<?php
echo "<!DOCTYPE html>
<html>

<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <meta http-equiv=\"X-UA-Compatible\" content=\"ie=edge\">
    <meta name=\"description\" content=\"Index du site.\">
    <meta name=\"keywords\" content=\"Cinema, My, Films, Distributeur, Abonnement, Durée, Acteurs\">
    <title>My Cinema</title>
    <link rel=\"stylesheet\" href=\"https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css\" integrity=\"sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB\"
        crossorigin=\"anonymous\">
    <link rel=\"stylesheet\" type=\"text/css\" href=\"css/reset.css\" />
    <link rel=\"stylesheet\" type=\"text/css\" href=\"css/style.css\" />
    <link rel=\"icon\" type=\"image/png\" href=\"src/favicon.png\" />
</head>";
include_once("php/config.php");
    session_destroy();
    echo "<p class = 'error'><br/>Déconnecté</p>
    <meta http-equiv=\"refresh\"  content=\"0;URL=index.php\">";
?>

