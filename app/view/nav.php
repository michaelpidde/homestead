<?php if(count($nav) > 0) {  ?>
<aside class="rounded">
    <nav>
        <ul>
            <?php foreach($nav as $item) { ?>
            <li><?php echo $item; ?></li>
            <?php } ?>
        </ul>
    </nav>
</aside>
<?php } ?>