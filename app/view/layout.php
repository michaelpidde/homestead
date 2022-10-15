<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $model->title(); ?></title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="icon" href="data:,">
</head>
<body>
    <main>
        <header>
            <h1>Clozer Woods</h1>
        </header>
        <content class="rounded">
            <!-- @if(Model.PublishedPages != null && Model.PublishedPages.Any()) { -->
                <aside class="rounded">
                    <nav>
                        <!-- @{
                            await RenderNavigationNode(Model.PublishedPages);
                        } -->
                    </nav>
                </aside>
            <!-- } -->

            <?php echo $content; ?>
            <div class="clear"></div>
        </content>
    </main>

    <!-- @RenderSection("scripts", false) -->
</body>
</html>