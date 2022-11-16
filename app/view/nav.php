<?php if(count($nav) > 0) {  ?>
<aside class="rounded">
    <nav>
        <ul>
            <?php foreach($nav as $item) { ?>
            <li><a href="<?php echo "/" . $item->route(); ?>"><?php echo $item->label(); ?></a></li>
            <?php } ?>
        </ul>
    </nav>
</aside>
<?php } ?>