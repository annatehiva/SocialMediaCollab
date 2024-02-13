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
            <!--Sidebar: si c'est mur du propre utilisateur, afficher le input pour publier des posts, 
                si c'est le mur des autres, afficher juste la bienvenue. -->
            <?php
            /**
             * Etape 1: Le mur concerne un utilisateur en particulier
             * La première étape est donc de trouver quel est l'id de l'utilisateur
             * Celui ci est indiqué en parametre GET de la page sous la forme user_id=...
             * Documentation : https://www.php.net/manual/fr/reserved.variables.get.php
             * ... mais en résumé c'est une manière de passer des informations à la page en ajoutant des choses dans l'url
             */
                $userId =intval($_GET['user_id']);
            /**
             * Etape 2: se connecter à la base de donnée
             */
                include 'connectionSql.php';
                /**
                 * Etape 3: récupérer le nom de l'utilisateur
                 */
                $laQuestionEnSql = "SELECT * FROM users WHERE id= '$userId' ";
                $lesInformations = $mysqli->query($laQuestionEnSql);
                $user = $lesInformations->fetch_assoc();
                //@todo: afficher le résultat de la ligne ci dessous, remplacer XXX par l'alias et effacer la ligne ci-dessous
                //echo "<pre>" . print_r($user, 1) . "</pre>";
            //Si c'est le mur du propre utilisateur
            if($userId==$_SESSION['connected_id']){
            ?>
            <aside class="bg">
                <section class='centerCompteProfil'>
                    <div class='profilWall'>
                        <div>
                            <?php include 'imgProfil.php' ?>
                        </div>
                        <div class='fontDesign'>
                            <h3>Bienvenue sur ton mur CielBlogue, <?php echo $_SESSION['connected_alias'] ?></h3>
                            <p>Sur cette page, tu trouveras tous tes posts.</p>
                        </div>
                    </div>
                    <div class='fontDesign'>
                <?php
                         /**
                        * TRAITEMENT DU FORMULAIRE
                        */
                        // Etape 1 : vérifier si on est en train d'afficher ou de traiter le formulaire
                        // si on recoit un champs email rempli il y a une chance que ce soit un traitement
                    $enCoursDeTraitement = isset($_POST['message']);
                    if ($enCoursDeTraitement){
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
                        } else {
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
                            if ( ! $ok || $postContent==""){
                                echo "Impossible d'ajouter le message: " . $mysqli->error;
                            } else{
                                echo "Message posté en tant que :";
                                header("location: wall.php?user_id=".$_SESSION['connected_id']);
                                exit;
                            }
                        }
                    }
                ?>
                    <form action="wall.php?user_id=<?php echo $_SESSION['connected_id'] ?> " method="post">
                        <dl>
                            <dt><label for='message'>Message :</label></dt>
                            <dd><textarea name='message'></textarea></dd>
                        </dl>
                        <!-- <input type="text" class="form-control" required> -->
                        <button  class="buttonWall" [disabled]="message === ''">Envoyer</button>
                        <!-- <input type='submit'> -->
                    </form>
                </div>
                </section>
            </aside>
            <?php } else { ?>
                <!-- si c'est un mur des autres -->
                <aside class="bg">
                    <section class='centerCompteProfil'>
                        <div class='profilWall'>
                            <div>
                                <?php include 'imgProfil.php' ?>
                            </div>
                            <div class='fontDesign'>
                                <h3><?php echo $_SESSION['connected_alias'] ?>, bienvenue sur le mur de <?php echo $user["alias"] ?> ! </h3>
                                <p>Sur cette page, tu trouveras tous ses posts.</p>
                            </div>
                        </div>
                    </section> 
                </aside>
            <?php } ?>

            <!--Afficher les postes-->
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
                    if (!$lesInformations){
                        echo("Échec de la requete : " . $mysqli->error);
                    }
                /**
                 * Etape 4: @todo Parcourir les messsages et remplir correctement le HTML avec les bonnes valeurs php
                 */
                    while ($post = $lesInformations->fetch_assoc()){
                    //echo "<pre>" . print_r($post, 1) . "</pre>";
                    include 'articlePost.php';
                } ?>
                <!-- Le bouton d'abonnement: si c'est le mur de la personne que l'utilisateur n'a pas abonné, afficher le boutton,
                    si c'est le mur de la personne que l'utilisateur a abonné, afficher que "Vous avez bien abonné cet auteure !",
                    si c'est le mur de propre utilisateur, affiche rien. -->
                <div>
                    <?php
                        $requetVerifierAbonnement = 
                            "SELECT followed_user_id, following_user_id 
                            FROM followers 
                            WHERE followed_user_id = $userId AND following_user_id = ".$_SESSION['connected_id'];
                        $verifierAbonnement = $mysqli->query($requetVerifierAbonnement);
                        $informationAbonnement = $verifierAbonnement->fetch_assoc();
                        //si c'est le mur des autres et non abonné 
                        if($userId != $_SESSION['connected_id'] && ! $informationAbonnement){
                    ?>
                            <form action="wall.php?user_id=<?php echo $userId ?>" method="post">
                                <button name="boutonAbonnement">Abonnez-vous !</button>
                            </form>
                    <?php   
                            //si le bouton d'abonnement est clické
                            $abonnementEnCoursDeTraitement = isset($_POST["boutonAbonnement"]);
                            if($abonnementEnCoursDeTraitement){
                                $requetAbonnement = 
                                    "INSERT INTO followers" 
                                    . "(id, followed_user_id, following_user_id) " .
                                    "VALUES(NULL," .$userId. "," .$_SESSION['connected_id'].")";
                                $ajouteAbonnement = $mysqli->query($requetAbonnement);
                                header("location: wall.php?user_id=".$userId);
                                exit;
                            }
                            // si c'est le mur de quelqu'un abonné
                        }else if($informationAbonnement){
                            echo "Vous êtes bien abonnée à cette auteure !";
                        }
                    ?>
                </div>
            </main>
        </div>
    </body>
</html>
