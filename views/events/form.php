<div class="form-group">
    <label>Summary</label>
    <input type="text" class="form-control" name="summary" value="<?php echo oldValue('summary', $event["summary"] ?? "") ?>">
    <div class="invalid-error">
        <?php echo $error->getFirstError('summary') ?>
    </div>
</div>
<div class="form-group">
    <label>Start Time</label>
    <input type="datetime-local" class="form-control" name="start_time" value="<?php echo oldValue('start_time', $event["start"]["dateTime"] ?? "") ?>">
    <div class="invalid-error">
        <?php echo $error->getFirstError('start_time') ?>
    </div>
</div>
<div class="form-group">
    <label>End Time</label>
    <input type="datetime-local" class="form-control" name="end_time" value="<?php echo oldValue('end_time', $event["end"]["dateTime"] ?? "") ?>">
    <div class="invalid-error">
        <?php echo $error->getFirstError('end_time') ?>
    </div>
</div>
<div class="form-group">
    <label>Location</label>
    <input type="text" class="form-control" name="location" value="<?php echo oldValue('location', $event["location"] ?? "") ?>">
    <div class="invalid-error">
        <?php echo $error->getFirstError('location') ?>
    </div>
</div>
<div class="form-group">
    <label>Description</label>
    <input type="text" class="form-control" name="description" value="<?php echo oldValue('description', $event["description"] ?? "") ?>">
    <div class="invalid-error">
        <?php echo $error->getFirstError('description') ?>
    </div>
</div>
<div class="form-group">
    <button class="btn btn-primary">Submit</button>
</div>