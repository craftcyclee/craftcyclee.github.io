<!DOCTYPE html>
<html class="html" lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tutorials - Craft Cycle</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn-uicons.flaticon.com/3.0.0/uicons-regular-rounded/css/uicons-regular-rounded.css"/>
    <link rel="stylesheet" href="style/output.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Chewy&family=Outfit:wght@100..900&family=Sour+Gummy:ital,wght@0,100..900;1,100..900&display=swap"/>
    <style>
      /* Small local styles for tutorial cards */
      .tutorial-card { border: 1px solid #eee; padding: 1rem; margin-bottom: 1rem; border-radius: 8px; }
      .tutorial-title { display:flex; justify-content:space-between; align-items:center; cursor:pointer; }
      .tutorial-body { margin-top: .75rem; display:none; }
      .tutorial-body.active { display:block; }
      .video-wrapper { position:relative; padding-bottom:56.25%; height:0; overflow:hidden; }
      .video-wrapper iframe { position:absolute; top:0; left:0; width:100%; height:100%; }
    </style>
  </head>
  <body>

    <?php include "components/header.php";?>

    <header class="catalog-header">
      <h1 class="primary-text">Our Craft Tutorials</h1>
      <p>
        We provide step-by-step tutorials to help you create beautiful crafts from out kits! Check out our latest tutorials below.
      </p>
    </header>

    <main>
      <section class="main-container">
        <section class="catalog-controls top-4">
          <form class="search-bar" method="get" action="tutorial.php">
            <?php $q = isset($_GET['q']) ? trim($_GET['q']) : ''; ?>
            <input type="text" name="q" placeholder="Search for tutorials..." value="<?= htmlspecialchars($q) ?>" />
            <button type="submit"><i class="fi fi-rr-search"></i></button>
          </form>
        </section>

        <h1 class="text-4xl">Latest</h1>

        <?php
        require_once __DIR__ . '/components/db.php';

        $search = $q ?? null;
        try {
            $tutorials = fetchTutorials(null, null, $search);
        } catch (Exception $e) {
            error_log('Error fetching tutorials: ' . $e->getMessage());
            $tutorials = [];
        }

        if (empty($tutorials)) {
            echo '<p>No tutorials found.</p>';
        } else {
            foreach ($tutorials as $t) {
                $id = (int)($t['id'] ?? 0);
                $title = htmlspecialchars($t['title'] ?? 'Untitled');
                $body = nl2br(htmlspecialchars($t['body'] ?? ''));
                $yt = $t['youtube_id'] ?? null;
                // Basic YouTube id validation (alphanumeric, - and _ , length >=6)
                $yt = $yt && preg_match('/^[A-Za-z0-9_-]{6,}$/', $yt) ? $yt : null;
                ?>

                <article class="tutorial-card" data-id="<?= $id ?>">
                  <div class="tutorial-title" data-toggle="<?= $id ?>">
                    <div>
                      <strong><?= $title ?></strong>
                    </div>
                    <div>
                      <button class="expand-toggle" type="button" aria-expanded="false" data-target="body-<?= $id ?>">Show</button>
                    </div>
                  </div>
                  <div id="body-<?= $id ?>" class="tutorial-body">
                    <?php if ($yt): ?>
                      <div class="video-wrapper">
                        <iframe src="https://www.youtube.com/embed/<?= htmlspecialchars($yt) ?>" title="<?= $title ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                      </div>
                    <?php endif; ?>
                    <div class="tutorial-text">
                      <?= $body ?>
                    </div>
                  </div>
                </article>

                <?php
            }
        }
        ?>

      </section>
    </main>

    <?php include "components/footer.php";?>

    <script>
      document.addEventListener('click', function (e) {
        const btn = e.target.closest('.expand-toggle');
        if (!btn) return;
        const targetId = btn.getAttribute('data-target');
        const target = document.getElementById(targetId);
        if (!target) return;
        const isActive = target.classList.toggle('active');
        btn.textContent = isActive ? 'Hide' : 'Show';
        btn.setAttribute('aria-expanded', isActive ? 'true' : 'false');
      });
    </script>

  </body>
</html>