<?php
$current = basename($_SERVER['SCRIPT_NAME']);
function nav_active($path, $current) {
    return $path === $current ? 'active' : '';
}
?>
<nav>
  <section class="nav-container p-4">
    <h1 class="primary-text">CRAFT CYCLE</h1>
    <a href="index.php" class="<?= nav_active('index.php', $current) ?>">Home</a>
    <a href="catalog.php" class="<?= nav_active('catalog.php', $current) ?>">Catalog</a>
    <!-- <a href="news.php" class="<?= nav_active('news.php', $current) ?>">News</a> -->
    <a href="tutorial.php" class="<?= nav_active('tutorial.php', $current) ?>">Tutorial</a>
  </section>
</nav>