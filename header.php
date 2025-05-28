<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title><?= wp_title(); ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <?php wp_head(); ?>
  <script type="text/javascript">
    let arrowIconContent = `<?= get_svg("images/arrow-right.svg"); ?>`;
    // alert('PAS OP! Dit is de ontwikkelserver. De huidige website staat live.');
    //nasty fix for nasty code
  </script>

  <style>
    @media screen and (max-width: 1200px) {
      .navigation .menu li ul li {
        float: none !important;
      }
    }
  </style>
</head>


<body <?php body_class(); ?>>
  <header>
    <div class="top-header">
      <div class="container">
        <div class="search-bar">
          <!-- <form action="/<?= defined('ICL_LANGUAGE_CODE') && ICL_LANGUAGE_CODE != 'nl' ? ICL_LANGUAGE_CODE : ''; ?>">
              <input type="text" name="s" required>

              <button style="display: none"></button>
            </form> -->
        </div>
        <div class="quick-menu">
          <?php wp_nav_menu(['theme_location' => 'top-nav']); ?>
        </div>
      </div>
    </div>
    <div class="header">
      <div class="container">
        <div class="row">
          <div class="col-6 col-lg-3">
            <a href="<?= site_url(); ?>" class="logo" aria-label="Logo CraneSolutions">
              <?= get_svg('images/logo.svg'); ?>
            </a>
          </div>
          <div class="col-6 col-lg-9">
            <div class="navigation">
              <?= wp_nav_menu(['theme_location' => 'primary']); ?>

              <?php $cart_count = WC()->cart->get_cart_contents_count();
              if ($cart_count > 0) : ?>
                <a href="<?php echo esc_url(wc_get_cart_url()); ?>" class="btn-secondary">
                  <?= get_svg('images/file.svg'); ?>
                  <span class="cart-count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
                </a>
              <?php endif; ?>
            </div>
            <div class="hamburger hamburger--squeeze is-hamburger">
              <span class='hamburger-box'>
                <span class="hamburger-inner"></span>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </header>
  <main>