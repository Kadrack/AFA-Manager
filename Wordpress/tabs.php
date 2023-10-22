<?php
//Remplacement de toutes les requêtes
$base_url = ABSPATH."wp-content/uploads/json/composition.json";
$request = file_get_contents($base_url);

//On décode le JSON
$admninisrations = json_decode($request,true)[3]['Members'];
$techniques = json_decode($request,true)[1]['Members'];
$pedagogiques = json_decode($request,true)[4]['Members'];
$juniors = json_decode($request,true)[2]['Members'];
$disciplines = json_decode($request,true)[5]['Members'];
$ethiques = json_decode($request,true)[6]['Members'];

// Plus besoin de convertir les titres en texte, le texte est directement fourni dans le fichier
?>


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
                        <?php if ($admninisration)
                            // $commissionMemberTitle permet de convertir le titre en chiffre vers le titre en texte, inutile maintenant le texte est directememnt fourni
                            // cluster_member_title = titre en chiffre dans la DB, Title = Texte du titre dans le fichier fourni
                            // member_firstname, member_name, member_phone, cluster_member_email = Prénom, Nom, Téléphone et Email dans la DB dans le fichier c'est Firstname, Name, Phone et Email
                            // Les modif dans les commissions suivantes sont exactement les mêmes que celle-ci
                        ?>
                        <p class="rank"><strong><?php echo $admninisration['Title'] ?></strong></p>
                        <p><?php echo $admninisration['Firstname'] ?>
                            <?php echo $admninisration['Name'] ?></p>
                        </div>
                        <div class="member-contact-details">
                            <a href="tel:<?php echo $admninisration['Phone'] ?>">
                                T. 0032 <?php echo $admninisration['Phone'] ?>
                            </a>
                            <a href="mailto:<?php echo $admninisration['Email'] ?>">Contacter par mail</a>
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
                            <?php if (isset($technique['Title'])) : // Test inutile tout les membres de commissions ont un titre, même commentaire pour les commissions suivantes ?>
                                <p class="rank"><strong><?php echo $technique['Title'] ?></strong></p>
                            <?php endif; ?>
                            <p><?php echo $technique['Firstname'] ?>
                                <?php echo $technique['Name'] ?></p>
                            </div>
                            <div class="member-contact-details">
                                <a href="tel:<?php echo $technique['Phone'] ?>">
                                    T. 0032 <?php echo $technique['Phone'] ?>
                                </a>
                                <a href="mailto:<?php echo $technique['Email'] ?>">Contacter par mail</a>
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
                                <?php if (isset($pedagogique['Title'])) : ?>
                                    <p class="rank"><strong><?php echo $pedagogique['Title'] ?></strong></p>
                                <?php endif; ?>
                                <p><?php echo $pedagogique['Firstname'] ?>
                                    <?php echo $pedagogique['Name'] ?></p>
                                </div>
                                <div class="member-contact-details">
                                    <a href="tel:<?php echo $pedagogique['Phone'] ?>">
                                        T. 0032 <?php echo $pedagogique['Phone'] ?>
                                    </a>
                                    <a href="mailto:<?php echo $pedagogique['Email'] ?>">Contacter par mail</a>
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

                                    <?php if (isset($junior['Title'])) : ?>
                                        <p class="rank"><strong><?php echo $junior['Title'] ?></strong></p>

                                    <?php endif; ?>
                                    <p><?php echo $junior['Firstname'] ?>
                                        <?php echo $junior['Name'] ?></p>
                                    </div>
                                    <div class="member-contact-details">
                                        <a href="tel:<?php echo $junior['Phone'] ?>">
                                            T. 0032 <?php echo $junior['Phone'] ?>
                                        </a>
                                        <a href="mailto:<?php echo $junior['Email'] ?>">Contacter par mail</a>
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
                                        <?php if (isset($discipline['Title'])) : ?>
                                            <p class="rank"><strong><?php echo $discipline['Title'] ?></strong></p>
                                        <?php endif; ?>
                                        <p><?php echo $discipline['Firstname'] ?>
                                            <?php echo $discipline['Name'] ?></p>
                                        </div>
                                        <div class="member-contact-details">
                                            <a href="tel:<?php echo $discipline['Phone'] ?>">
                                                T. 0032 <?php echo $discipline['Phone'] ?>
                                            </a>
                                            <a href="mailto:<?php echo $discipline['Email'] ?>">Contacter par mail</a>
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
                                            <p><?php echo $ethique['Firstname'] ?> <?php echo $ethique['Name'] // Pourquoi pas de téléphone ni d'adresse mail ?></p>
                                        </div>
                                    </div>
                                <?php endforeach ?>
                            </div>
                        </div>
                    </div>
                        <!-- END -->
            <?php endif; ?>
