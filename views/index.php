<?php includeFile('layouts/main.php'); ?>
    <div class="content-center">
        <div>
            <h3>Welcome to Intuji Application</h3>
            <a href="<?php echo baseUrl("oauth/redirect") ?>"><button>Login Gmail</button></a>
        </div>
    </div>
<?php includeFile('layouts/footer.php'); ?>