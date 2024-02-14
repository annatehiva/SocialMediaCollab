<article>
    <h3>
        <time><?php echo $post['created'] ?></time>
    </h3>
    <a href="wall.php?user_id=<?php echo $post['author_id'] ?>">
        <address><?php echo "par ".$post['author_name'] ?></address>
    </a>
    <div>
        <p><?php echo $post['content'] ?></p>
    </div>
    <footer>
        <form action="wall.php?user_id=<?php echo $userId ?>" method="post">
            <input type="hidden" name="post_id" value="<?php echo $post['id'] ?>">
            <button type="submit" name="likeButton">♥</button>
            <small><?php echo $post['like_number'] ?></small>
        </form>
        <a href=""><?php echo " # ".$post['taglist'] ?></a>,
    </footer>
</article>

<?php
include 'connectionSql.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postId = $_POST['post_id'];
    $userId = $_SESSION['connected_id'];

// vérifie si ligne de like est déjà existante
    $checkQuery = "SELECT * FROM likes2 WHERE user_id = $userId AND post_id = $postId";
    $checkResult = $mysqli->query($checkQuery);

    if ($checkResult && $checkResult->num_rows > 0) {
// délike/supprime si ligne déjà existante dans db
        $deleteQuery = "DELETE FROM likes2 WHERE user_id = $userId AND post_id = $postId";
        $mysqli->query($deleteQuery);
    } else {
    //  like/insert si ligne non existante
        $insertQuery = "INSERT INTO likes2 (user_id, post_id) VALUES ($userId, $postId)";
        $mysqli->query($insertQuery);
    }

    // Redirect back to the wall page
    header("Location: wall.php?user_id=1");
    exit;
}
?>

