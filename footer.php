<?php defined("ABSPATH") || die('-1'); ?>  
<?php
$certificate_query = new WP_Query([
  'post_type' => 'certificates',
  'posts_per_page' => -1,
  'orderby' => 'menu_order',
  'order' => 'ASC'
]);
$product_query = new WP_Query([
  'post_type' => 'product',
  'posts_per_page' => -1,
  'tax_query' => [
    [
      'taxonomy' => 'product_cat',
      'terms' => [40],
      'field' => 'term_id'
    ]
  ]
]);
?>
</main>
    <footer>
      <section class="footer-shortcuts">
        <div class="container">
          <div class="row">
            <div class="col-12 col-lg-10 offset-lg-1">
              <div class="footer-shortcuts-box">
                <?= get_theme_option('theme_option_footer_block_content'); ?>

                <div class="buttons">
                  <?php for( $i = 0; $i < 3; $i++): $button_options = get_theme_option("theme_option_footer_url_options_{$i}"); $button_options_decoded = json_decode($button_options); ?>
                  <a href="<?= $button_options_decoded->url; ?>" class="btn is-alternative">
                    <?= $button_options_decoded->text; ?>
                    <?= get_svg("images/arrow-right.svg"); ?>
                  </a>
                  <?php endfor; ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="menus">
        <div class="container">
          <div class="row">
            <div class="col-12 col-sm-6 col-xl-3">
                <h3><?= __('Hijsmiddelen en- componenten', THEME_TD); ?></h3>
                <ul>
                  <?php foreach(get_terms([ 'taxonomy' => 'product_cat', 'hide_empty' => true]) as $term): ?>
                  <li>
                    <a href="<?= get_term_link($term->term_id); ?>"><?= $term->name; ?></a>
                  </li>
                  <?php endforeach; ?>
                </ul>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <h3><?= __('Hijskranen', THEME_TD); ?></h3>
                <?php if( $product_query->have_posts() ): ?>
                  <ul>
                    <?php while( $product_query->have_posts() ): $product_query->the_post(); ?>
                      <li>
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                      </li>
                    <?php endwhile; wp_reset_postdata(); ?>
                  </ul>
                <?php endif; ?>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <h3><?= __('Sectoren', THEME_TD); ?></h3>
                <ul>
                  <?php foreach( get_terms(['taxonomy' => 'sectors', 'hide_empty' => true,]) as $term): ?>
                    <li><a href="<?= get_term_link($term->term_id); ?>"><?= $term->name; ?></a></li>
                  <?php endforeach; ?>
                </ul>  
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <h3><?= __('CraneSolutions', THEME_TD); ?></h3>
                <?php wp_nav_menu([ 'theme_location' => 'footer-5' ]); ?>
                
            </div>
          </div>
        </div>
      </section>

      <section class="second-footer">
        <div class="container">
          <div class="row">
            <div class="col-12 col-lg-4">
              <h3>CraneSolutions B.V.</h3>
              <div class="text-fields">
                <p>
                  <?php if($address = get_option('seoninja_company_address')): ?>
                    <?= $address; ?><br />
                  <?php endif; ?>
                  <?php if($zipcode = get_option('seoninja_company_postal_code')): ?>
                    <?= $zipcode; ?> <?= get_option("seoninja_company_city"); ?><br />
                  <?php endif; ?>
                </p>
                <p>
                  <?php if( $phone = get_option('seoninja_company_telephone_number_int')): ?>
                  <?= __('Tel', THEME_TD); ?> <a href="tel:<?= $phone; ?>"><?= $phone; ?></a> <br />
                  <?php endif; ?>

                  <?php if( $email = get_option("seoninja_company_email_address") ): ?>
                  <a href="mailto:<?= $email; ?>"><?= $email; ?></a> <br />
                  <?php endif; ?>

                  <?php if( $vat_number = get_option('seoninja_company_btw_number')): ?>
                  <?= __('BTW nr.', THEME_TD); ?> <?= $vat_number; ?> <br />
                  <?php endif; ?>

                  <?php if( $vat_number = get_option('seoninja_company_kvk_number')): ?>
                  <?= __('KvK nr.', THEME_TD); ?> <?= $vat_number; ?>
                  <?php endif; ?>

                </p>
              </div>
            </div>
            <div class="col-12 col-lg-4">
              <h3><?= __('Volg ons op', THEME_TD); ?></h3>
              <ul class="socials">
                <?php if( $url = get_option('seoninja_socials_linkedin')): ?>
                <li>
                  <a href="<?= $url; ?>" target="_blank" rel="nofollow" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                </li>
                <?php endif; ?>

                <?php if( $url = get_option('seoninja_socials_youtube')): ?>
                <li>
                  <a href="<?= $url; ?>" target="_blank" rel="nofollow" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                </li>
                <?php endif; ?>

                <?php if( $url = get_option('seoninja_socials_facebook')): ?>
                <li>
                  <a href="<?= $url; ?>" target="_blank" rel="nofollow" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
                </li>
                <?php endif; ?>

                <?php if( $url = get_option('seoninja_socials_instagram')): ?>
                <li>
                  <a href="<?= $url; ?>" target="_blank" rel="nofollow" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                </li>
                <?php endif; ?>

                <?php if( $url = get_option('seoninja_socials_x')): ?>
                <li>
                  <a href="<?= $url; ?>" target="_blank" rel="nofollow" aria-label="X / Twitter"><i class="fab fa-x-twitter"></i></a>
                </li>
                <?php endif; ?>
              </ul>
            </div>
            <div class="col-12 col-lg-4">
              <h3><?= __('Certificaten', THEME_TD); ?></h3>
              <?php if( $certificate_query->have_posts() ): ?>
              <ul class='certificates'>
                <?php while( $certificate_query->have_posts() ): $certificate_query->the_post(); ?>
                <li>
                  <?php if( !empty( get_the_content() )): ?>
                    <a href="<?php the_permalink(); ?>" title="<?php the_title();?>">
                      <?=  get_the_post_thumbnail(get_the_ID(), 'thumbnail', ['alt' => get_the_title()]); ?>
                    </a>
                  <?php else: ?>
                    <?=  get_the_post_thumbnail(get_the_ID(), 'thumbnal', ['alt' => get_the_title()]); ?>
                  <?php endif; ?>
                </li>
                <?php endwhile; wp_reset_postdata(); ?>
              </ul>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </section>

      <section class="bottom-footer">
        <div class="container">
          <div class="row"> 
            <div class="col-12 col-lg-8">
             <p>CraneSolutions &copy; <?= date("Y"); ?> - <?= __('alle rechten voorbehouden', THEME_TD); ?>     |     <a href="<?= get_theme_option('theme_option_disclaimer_url'); ?>"><?= __('Disclaimer', THEME_TD); ?></a>    |    <a href="<?= get_privacy_policy_url(); ?>"><?= __('Privacy Statement', THEME_TD); ?></a>    |     <a class="open-cookieconsent elementor-item">Cookies</a></p> 
            </div>
            <div class="col-12 col-lg-2 offset-lg-2">
              <p>
                <?= __('Website door', THEME_TD); ?> <a href="https://dunepebbler.nl/" target="_blank">Dune Pebbler</a>
              </p>
            </div>
          </div>
        </div>
      </section>
    </footer>    
    <?php wp_footer(); ?>
  </body> 

</html>