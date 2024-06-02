<?php if($flashMessage->has('success')) { ?>
<div class="alert alert-success">
    <?php echo $flashMessage->success; ?>
</div>
<?php } ?>

<?php if($flashMessage->has('error')) { ?>
<div class="alert alert-danger">
    <?php echo $flashMessage->error; ?>
</div>
<?php } ?>