<?php
use Goteo\Library\Text,
    Goteo\Core\View,
    Goteo\Library\Page;

$page = Page::get('error');


include __DIR__ . '/../prologue.html.php';
include __DIR__ . '/../header.html.php';

use Goteo\Core\Error;

if (!isset($error) || !($error instanceof Error)) {
    $error = new Error;
}

?>

    <div id="sub-header">
        <div>
            <h2><?php echo $error->getMessage() ?>!</h2>
        </div>
    </div>

<?php if(isset($_SESSION['messages'])) { include __DIR__ . '/../header/message.html.php'; } ?>

    <div id="main">
        <div class="widget">
            <h3 class="title"><?php echo $page->name; ?></h3>
            <?php echo $page->content; ?>
        </div>
    </div>

<?php include __DIR__ . '/../footer.html.php' ?>
<?php include __DIR__ . '/../epilogue.html.php' ?>
