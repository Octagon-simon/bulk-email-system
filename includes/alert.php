<?php
if (
    isset($status['success']) && is_bool($status['success'])
    && isset($status['msg']) && !empty($status['msg'])
) {
    if ($status['success'] === true):
?>
<p class="alert alert-success mb-3 radius-0 text-center">
    <?php print($status['msg']); ?>
</p>
<?php elseif ($status['success'] === false):

        ?>
<p class="alert alert-danger mb-3 radius-0 text-center">
    <?php print($status['msg']); ?>
</p>
<?php endif;
}
        ?>