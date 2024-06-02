<?php includeFile('layouts/main.php'); ?>
<?php includeFile('layouts/header.php'); ?>
<div class="row d-flex justify-content-center mt-5">
    <div class="col-lg-6 col-xl-6">
        <div class="card rounded-3">
            <div class="card-body p-4 p-md-5">
                <h3 class="mb-4 pb-2 pb-md-0 mb-md-5 px-md-2">Edit Event</h3>
                <?php includeFile('flash_message/success_message.php') ?>
                <form action="<?php echo baseUrl('events/update?id=' . $event["id"]) ?>" method="post">
                    <?php echo formMethod('put') ?>
                    <?php includeFile('events/form.php', [
                        "event" => $event
                    ]) ?>
                </form>
            </div>
        </div>
    </div>
</div>
<?php includeFile('layouts/footer.php'); ?>