<?php
if($model->modified()) { ?>
    <p class="notice">Page saved.</p>
<?php } ?>

<a id="new" href="/maingate/newpage">+</a>

<div class="grid pages">
    <div class="title">Id</div>
    <div class="title">Title</div>
    <?php 
    if(count($model->pages()) > 0) {
        $alt = "";
        foreach($model->pages() as $page) { ?>
            <div class="col-id <?php echo $alt; ?>"><a href="/maingate/page/<?php echo $page->id(); ?>"><?php echo $page->id(); ?></a></div>
            <div class="col-title <?php echo $alt; ?>"><?php echo $page->title(); ?></div>
            <?php 
            $alt = ($alt == "") ? "alt" : "";
        }
    } else { ?>
        <div class="no-pages">There are no pages.</div>
    <?php } ?>
</div>