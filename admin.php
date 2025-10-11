<?php
// --- SECURITY CHECK (Option: Secret Key) ---
$secret_key = "mizusui"; 
$submitted_key = $_GET['key'] ?? ''; // Get key from URL parameter

if ($submitted_key !== $secret_key) {
    http_response_code(401);
    die("<h1>401 Unauthorized</h1><p>You do not have access to this page.</p>");
}

// If access is granted, display the form below:
?>
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

    <main class="p-4">
      <h1 class="text-3xl font-bold mb-4">Admin Panel</h1>
      <p>Welcome to the admin panel. Here you can manage products and view orders.</p>

    <?php
      // Simple CRUD for products
      require_once __DIR__ . '/components/db.php';

      // Handle POST actions: create, update, delete
      $message = '';
      if ($_SERVER['REQUEST_METHOD'] === 'POST') {
          $action = $_POST['action'] ?? '';
          if ($action === 'create') {
              $name = $_POST['name'] ?? '';
              $price = $_POST['price'] ?? 0;
              $img = $_POST['img_data'] ?? null;
              $featured = isset($_POST['featured']) ? 1 : 0;
              createProduct(['name' => $name, 'price' => $price, 'img_data' => $img, 'featured' => $featured]);
              $message = 'Product created.';
          } elseif ($action === 'update') {
              $id = (int)($_POST['id'] ?? 0);
              $name = $_POST['name'] ?? '';
              $price = $_POST['price'] ?? 0;
              $img = $_POST['img_data'] ?? null;
              $featured = isset($_POST['featured']) ? 1 : 0;
              updateProduct($id, ['name' => $name, 'price' => $price, 'img_data' => $img, 'featured' => $featured]);
              $message = 'Product updated.';
          } elseif ($action === 'delete') {
              $id = (int)($_POST['id'] ?? 0);
              deleteProduct($id);
              $message = 'Product deleted.';
          }
      }

  // Fetch all products for listing (fetchProducts returns id DESC by default)
  // reverse the array so the table shows lowest id first
  $products = array_reverse(fetchProducts());
      ?>

      <?php if ($message): ?>
        <div class="mb-4 p-2 bg-green-100 text-green-800"><?= htmlspecialchars($message) ?></div>
      <?php endif; ?>

      <main>
      <section class="admin-products">
        <h2 class="text-2xl font-bold mb-2">Products</h2>
        <table class="w-full table-auto mb-6 p-2 border border-gray-300">
          <thead>
            <tr class="text-left">
              <th>ID</th>
              <th>Name</th>
              <th>Price</th>
              <th>Image</th>
              <th>Featured</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($products as $p): ?>
            <tr>
              <td><?= htmlspecialchars($p['id']) ?></td>
              <td><?= htmlspecialchars($p['name']) ?></td>
              <td><?= htmlspecialchars($p['price']) ?></td>
              <td>
                <?php if (!empty($p['image'])): ?>
                  <img src="<?= htmlspecialchars($p['image']) ?>" alt="thumb" class="image-thumb" />
                <?php else: ?>
                  &ndash;
                <?php endif; ?>
              </td>
              <td><?= $p['featured'] ? 'Yes' : 'No' ?></td>
              <td>
                <button onclick="populateEdit(<?= htmlspecialchars(json_encode($p), ENT_QUOTES, 'UTF-8') ?>)">Edit</button>
                <form method="post" style="display:inline" onsubmit="return confirm('Delete this product?');">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id" value="<?= htmlspecialchars($p['id']) ?>">
                  <button type="submit">Delete</button>
                </form>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

        <div class="flex items-center justify-between mb-2">
          <div>
            <button type="button" id="show-add-btn" onclick="showAddForm();">Add New Product</button>
          </div>
        </div>

        <div id="product-form-wrapper" style="display:none;">
        <form id="product-form" method="post" class="max-w-lg">
          <input type="hidden" name="action" id="form-action" value="create">
          <input type="hidden" name="id" id="form-id" value="0">
          <div class="mb-8">
            <label>Name</label>
            <input type="text" name="name" id="form-name" class="w-full" required>
          </div>
          <div class="mb-8">
            <label>Price</label>
            <input type="number" name="price" id="form-price" class="w-full" required>
          </div>
          <div class="mb-8">
            <label>Image (path or URL)</label>
            <input type="text" name="img_data" id="form-img" class="w-full">
          </div>
          <div class="mb-8">
            <label><input type="checkbox" name="featured" id="form-featured"> Featured</label>
          </div>
          <div class="mb-8">
            <button type="submit">Save</button>
            <button type="button" onclick="resetForm();">Reset</button>
            <button type="button" onclick="hideForm();">Cancel</button>
          </div>
        </form>
        </div>
      </section>
      </main>

      <script>
      function resetForm(){
        document.getElementById('form-action').value = 'create';
        document.getElementById('form-id').value = 0;
        document.getElementById('form-name').value = '';
        document.getElementById('form-price').value = '';
        document.getElementById('form-img').value = '';
        document.getElementById('form-featured').checked = false;
        // hide form after reset when used as cancel
        const wrapper = document.getElementById('product-form-wrapper');
        if (wrapper) wrapper.style.display = 'none';
      }

        function populateEdit(product){
        document.getElementById('form-action').value = 'update';
        document.getElementById('form-id').value = product.id || 0;
        document.getElementById('form-name').value = product.name || '';
        document.getElementById('form-price').value = product.price || '';
        document.getElementById('form-img').value = product.image || '';
        document.getElementById('form-featured').checked = !!product.featured;
        window.scrollTo({ top: document.getElementById('product-form').offsetTop - 20, behavior: 'smooth' });
        // show the form wrapper when editing
        const wrapper = document.getElementById('product-form-wrapper');
        if (wrapper) wrapper.style.display = 'block';
      }
      
      function showAddForm(){
        resetForm();
        const wrapper = document.getElementById('product-form-wrapper');
        if (wrapper) wrapper.style.display = 'block';
        // ensure the action is create
        document.getElementById('form-action').value = 'create';
        document.getElementById('form-id').value = 0;
        window.scrollTo({ top: document.getElementById('product-form').offsetTop - 20, behavior: 'smooth' });
      }

      function hideForm(){
        const wrapper = document.getElementById('product-form-wrapper');
        if (wrapper) wrapper.style.display = 'none';
        resetForm();
      }
      </script>

    </main>
    <?php include "components/footer.php";?>

  </body>
</html>