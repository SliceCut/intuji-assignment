<?php includeFile('layouts/main.php'); ?>
<?php includeFile('layouts/header.php'); ?>
<div class="container mt-5">
    <?php includeFile('flash_message/success_message.php') ?>
    <div class="section-header">
        <h2 class="section-header__title">Event List</h2>
        <div class="btn-groups">
            <a class="btn btn-primary btn-sm" href="<?php echo baseUrl('event/create') ?>">
                Create Event
            </a>
        </div>
    </div>
    <div class="search-form mt-4 mb-4">
        <form action="<?php echo baseUrl("event") ?>" method="get">
            <div class="row">
                <div class="col-md-3 mb-2">
                    <label>Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Search" value="<?php echo oldValue('search', $search ?? ""); ?>">
                    <div class="invalid-error">
                        <?php echo $error->getFirstError('search') ?>
                    </div>
                </div>
                <div class="col-md-3 mb-2">
                    <label>Start Time</label>
                    <input type="datetime-local" name="start_time" class="form-control" placeholder="start time" value="<?php echo oldValue('start_time', $start_time ?? ""); ?>">
                    <div class="invalid-error">
                        <?php echo $error->getFirstError('start_time') ?>
                    </div>
                </div>
                <div class="col-md-3 mb-2">
                    <label>End Time</label>
                    <input type="datetime-local" name="end_time" class="form-control" placeholder="end time" value="<?php echo oldValue('end_time', $end_time ?? ""); ?>">
                    <div class="invalid-error">
                        <?php echo $error->getFirstError('end_time') ?>
                    </div>
                </div>
                <div class="col-md-3 mb-2">
                    <label>Limit</label>
                    <select class="form-control" name="per_page">
                        <?php foreach ($limits as $limit) { ?>
                            <option value="<?php echo $limit["value"]; ?>" <?php echo oldValue("per_page", $per_page) == $limit["value"] ? "selected" : ""; ?>><?php echo $limit["label"]; ?></option>
                        <?php } ?>
                    </select>
                    <div class="invalid-error">
                        <?php echo $error->getFirstError('per_page') ?>
                    </div>
                </div>
                <div class="col-md-3 mb-2">
                    <button type="submit" class="form-control btn btn-secondary">Submit</button>
                </div>
            </div>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <td>Start</td>
                    <td>End</td>
                    <td>Summary</td>
                    <td>Creator</td>
                    <td>eventType</td>
                    <td>Status</td>
                    <td>Action</td>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($events["items"] as $item) { ?>
                    <tr>
                        <td><?php echo $item['start']["dateTime"]; ?> <?php echo $item['end']["timeZone"]; ?></td>
                        <td><?php echo $item['end']["dateTime"]; ?> <?php echo $item['end']["timeZone"]; ?></td>
                        <td><?php echo $item['summary'] ?? "-"; ?></td>
                        <td><?php echo $item['creator']["email"]; ?></td>
                        <td><?php echo $item['eventType']; ?></td>
                        <td><?php echo $item['status']; ?></td>
                        <td>
                            <div class="btn-group">
                                <a class="btn btn-primary btn-sm" href="<?php echo baseUrl('event/edit?id=') . $item['id'] ?>" title="Edit">
                                    Edit
                                </a>
                                <form method="post" action="<?php echo baseUrl('events/delete?id=') . $item['id'] ?>" onsubmit="confirmDelete(event)">
                                    <?php echo formMethod('delete'); ?>
                                    <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                        Delete
                                    </button>
                                </form>
                                <button type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#event_<?php echo $item["id"] ?>">
                                    View
                                </button>
                            </div>
                        </td>
                        <?php includeFile('events/show_modal.php', ["event" => $item]); ?>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <div class="text-right">
            <a href="#" class="mx-2" onclick="handlePrevPagination()">Prev</a>
            <?php if ($next) { ?>
                <a href="#" class="mx-2" onclick="handleNextPagination()">NExt</a>
            <?php } ?>
        </div>
    </div>
</div>
<script>
    function confirmDelete(event) {
        if (!confirm("Are you sure you want to delete this event?")) {
            event.preventDefault();
        }
    }

    function handlePrevPagination() {
        const current = "<?php echo $current; ?>";
        if (current == "" || current == null) {
            window.location.href = "<?php echo baseUrl('event'); ?>";
        } else {
            window.location.href = "<?php echo baseUrl('event'); ?>" + "?current=" + "<?php echo $prev; ?>" + "&next=" + "<?php echo $current; ?>";
        }
    }

    function handleNextPagination() {
        window.location.href = "<?php echo baseUrl('event'); ?>" + "?prev=" + "<?php echo $current; ?>" + "&current=" + "<?php echo $next; ?>";
    }
</script>
<?php includeFile('layouts/footer.php'); ?>