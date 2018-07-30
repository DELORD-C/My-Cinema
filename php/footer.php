        <footer>
            Projet My Cinema &#9400;
            <?php

            if(isset($_SESSION["admin"]) && ($_SESSION["admin"] == 'cinema2018' || $_SESSION["admin"] == 'jolan')) {
                echo "
                <div class='connect'>
                    <a class='gestion' href='gestion.php'>Gestion Personnel</a>
                </div>
                <div class='connect'>
                    <a href='disconnect.php'>Déconnection</a>
                </div>
                </footer>
                ";
            }
            else {
                echo"
                <p>Bienvenue sur My Cinema</p>
                <div class='connect'>
                    <a href=\"connect.php\">Connection Administrateur</a>
                </div>
            </footer>
                ";
            }

            ?>

        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
        crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49"
        crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js" integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T"
        crossorigin="anonymous"></script>
    </body>

</html>