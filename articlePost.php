
    <article>
        <h3>
            <time><?php echo $post['created'] ?></time>
        </h3>
            <address><?php echo "par " .$post['author_name'] ?></address>
        <div>
            <p><?php echo $post['content'] ?></p>
        </div>
        <footer>
            <small>♥ <?php echo $post['like_number'] ?> </small>
            <a href=""><?php echo " # ".$post['taglist'] ?></a>,
        </footer>
    </article>
