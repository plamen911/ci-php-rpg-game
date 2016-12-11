<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container body-content">
    <div class="well">
        <h2>All Planets -
            <a href="<?php echo base_url('planet/create'); ?>" class="btn btn-warning">Create New</a>
        </h2>
        <div class="row">
            <table class="table table-striped table-hover ">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Coordinates</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($planets as $planet): ?>
                    <tr>
                        <td><?php echo $planet->id; ?></td>
                        <td><?php echo html_escape($planet->name); ?></td>
                        <td>X = <?php echo $planet->x; ?>, Y = <?php echo $planet->y; ?></td>
                        <td>
                            <a href="<?php echo base_url('planet/edit/' . $planet->id); ?>" class="btn btn-success btn-xs">Edit</a>
                            <a href="<?php echo base_url('planet/delete/' . $planet->id); ?>" class="btn btn-danger btn-xs">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>