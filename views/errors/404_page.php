<?php includeFile('layouts/main.php'); ?>
    <div class="content-center">
        <div>
            <h3>Not Found 404</h3>
            <h3><?php echo $exception->getMessage?></h3>
        </div>
    </div>
<?php includeFile('layouts/footer.php'); ?>