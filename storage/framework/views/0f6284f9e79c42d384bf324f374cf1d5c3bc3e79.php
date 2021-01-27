<form action='<?php echo e(route("lipila.collect")); ?>' method="post">
    <?php echo csrf_field(); ?>
    <input type="text" placeholder="msisdn" name="MSISDN">
    <input type="text" placeholder="amount" name="AMOUNT">
    <button type="submit">submit</button>
</form>
<?php /**PATH C:\xampp\htdocs\lipila\resources\views/lipilaPay.blade.php ENDPATH**/ ?>