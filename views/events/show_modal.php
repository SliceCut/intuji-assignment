<!-- Modal -->
<div class="modal fade" id="event_<?php echo $event["id"]; ?>" tabindex="-1" role="dialog" aria-labelledby="event_<?php echo $event["id"]; ?>" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Event Detail</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Summary</label>
                    <input type="text" class="form-control" name="summary" value="<?php echo $event["summary"] ?? ""; ?>" readonly>
                    <div class="invalid-error">
                        <?php echo $error->getFirstError('summary') ?>
                    </div>
                </div>
                <div class="form-group">
                    <label>Start Time</label>
                    <input type="text" class="form-control" name="start_time" value="<?php echo $event["start"]["dateTime"] ?? ""; ?>" readonly>
                    <div class="invalid-error">
                        <?php echo $error->getFirstError('start_time') ?>
                    </div>
                </div>
                <div class="form-group">
                    <label>End Time</label>
                    <input type="text" class="form-control" name="end_time" value="<?php echo $event["end"]["dateTime"] ?? ""; ?>" readonly>
                    <div class="invalid-error">
                        <?php echo $error->getFirstError('end_time') ?>
                    </div>
                </div>
                <div class="form-group">
                    <label>Location</label>
                    <input type="text" class="form-control" name="location" value="<?php echo $event["location"] ?? ""; ?>" readonly>
                    <div class="invalid-error">
                        <?php echo $error->getFirstError('location') ?>
                    </div>
                </div>
                <div class="form-group">
                    <label>Creator</label>
                    <input type="text" class="form-control" name="creator_email" value="<?php echo $event["creator"]["email"] ?? ""; ?>" readonly>
                    <div class="invalid-error">
                        <?php echo $error->getFirstError('location') ?>
                    </div>
                </div>
                <div class="form-group">
                    <label>organizer</label>
                    <input type="text" class="form-control" name="organizer_email" value="<?php echo $event["organizer"]["email"] ?? ""; ?>" readonly>
                    <div class="invalid-error">
                        <?php echo $error->getFirstError('location') ?>
                    </div>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <input type="text" class="form-control" name="description" value="<?php echo $event["description"] ?? ""; ?>" readonly>
                    <div class="invalid-error">
                        <?php echo $error->getFirstError('description') ?>
                    </div>
                </div>
                <div class="form-group">
                    <label>Event Type</label>
                    <input type="text" class="form-control" name="eventType" value="<?php echo $event["eventType"] ?? ""; ?>" readonly>
                    <div class="invalid-error">
                        <?php echo $error->getFirstError('description') ?>
                    </div>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <input type="text" class="form-control" name="status" value="<?php echo $event["status"] ?? ""; ?>" readonly>
                    <div class="invalid-error">
                        <?php echo $error->getFirstError('description') ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>