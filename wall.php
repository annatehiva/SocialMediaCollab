<?php
session_start();
?>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>ReSoC - Mur</title>
        <meta name="author" content="Julien Falconnet">
        <link rel="stylesheet" href="style.css"/>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Protest+Revolution&family=Protest+Riot&display=swap" rel="stylesheet">
    </head>
    
    <body class="protest-riot-regular">
    
        <header>
            <?php include 'nav.php' ?>
        </header>
        <div id="wrapper">
            <?php
            /**
             * Etape 1: Le mur concerne un utilisateur en particulier
             * La première étape est donc de trouver quel est l'id de l'utilisateur
             * Celui ci est indiqué en parametre GET de la page sous la forme user_id=...
             * Documentation : https://www.php.net/manual/fr/reserved.variables.get.php
             * ... mais en résumé c'est une manière de passer des informations à la page en ajoutant des choses dans l'url
             */
            $userId =intval($_GET['user_id']);
            ?>
            <?php
            /**
             * Etape 2: se connecter à la base de donnée
             */
                include 'connectionSql.php';
            ?>
            <aside class="bg">
                <?php
                /**
                 * Etape 3: récupérer le nom de l'utilisateur
                 */
                $laQuestionEnSql = "SELECT * FROM users WHERE id= '$userId' ";
                $lesInformations = $mysqli->query($laQuestionEnSql);
                $user = $lesInformations->fetch_assoc();
                //@todo: afficher le résultat de la ligne ci dessous, remplacer XXX par l'alias et effacer la ligne ci-dessous
                //echo "<pre>" . print_r($user, 1) . "</pre>";
                ?>
                
                <section class='centerCompteProfil'>
                    <div class='profilWall'>
                        <div> 
                            <?php include 'imgProfil.php' ?>
                        </div>
                        <div class='fontDesign'>
                            <h3>Bienvenue <?php echo $_SESSION['connected_alias'] ?> sur ton mur CielBlogue !</h3>
                            <p>Sur cette page, tu trouveras tous tes posts.
                        </div>
                    </div>

                    <div class='fontDesign'>
                        </p>
                            <?php
                         /**
                        * TRAITEMENT DU FORMULAIRE
                        */
                        // Etape 1 : vérifier si on est en train d'afficher ou de traiter le formulaire
                        // si on recoit un champs email rempli il y a une chance que ce soit un traitement
                            $enCoursDeTraitement = isset($_POST['message']);
                            if ($enCoursDeTraitement)
                            {
                        // on ne fait ce qui suit que si un formulaire a été soumis.
                        // Etape 2: récupérer ce qu'il y a dans le formulaire @todo: c'est là que votre travaille se situe
                        // observez le résultat de cette ligne de débug (vous l'effacerez ensuite)
                        //echo "<pre>" . print_r($_POST, 1) . "</pre>";
                        // et complétez le code ci dessous en remplaçant les ???
                        //$authorId = $_POST['auteur'];
                            $postContent = $_POST['message'];
                        //$authorId = intval($mysqli->real_escape_string($authorId));
                            $postContent = $mysqli->real_escape_string($postContent);
                            if (empty($postContent)){
                                echo "Erreur";
                            }
                            else{
                        //Etape 4 : construction de la requete
                            $lInstructionSql = "INSERT INTO posts "
                                . "(id, user_id, content, created, parent_id) "
                                . "VALUES (NULL, "
                                . $_SESSION['connected_id'] . ", "
                                . "'" . $postContent . "', "
                                . "NOW(), "
                                // . "'', "
                                . "NULL);"
                                ;
                        // echo $lInstructionSql;
                        // Etape 5 : execution
                            $ok = $mysqli->query($lInstructionSql);
                            if ( ! $ok || $postContent=="")
                            {
                                echo "Impossible d'ajouter le message: " . $mysqli->error;
                            } else
                            {
                                echo "Message posté en tant que :";
                                header("location: wall.php?user_id=".$_SESSION['connected_id']);
                                exit;
                            }
                        }}
                        ?>
                    <form action="wall.php?user_id=<?php echo $_SESSION['connected_id'] ?> " method="post">
                        <dl>
                            <dt><label for='message'>Message :</label></dt>
                            <dd><textarea name='message'></textarea></dd>
                        </dl>
                        <!-- <input type="text" class="form-control" required> -->
                        <button class="buttonWall" [disabled]="message === ''">Envoyer</button>
                        <!-- <input type='submit'> -->
                    </form>
                        </div>
                </section>
            </aside>
            <main>
                <?php
                /**
                 * Etape 3: récupérer tous les messages de l'utilisatrice
                 */
                $laQuestionEnSql = "
                    SELECT posts.content, posts.created, users.alias as author_name,
                    users.id as author_id,
                    COUNT(likes.id) as like_number, GROUP_CONCAT(DISTINCT tags.label) AS taglist
                    FROM posts
                    JOIN users ON  users.id=posts.user_id
                    LEFT JOIN posts_tags ON posts.id = posts_tags.post_id
                    LEFT JOIN tags       ON posts_tags.tag_id  = tags.id
                    LEFT JOIN likes      ON likes.post_id  = posts.id
                    WHERE posts.user_id='$userId'
                    GROUP BY posts.id
                    ORDER BY posts.created DESC
                    ";
                $lesInformations = $mysqli->query($laQuestionEnSql);
                if ( ! $lesInformations)
                {
                    echo("Échec de la requete : " . $mysqli->error);
                }
                /**
                 * Etape 4: @todo Parcourir les messsages et remplir correctement le HTML avec les bonnes valeurs php
                 */
                while ($post = $lesInformations->fetch_assoc())
                {
                    //echo "<pre>" . print_r($post, 1) . "</pre>";
                    ?>
                <?php include 'articlePost.php'?>
                <?php } ?>
            </main>
        </div>
    </body>
</html>