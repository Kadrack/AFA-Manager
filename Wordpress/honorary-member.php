<?php
/* Template Name: Page de texte */

/*Utilisation de fichier JSON importés dans le repertoire 'uploads/json' */
$base_url = ABSPATH."wp-content/uploads/json/honnor.json";
$request = file_get_contents($base_url);

//On décode le JSON
$membres = json_decode($request,true);

get_header();
?>

<?php $imageBannerFormation = get_field('intro_image_banner') ?>
<?php if ($imageBannerFormation) : ?>
    <section class="section-banner">
        <div class="image-container" style="background-image: url('<?php echo $imageBannerFormation['sizes']['xlarge'] ?>')"></div>
    </section>
<?php endif; ?>
    <section class="section -section-small section-article -nooverflow">
        <div class="title -contrastbar -article from-bottom">
            <?php the_field('intro_titre_page_texte') ?>
        </div>

        <div class="wp-content text -center from-bottom subtitle">
          <?php the_field('intro_soustitre_page_texte');?>
        </div>
        <?php $i = 0;
        if (have_rows('intro_texte_page_texte')) : ?>
            <?php while (have_rows('intro_texte_page_texte')) :
                the_row(); ?>
                <?php if ($i == 0) : ?>
                  <?php
                  // Count pour équilibrer les colonnes
                  $i=0;
                  $ln = count($membres);

                  if($membres) : ?>
                    <div class="wp-content text -center from-bottom list-members">
                      <ul>
                        <?php foreach($membres as $m) :?>
                          <li><?php echo $m['Firstname'].' '.$m['Name'] ;?></li>
                          <?php $i++;?>
                          <?php if($i >= $ln / 2) :?>
                              </ul><ul>
                              <?php $i = 0;?>
                          <?php endif;?>
                        <?php endforeach;?>
                      </ul>
                    </div>
                  <?php endif; ?>
                <div class="wp-content text -center from-bottom">
                    <?php the_sub_field('texte') ?>
                </div>
            <?php else : ?>
              <?php
                $membres = get_field('members');
                $i=0;
                  if($membres) :  ?>
                    <div class="wp-content text -light from-bottom list-members">
                        <ul>
                          <?php
                            foreach($membres as $m){
                              echo "<li>$m</li>";
                              $i++;

                              if($i == 5) :
                                echo "</ul><ul>";
                                $i = 0;
                              endif;
                            }
                            ?>
                        </ul>
                  </div>
              <?php endif; ?>
                <div class="wp-content text -light from-bottom">
                    <?php the_sub_field('texte') ?>
                </div>
            <?php endif; ?>
                <?php $i++;
            endwhile; ?>
        <?php endif; ?>


    </section>
<?php $images = get_field('galerie_page_texte') ?>
<?php if ($images) : ?>
    <section class="section-gallery">
        <div class="gallery-4items">
            <div class="gallery-slider">
                <div class="swiper-wrapper">
                    <?php foreach ($images as $image) : ?>
                        <div class="swiper-slide">
                            <a href="<?php echo $image['url']; ?>" class="slide-link" data-fancybox="carousel">
                                <img src="<?php echo $image['sizes']['medium-large']; ?>" alt="<?php echo $image['alt']; ?>" />
                                <span class="icon-plus"></span>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>
    <section class="section-portail">
        <?php get_template_part('template-parts/portail'); ?>
    </section>
<?php
get_footer();
