<?php includeFile('layouts/main.php'); ?>
    <div class="content-center">
        <div>
            <h3>Exception Error</h3>
            <h3><?php echo $exception->getMessage() ?></h3>
            <h3><?php echo $exception->getCode() ?></h3>
        </div>
    </div>
<?php includeFile('layouts/footer.php'); ?>