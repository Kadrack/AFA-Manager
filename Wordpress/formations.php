<?php
/* Template Name: Formations */
get_header();

//Remplacement de toutes les requêtes
$base_url = ABSPATH."wp-content/uploads/json/composition.json";
$request = file_get_contents($base_url);

//On décode le JSON
$pedagogiques = json_decode($request,true)[4]['Members'];

// Plus besoin de convertir les titres en texte, le texte est directement fourni dans le fichier
// Exactement la même chose que dans le fichiers des commissions
?>

<section class="section-banner">
    <?php $imageBannerFormation = get_field('intro_formation_image_banner') ?>
    <div class="image-container" style="background-image: url('<?php echo $imageBannerFormation['sizes']['xlarge'] ?>')"></div>
</section>
<section class="section-recurrence -formation">
    <img src="<?php echo get_template_directory_uri() . '/assets/img/bg-aikido.png' ?>" alt="">
    <div class="section -federation content">
        <div class="title from-bottom">
            <?php the_field('titre_formation') ?>
        </div>
        <div class=" wp-content text -center from-bottom">
            <?php $i = 0; ?>
            <?php if (have_rows('texte_formation')) : ?>
                <?php while (have_rows('texte_formation')) : the_row(); ?>
                    <?php $i++; ?>
                    <?php the_sub_field('texte') ?>
                    <?php if ($i === 1) : ?>
        </div>
        <div class=" wp-content text -light from-bottom">
        <?php endif; ?>
    <?php endwhile; ?>
<?php endif; ?>
        </div>
    </div>
    <div class="image-recurrence">
        <?php $imageFormation = get_field('image_formation') ?>
        <div class="image-container">
            <img src="<?php echo $imageFormation['sizes']['medium-large'] ?>" alt="">
            <img class="deco-aikido from-right" src="<?php echo get_template_directory_uri() . '/assets/img/deco-aikido.png' ?>" alt="">
        </div>
    </div>
</section>
<section class="section-training">
    <?php get_template_part('template-parts/tabs-training'); ?>
</section>
<div class="bg-dark -paddingbottom">
    <section class="section-container -full">
        <div class="slider-blocktext double-slider">
            <div class="slider-presentation -double">
                <div class="swiper-wrapper">
                    <?php if (have_rows("slide_infos_complementaires")) : ?>
                        <?php while (have_rows("slide_infos_complementaires")) : the_row(); ?>
                            <div class="swiper-slide ">
                                <?php $image = get_sub_field("image"); ?>
                                <img src="<?php echo $image['sizes']['xlarge'] ?>" alt="<?php echo $image['alt'] ?>" class="image">
                            </div>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </div>
                <div class="next-prev-container">
                    <div class="swiper-button-prev prev-double"><i class="fas fa-chevron-left"></i></div>
                    <div class="swiper-button-next next-double"><i class="fas fa-chevron-right"></i></div>
                </div>
            </div>
            <div class="bloc-container -dark">
                <div class="slider-text -double">
                    <div class="swiper-wrapper">
                        <?php if (have_rows("slide_infos_complementaires")) : ?>
                            <?php while (have_rows("slide_infos_complementaires")) : the_row(); ?>
                                <div class="swiper-slide ">
                                    <div class="wp-content">
                                        <?php the_sub_field('texte') ?>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<section class="section-adeps">
    <h2 class="sro">Informations complémentaire</h2>
    <?php $imageInfo = get_field("image_info") ?>
    <div class="image-container" style="background: url('<?php echo $imageInfo['sizes']['large'] ?>') no-repeat; background-size: cover; background-position:center;background-attachment:fixed;">
        <div class="section -section-small">
            <div class="title">
                <h2><?php the_field('titre_info') ?></h2>
            </div>
            <div class="text -formation">
                <?php the_field('texte_info') ?>
            </div>
            <div class="contact-personne">
                <?php foreach ($pedagogiques as $pedagogique) : ?>
                    <?php if ($pedagogique['TitleNumber'] == 1 || $pedagogique['TitleNumber'] == 3) :
                        //  Le nombres de titres dans la DB a éét simplifié donc Secrétaire n'est plus le dixième titre mais le troisième
                        //  Les modifications en dessous sont identiques à celle faites dans le fichier des commissions
                    ?>
                        <div class="single-contact">
                            <p>
                                <strong><?php echo $pedagogique['Firstname'] ?> <?php echo $pedagogique['Name'] ?></strong> <br>
                                <?php echo $pedagogique['Title'] ?> de la commission pédagogique <br>
                                <a href="mailto:<?php echo $pedagogique['Email'] ?>"><?php echo $pedagogique['Email'] ?></a><br>
                                GSM : <a href="tel:<?php echo $pedagogique['Phone'] ?>"> <?php echo $pedagogique['Phone'] ?></a </p>
                        </div>
                    <?php endif;
                    ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
<section class="section-gallery">
    <?php get_template_part('template-parts/gallery'); ?>
</section>
<section class="section section-partners">
    <?php get_template_part('template-parts/partners'); ?>
</section>
<div class="bg-secondContrast">
    <section class="section -section-small section-contact">
        <?php get_template_part('template-parts/contact-form'); ?>
    </section>
</div>
<section class="section-newsletter">
    <?php get_template_part('template-parts/newsletter'); ?>
</section>
<section class="section-portail">
    <?php get_template_part('template-parts/portail'); ?>
</section>
<?php
get_footer();
