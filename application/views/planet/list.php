<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container body-content">
    <div class="well">
        <h2>All Planets</h2>
        <div class="row">
            <ul class="list-group">
                <?php foreach ($planets as $planet): ?>
                    <?php $isActive = $planet_id == $planet->planet_id; ?>
                    <li class="list-group-item<?php echo (($isActive) ? ' list-group-item-info' : ''); ?>">
                        <?php if (!$isActive) : ?>
                            <div class="pull-right">
                                <a href="<?php echo site_url('/planet/activate/' . $planet->planet_id); ?>" class="btn btn-warning">Set Active</a>
                                <a href="<?php echo site_url('/planet/delete/' . $planet->planet_id); ?>" class="btn btn-danger delete-planet">Delete</a>
                            </div>
                        <?php endif; ?>
                        <h4 class="list-group-item-heading"><?php echo get_planet_name($planet); ?></h4>
                        <p class="list-group-item-text">Coordinates: [<?php echo $planet->x; ?>:<?php echo $planet->y; ?>]</p>
                    </li>
                <?php endforeach; ?>
            </ul>
            <?php echo form_open('/planet/create'); ?>
                <input type="submit" value="New Planet" class="btn btn-warning">
            </form>
        </div>
    </div>
</div>

<script>
    $(function () {
        $('.delete-planet').bind('click', function (e) {
            if (!confirm('Are you sure you want to delete this planet?')) {
                e.preventDefault();
            }
        });
    });
</script>
