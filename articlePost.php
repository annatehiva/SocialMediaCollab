<article>
    <h3>
        <time><?php echo $post['created'] ?></time>
    </h3>
    <a href="wall.php?user_id=<?php echo $post['author_id'] ?>">
        <address class="nameLink"><?php echo "par ".$post['author_name'] ?></address>
    </a>
    <div>
        <p><?php echo $post['content'] ?></p>
    </div>
    <footer>
        <form method="post">
            <small><?php echo $post['like_number'] ?></small>
            <button name="likeButton<?php echo $post['id'] ?>">♥</button>
        <?php
            $likeEnCoursTraitement = isset($_POST["likeButton".$post['id']]);
            if ($likeEnCoursTraitement) {
                $postId=$post['id'];
                $connectedId = $_SESSION['connected_id'];
            // vérifie si ligne de like est déjà existante
                $checkQuery = "SELECT * FROM likes2 WHERE user_id =$connectedId AND post_id =$postId" ;
                $checkResult = $mysqli->query($checkQuery);
                $fetchResult = $checkResult->fetch_assoc();
                if ($fetchResult) {
                // délike/supprime si ligne déjà existante dans db
                    $deleteQuery = "DELETE FROM likes2 WHERE user_id =$connectedId AND post_id =$postId";
                    $mysqli->query($deleteQuery);
                } else {
                //  like/insert si ligne non existante
                    $insertQuery = "INSERT INTO likes2 (user_id, post_id) VALUES ('$connectedId', '$postId')";
                    $mysqli->query($insertQuery);
                }
                // Redirect back to the wall page
            }
        ?> 
        </form>
        <a class="nameLink" href=""><?php echo " # ".$post['taglist'] ?></a>
    </footer>
</article>



