<!DOCTYPE html>
<html class="html" lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Craft Cycle - Home</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn-uicons.flaticon.com/3.0.0/uicons-regular-rounded/css/uicons-regular-rounded.css"/>
    <link rel="stylesheet" href="style/output.css"/>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Chewy&family=Outfit:wght@100..900&family=Sour+Gummy:ital,wght@0,100..900;1,100..900&display=swap"/>
  </head>
  <body>

    <?php include "components/header.php";?>

    <header>
      <section class="header-container">
        <section class="header-text">
          <h2 class="primary-text text-2xl font-bold">Make It. Love It.</h2>
          <h2 class="secondary-text text-2xl font-bold">Wear It.</h2>
          <p>
            Discover our collection of easy-to-follow DIY kits. Every box turns
            hours of fun into a wonderful, wearable creation. Transforming waste
            into wonders!
          </p>
        </section>
        <section class="header-image-container">
          <img src="image/cactus.png" alt="Mini cactus craft" class="header-image" /><img
            src="image/cake.png"
            alt="Clay cake craft"
            class="header-image"
          /><img src="image/cherry.png" alt="Cherry charm craft" class="header-image" /><img
            src="image/keychain.png"
            alt="Keychain craft"
            class="header-image"
          /><img src="image/mirror.png" alt="Mirror charm craft" class="header-image" /><img
            src="image/pin.png"
            alt="Bottle cap pin craft"
            class="header-image"
          /><img src="image/teabag.png" alt="Tea bag holder craft" class="header-image" /><img
            src="image/cactus.png"
            alt="Mini cactus craft"
            class="header-image"
          /><img src="image/cake.png" alt="Clay cake craft" class="header-image" />
        </section>
      </section>
    </header>

    <main >
      <section class="main-container">
        <section class="main-header p-4">
          <h1 class="text-3xl font-bold">Featured</h1>
          <p class="p-2">Our current best-selling kits, ready for you to create!</p>
          <a href="catalog.html" class="view-all-button">View All Kits</a>
        </section>
        
        <section class="main-featured">
        <?php
        // Include DB helper and fetch up to 3 featured product
        require_once __DIR__ . '/components/db.php';
        $product = [];
        try {
            $product = fetchProducts(3);
        } catch (Exception $e) {
            // If something goes wrong, leave $products empty and show fallbacks
            error_log('Error fetching products: ' . $e->getMessage());
            $product = [];
        }

        if (!empty($product)):
            foreach ($product as $p):
                // Normalize fields and provide defaults
                $img = !empty($p['image']) ? htmlspecialchars($p['image']) : 'image/pin.png';
                $name = !empty($p['name']) ? htmlspecialchars($p['name']) : 'Unnamed product';
                $price = isset($p['price']) ? number_format((float)$p['price'], 0, ',', '.') : '0';
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
          <!-- <div class="main-product-card">
            <img src="image/pin.png" class="main-product-card-image" />
            <div class="main-product-text"> 
              <p class="main-product-card-title">Bottle Cap Pin Kit</p>
              <p class="main-product-card-price">Rp 7.000</p>
            </div>
          </div>

          <div class="main-product-card">
            <img src="image/mirror.png" class="main-product-card-image" />
            <div class="main-product-text"> 
              <p class="main-product-card-title">Mirror Charm Kit</p>
              <p class="main-product-card-price">Rp 7.000</p>
            </div>
          </div>

          <div class="main-product-card">
            <img src="image/cherry.png" class="main-product-card-image" />
            <div class="main-product-text"> 
              <p class="main-product-card-title">Baby Cherry Kit</p>
              <p class="main-product-card-price">Rp 7.000</p>
            </div>
          </div> -->
        <?php endif; ?>
        </section>
          
        <section class="main-benefits">
          <div class="main-benefits-card">
            <i class="fi fi-rr-play-circle benefits-icon"></i>
            <h1 class="secondary-text">Step-by-Step Video</h1>
            <p>
              Every kit includes a full, easy-to-follow video tutorial for all
              skill levels.
            </p>
          </div>
          <div class="main-benefits-card">
            <i class="fi fi-rr-gem benefits-icon"></i>
            <h1 class="secondary-text">High Quality Materials</h1>
            <p>
              We source only the best materials to ensure your final craft is
              beautiful and durable.
            </p>
          </div>
          <div class="main-benefits-card">
            <i class="fi fi-rr-leaf benefits-icon"></i>
            <h1 class="secondary-text">Sustainable Crafting</h1>
            <p>
              Committed to low waste and mindful sourcing in every kit we
              design.
            </p>
          </div>
        </section>

        <!-- <section class="main-sign-up">
          <div class="main-sign-up-card">
            <h1>Join the Crafting Circle!</h1>
            <p>
              Join our community and get 10% off your first order and exclusive access to new kit
              releases!
            </p>
            <form action="">
              <input
                type="email"
                placeholder="Enter your email address..."
                class="main-sign-up-form"
              />
            </form>
            <a href="" class="main-sign-up-button">Sign Up!</a>
          </div>
        </section> -->
      </section>
    </main>

    <?php include "components/footer.php";?>

  </body>
</html>