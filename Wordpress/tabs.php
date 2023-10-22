<?php
/*Début rêquete */
$servername = "";
$username = "";
$password = "";
$dbname = "";

try {
           $db = new pdo("mysql:host=$servername;dbname=$dbname", $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
} catch (Exception $e) {
           die('Erreur : ' . $e->getMessage());
}
/* Liste conseil d'administration */
$sqlAdministration = "SELECT m.member_firstname, m.member_name, m.member_phone, c.cluster_member_email, c.cluster_member_title
FROM cluster_member c
INNER JOIN member m ON (c.cluster_member_join_member = m.member_id)
WHERE c.cluster_member_join_cluster = 3 AND (c.cluster_member_date_out IS null OR c.cluster_member_date_out > CURRENT_DATE) ORDER BY c.cluster_member_title ASC";
/* Fin liste */
/* Liste commission technique */
$sqlTechnique = "SELECT m.member_firstname, m.member_name, m.member_phone, c.cluster_member_email, c.cluster_member_title, max(g.grade_rank) , max(t.title_rank), max(f.formation_rank)
FROM cluster_member c
INNER JOIN member m ON (c.cluster_member_join_member = m.member_id)
INNER JOIN grade g ON (g.grade_join_member = m.member_id)
LEFT JOIN title t ON (t.title_join_member = m.member_id)
LEFT JOIN formation f ON (f.formation_join_member = m.member_id)
WHERE c.cluster_member_join_cluster = 1 AND (c.cluster_member_date_out IS null OR c.cluster_member_date_out > CURRENT_DATE)
GROUP BY m.member_id ORDER BY c.cluster_member_title ASC";
/* Fin liste */
/* Liste commission pedagogique */
$sqlPedagogique = "SELECT m.member_firstname, m.member_name, m.member_phone, c.cluster_member_email, c.cluster_member_title, max(g.grade_rank), max(f.formation_rank)
FROM cluster_member c
INNER JOIN member m ON (c.cluster_member_join_member = m.member_id)
INNER JOIN grade g ON (g.grade_join_member = m.member_id)
LEFT JOIN formation f ON (f.formation_join_member = m.member_id)
WHERE c.cluster_member_join_cluster = 4 AND (c.cluster_member_date_out IS null OR c.cluster_member_date_out > CURRENT_DATE)
GROUP BY m.member_id ORDER BY c.cluster_member_title ASC";
/* Fin liste */
/* Liste commission junior */
$sqlJunior = "SELECT m.member_firstname, m.member_name, m.member_phone, c.cluster_member_email, c.cluster_member_title
FROM cluster_member c
INNER JOIN member m ON (c.cluster_member_join_member = m.member_id)
WHERE c.cluster_member_join_cluster = 2 AND (c.cluster_member_date_out IS null OR c.cluster_member_date_out > CURRENT_DATE) ORDER BY c.cluster_member_title ASC";
/* Fin liste */
/* Liste commission discipline */
$sqlDiscipline = "SELECT m.member_firstname, m.member_name, m.member_phone, c.cluster_member_email, c.cluster_member_title
FROM cluster_member c
INNER JOIN member m ON (c.cluster_member_join_member = m.member_id)
WHERE c.cluster_member_join_cluster = 5 AND (c.cluster_member_date_out IS null OR c.cluster_member_date_out > CURRENT_DATE) ORDER BY c.cluster_member_title ASC";
/* Fin liste */
$sqlEthique = "SELECT m.member_firstname, m.member_name
FROM cluster_member c
INNER JOIN member m ON (c.cluster_member_join_member = m.member_id)
WHERE c.cluster_member_join_cluster = 6 AND (c.cluster_member_date_out IS null OR c.cluster_member_date_out > CURRENT_DATE)";

$admninisrations = $db->query($sqlAdministration)->fetchAll();
$techniques = $db->query($sqlTechnique)->fetchAll();
$pedagogiques = $db->query($sqlPedagogique)->fetchAll();
$juniors = $db->query($sqlJunior)->fetchAll();
$disciplines = $db->query($sqlDiscipline)->fetchAll();
$ethiques = $db->query($sqlEthique)->fetchAll();
?>

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

<?php if (have_rows('toggles')) : ?>
    <div class="container-tabs">
        <div class="nomobile">
            <ul class="nav nav-tabs from-bottom">
                <?php $i = 0; ?>
                <?php while (have_rows('toggles')) : the_row(); ?>
                    <?php $i++; ?>
                    <li class="tab-title -commission
                    <?php if ($i === 1) : echo 'active'; ?>
                    <?php endif; ?>" data-tab="#tab-<?php echo $i; ?>">
                    <div>
                        <?php the_sub_field('titre_administration') ?>
                    </div>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>
    <div class="-dropdown nodesktop">
        <div class="dropdown">
            <div class="select-filter">
                <span class="name"></span> <img src="<?php echo get_template_directory_uri() . '/assets/img/chevron-down.png' ?>" alt="">
            </div>
            <ul class="nav nav-tabs dropdown-filters-composition">
                <?php $i = 0; ?>
                <?php while (have_rows('toggles')) : the_row(); ?>
                    <?php $i++; ?>
                    <li class="tab-title -commission
                    <?php if ($i === 1) : echo 'active'; ?>
                    <?php endif; ?>" data-tab="#tab-<?php echo $i; ?>">
                    <div>
                        <?php the_sub_field('titre_administration') ?>
                    </div>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>
</div>
<!-- START-member-administration -->
<div class="tab-content -commission -smaller wp-content -justify">
    <div id="tab-1" class="tab-pane section -history active">
        <div class="title-container">
            <h3>Conseil administration</h3>
            <?php the_field('ca_texte') ?>
        </div>
        <div class="group-members">
            <div class="col">
                <?php $i = 0; ?>
                <?php foreach ($admninisrations as $admninisration) : ?>
                    <div class="member<?php if ($i % 2 != 1) : ?>
                        <?php echo 'active' ?>
                    <?php endif; ?>">
                    <div class="member-details">
                        <?php if ($admninisration) ?>
                        <p class="rank"><strong><?php echo $commissionMemberTitle[$admninisration['cluster_member_title']] ?></strong></p>
                        <p><?php echo $admninisration['member_firstname'] ?>
                            <?php echo $admninisration['member_name'] ?></p>
                        </div>
                        <div class="member-contact-details">
                            <a href="tel:<?php echo $admninisration['member_phone'] ?>">
                                T. 0032 <?php echo $admninisration['member_phone'] ?>
                            </a>
                            <a href="mailto:<?php echo $admninisration['cluster_member_email'] ?>">Contacter par mail</a>
                        </div>
                    </div>
                    <?php $i++; ?>
                    <?php if ($i % 2 === 0 && $i === 4) : ?>
                    </div>
                    <div class="col">
                        <?php $i = 0 ?>
                    <?php endif; ?>
                <?php endforeach ?>
            </div>
        </div>
    </div>
    <!-- END -->
    <!-- START-member-commission-technique -->
    <div id="tab-2" class="tab-pane section -history">
        <div class="title-container">
            <h3>Commission technique</h3>
            <?php the_field('ct_texte') ?>
        </div>
        <div class="group-members">
            <div class="col">
                <?php $i = 0; ?>
                <?php foreach ($techniques as $technique) : ?>
                    <?php if ($i === 3) : ?>
                    </div>
                    <div class="col">
                    <?php endif; ?>

                    <div class="member">
                        <div class="member-details">
                            <?php if (isset($commissionMemberTitle[$technique['cluster_member_title']])) : ?>
                                <p class="rank"><strong><?php echo $commissionMemberTitle[$technique['cluster_member_title']] ?></strong></p>
                            <?php endif; ?>
                            <p><?php echo $technique['member_firstname'] ?>
                                <?php echo $technique['member_name'] ?></p>
                            </div>
                            <div class="member-contact-details">
                                <a href="tel:<?php echo $technique['member_phone'] ?>">
                                    T. 0032 <?php echo $technique['member_phone'] ?>
                                </a>
                                <a href="mailto:<?php echo $technique['cluster_member_email'] ?>">Contacter par mail</a>
                            </div>
                        </div>
                        <?php $i++; ?>
                    <?php endforeach ?>
                </div>
            </div>
        </div>
        <!-- END -->
        <!-- START-member-commission-pedagogique -->
        <div id="tab-3" class="tab-pane section -history">
            <div class="title-container">
                <h3>Commission pédagogique</h3>
                <?php the_field('cp_texte') ?>
            </div>
            <div class="group-members">
                <div class="col">
                    <?php $i = 0; ?>
                    <?php foreach ($pedagogiques as $pedagogique) : ?>
                        <?php if ($i === 2) : ?>
                        </div>
                        <div class="col">
                        <?php endif; ?>
                        <div class="member">
                            <div class="member-details">
                                <?php if (isset($commissionMemberTitle[$pedagogique['cluster_member_title']])) : ?>
                                    <p class="rank"><strong><?php echo $commissionMemberTitle[$pedagogique['cluster_member_title']] ?></strong></p>
                                <?php endif; ?>
                                <p><?php echo $pedagogique['member_firstname'] ?>
                                    <?php echo $pedagogique['member_name'] ?></p>
                                </div>
                                <div class="member-contact-details">
                                    <a href="tel:<?php echo $pedagogique['member_phone'] ?>">
                                        T. 0032 <?php echo $pedagogique['member_phone'] ?>
                                    </a>
                                    <a href="mailto:<?php echo $pedagogique['cluster_member_email'] ?>">Contacter par mail</a>
                                </div>
                            </div>
                            <?php $i++; ?>
                        <?php endforeach ?>
                    </div>
                </div>
            </div>
            <!-- END -->
            <!-- START-member-commission-junior -->
            <div id="tab-4" class="tab-pane section -history">
                <div class="title-container">
                    <h3>Commission junior</h3>
                    <?php the_field('cj_texte') ?>
                </div>
                <div class="group-members">
                    <div class="col">
                        <?php $i = 0; ?>
                        <?php foreach ($juniors as $junior) : ?>
                            <div class="member">
                                <div class="member-details">

                                    <?php if (isset($commissionMemberTitle[$junior['cluster_member_title']])) : ?>
                                        <p class="rank"><strong><?php echo $commissionMemberTitle[$junior['cluster_member_title']] ?></strong></p>

                                    <?php endif; ?>
                                    <p><?php echo $junior['member_firstname'] ?>
                                        <?php echo $junior['member_name'] ?></p>
                                    </div>
                                    <div class="member-contact-details">
                                        <a href="tel:<?php echo $junior['member_phone'] ?>">
                                            T. 0032 <?php echo $junior['member_phone'] ?>
                                        </a>
                                        <a href="mailto:<?php echo $junior['cluster_member_email'] ?>">Contacter par mail</a>
                                    </div>
                                </div>
                                <?php $i++; ?>
                            <?php endforeach ?>
                        </div>
                    </div>
                </div>
                <!-- END -->
                <!-- START-member-discipline -->
                <div id="tab-5" class="tab-pane section -history">
                    <div class="title-container">
                        <h3>Conseil de discipline</h3>
                        <?php the_field('cd_texte') ?>
                    </div>
                    <div class="group-members">
                        <div class="col">
                            <?php $i = 0; ?>
                            <?php foreach ($disciplines as $discipline) : ?>
                                <?php if ($i === 1) : ?>
                                </div>
                                <div class="col">
                                <?php endif; ?>
                                <div class="member">
                                    <div class="member-details">
                                        <?php if (isset($commissionMemberTitle[$discipline['cluster_member_title']])) : ?>
                                            <p class="rank"><strong><?php echo $commissionMemberTitle[$discipline['cluster_member_title']] ?></strong></p>
                                        <?php endif; ?>
                                        <p><?php echo $discipline['member_firstname'] ?>
                                            <?php echo $discipline['member_name'] ?></p>
                                        </div>
                                        <div class="member-contact-details">
                                            <a href="tel:<?php echo $discipline['member_phone'] ?>">
                                                T. 0032 <?php echo $discipline['member_phone'] ?>
                                            </a>
                                            <a href="mailto:<?php echo $discipline['cluster_member_email'] ?>">Contacter par mail</a>
                                        </div>
                                    </div>
                                    <?php $i++; ?>
                                    <?php if ($i % 2 === 0 && $i === 4) : ?>
                                    </div>
                                    <div class="col">
                                        <?php $i = 0 ?>
                                    <?php endif; ?>
                                <?php endforeach ?>
                            </div>
                        </div>
                    </div>
                    <!-- END -->
                    <!-- START-ethique -->
                    <div id="tab-6" class="tab-pane section -history">
                        <div class="title-container">
                            <h3>Le référent éthique</h3>
                            <?php the_field('ethique_texte') ?>
                        </div>
                        <div class="group-members">
                            <div class="col">
                                <?php $i = 0; ?>
                                <?php foreach ($ethiques as $ethique) : ?>
                                    <div class="member">
                                        <div class="member-details">
                                            <p class="rank"><strong>Relais</strong></p>
                                            <p><?php echo $ethique['member_firstname'] ?> <?php echo $ethique['member_name'] ?></p>
                                        </div>
                                    </div>
                                <?php endforeach ?>
                            </div>
                        </div>
                    </div>
                        <!-- END -->
            <?php endif; ?>
