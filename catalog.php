<!DOCTYPE html>
<html class="html" lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Craft Cycle - Catalog</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn-uicons.flaticon.com/3.0.0/uicons-regular-rounded/css/uicons-regular-rounded.css"/>
    <link rel="stylesheet" href="style/output.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Chewy&family=Outfit:wght@100..900&family=Sour+Gummy:ital,wght@0,100..900;1,100..900&display=swap"/>
  </head>
  <body>
    
    <?php include "components/header.php";?>

    <header class="catalog-header">
      <h1 class="primary-text">Our Craft Kits</h1>
      <p>
        Find your next creative adventure! All kits are designed to be fun for
        everyone.
      </p>
    </header>

    <main>
      <section class="main-container">
        <section class="catalog-controls">
          <form class="search-bar" method="get" action="catalog.php">
            <?php $q = isset($_GET['q']) ? trim($_GET['q']) : ''; ?>
            <input type="text" name="q" placeholder="Search for kits..." value="<?= htmlspecialchars($q) ?>" />
            <button type="submit"><i class="fi fi-rr-search"></i></button>
          </form>
          <!-- <div class="filter-buttons">
            <a href="#" class="filter-active">All</a>
            <a href="#">Pins</a>
            <a href="#">Charms</a>
            <a href="#">Keychains</a>
          </div> -->
        </section>

        <section class="product-container">
    <?php
    require_once __DIR__ . '/components/db.php';
    try {
      $q = isset($_GET['q']) ? trim($_GET['q']) : null;
      $products = fetchProducts(null, $q);
    } catch (Exception $e) {
      error_log('Error fetching catalog products: ' . $e->getMessage());
      $products = [];
    }

        if (!empty($products)):
            foreach ($products as $prod):
                $img = !empty($prod['image']) ? htmlspecialchars($prod['image']) : 'image/pin.png';
                $name = !empty($prod['name']) ? htmlspecialchars($prod['name']) : 'Unnamed product';
                $price = isset($prod['price']) ? number_format((float)$prod['price'], 0, ',', '.') : '0';
        ?>

          <div class="main-product-card">
            <img src="<?= $img ?>" class="main-product-card-image" />
            <div class="main-product-text"> 
              <p class="main-product-card-title"><?= $name ?></p>
              <p class="main-product-card-price">Rp <?= $price ?></p>
            </div>
          </div>
        <?php
            endforeach;
        else:
        ?>
          <p>No products found.</p>
        <?php endif; ?>
        </section>

        <section class="load-more-container">
          <a href="#" class="load-more-button">Load More</a>
        </section>
      </section>
    </main>

    <?php include "components/footer.php";?>

  </body>
</html>
