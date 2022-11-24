<form method="post" action="/maingate/page/<?php echo $model->selectedPage()->id(); ?>">
    <label for="stub">Stub</label>
    <input type="text" name="stub" value="<?php echo $model->selectedPage()->stub(); ?>" />

    <label for="title">Title</label>
    <input type="text" name="title" value="<?php echo $model->selectedPage()->title(); ?>" />

    <div class="toolbar">
        <img id="showHelp" class="tool" src="/images/icon-book.gif" title="Show help" />
        <img id="insertMedia" class="tool" src="/images/icon-image.gif" title="Insert media" />
    </div>
    <textarea name="content" id="pageContent" spellcheck="false"><?php echo $model->selectedPage()->content(); ?></textarea>

    <?php 
    if(count($model->pages()) > 0) {
        $selected = $model->selectedPage()->parentId();
    ?>
        <label for="parentId">Parent</label>
        <select name="parentId" id="parentId">
            <option value="" selected>Select...</option>
            <?php foreach($model->pages() as $page) { ?>
                <option value="<?php echo $page->id(); ?>" <?php if($selected === $page->id()) { echo "selected"; } ?>><?php echo $page->title(); ?></option>
            <?php } ?>
        </select>
    <?php } ?>

    <label for="isHome">Is Homepage</label>
    <?php
        $isHome = "";
        if($model->selectedPage()->isHome()) {
            $isHome = "checked";
        }
    ?>
    <input type="checkbox" name="isHome" <?php echo $isHome; ?> />

    <label for="published">Published</label>
    <?php
        $published = "";
        if($model->selectedPage()->published()) {
            $published = "checked";
        }
    ?>
    <input type="checkbox" name="published" <?php echo $published; ?> />

    <input type="submit" name="submit" value="Save" />
</form>

<script type="text/javascript">
    document.querySelector('#showHelp').onclick = function() {
        showGuideModal(`@{ Html.RenderPartial("~/Views/Public/FormattingGuide.cshtml"); }`);
    };
    document.querySelector('#insertMedia').onclick = function() {
        showMediaItemModal();
    };
</script>