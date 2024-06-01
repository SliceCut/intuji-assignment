<?php if($flashMessage->hasError('success')) { ?>
<div class="alert alert-success">
    <?php echo $flashMessage->success; ?>
</div>
<?php } ?>