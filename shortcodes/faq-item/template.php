<?php if ($query->have_posts()) : ?>
  <section class="faq-categories">
    <div class="container">
      <div class="row">
        <div class="col-12">
          <h2><?= $shortcode_atts['title']; ?></h2>
          <?php while ($query->have_posts()) : $query->the_post(); ?>
            <div class="faq-category">
              <h2><?php the_title(); ?></h2>
              <?php the_content(); ?>
            </div>
          <?php endwhile; ?>
        </div>
      </div>
    </div>
  </section>
<?php endif; ?>