<?php
/* Template Name: Formations */
get_header();

/*Début rêquete */

/* Fin récupération information */

$servername = "";
$username = "";
$password = "";
$dbname = "";

try {
    $db = new pdo("mysql:host=$servername;dbname=$dbname", $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}
/* Liste commission pedagogique */
$sqlPedagogique = "SELECT m.member_firstname, m.member_name, m.member_phone, c.cluster_member_email, c.cluster_member_title, max(g.grade_rank), max(f.formation_rank)
FROM cluster_member c
INNER JOIN member m ON (c.cluster_member_join_member = m.member_id)
INNER JOIN grade g ON (g.grade_join_member = m.member_id)
LEFT JOIN formation f ON (f.formation_join_member = m.member_id)
WHERE c.cluster_member_join_cluster = 4 AND (c.cluster_member_date_out IS null OR c.cluster_member_date_out > CURRENT_DATE)
GROUP BY m.member_id ORDER BY c.cluster_member_title ASC";
/* Fin liste */
$pedagogiques = $db->query($sqlPedagogique)->fetchAll(); ?>
<?php
$commissionMemberTitle = array(
    1 => "Président(e)",
    2 => "Vice-président(e)",
    3 => "Secrétaire général(e)",
    4 => "Trésorier(ère) général(e)",
    5 => "Délégué(e) technique",
    6 => "Délégué(e) au relations interfédérales sportives",
    7 => "Responsable communication",
    8 => "Community manager",
    9 => "Administrateur(trice)",
    10 => "Secrétaire",
    11 => "Membre",
    12 => "Procureur",
    13 => "Juge",
) ?>

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
                    <?php if ($pedagogique['cluster_member_title'] == 1 || $pedagogique['cluster_member_title'] == 10) :
                    ?>
                        <div class="single-contact">
                            <p>
                                <strong><?php echo $pedagogique['member_firstname'] ?> <?php echo $pedagogique['member_name'] ?></strong> <br>
                                <?php echo $commissionMemberTitle[$pedagogique['cluster_member_title']] ?> de la commission pédagogique <br>
                                <a href="mailto:<?php echo $pedagogique['cluster_member_email'] ?>"><?php echo $pedagogique['cluster_member_email'] ?></a><br>
                                GSM : <a href="tel:<?php echo $pedagogique['member_phone'] ?>"> <?php echo $pedagogique['member_phone'] ?></a </p>
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
