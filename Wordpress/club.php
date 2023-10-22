<?php
/* Template Name: Club */
get_header();

//Remplacement de toutes les requêtes
$base_url = ABSPATH."wp-content/uploads/json/clubs.json";
$request = file_get_contents($base_url);

//On décode le JSON
$clubs = json_decode($request,true);

// Plus besoin de convertir les grades, type de cours, jours, ... en texte, le texte est directement fourni dans le fichier
?>

<section class="section-banner">
    <?php $imageBannerclub = get_field('intro_club_image_banner') ?>
    <div class="image-container" style="background-image: url('<?php echo $imageBannerclub['sizes']['xlarge'] ?>')"></div>
</section>
<section class="section search-club">
    <div class="club-title">
        <div class="title from-right">
            <?php the_field('titre_recherche') ?>
        </div>
        <div class="subtitle from-left">
            <?php the_field('sous-titre_recherche') ?>
        </div>
    </div>
    <div class="location container-tabs nomobile">
        <ul class="nav nav-tabs">
            <?php $i = 1; ?>

            <?php while ($i < 9) : ?>
                <li class="<?php if ($i === 1) : echo 'active'; ?>
                <?php endif; ?> tab-title" data-tab="#tab-<?php echo $i; ?>">
                <?php $i++; ?>
                <?php switch ($i) {
                    case "2":
                    echo ('Toutes les provinces');
                    break;
                    case "3":
                    echo ('Brabant');
                    break;
                    case "4":
                    echo ('Brabant Flamand');
                    break;
                    case "5":
                    echo ('Bruxelles');
                    break;
                    case "6":
                    echo ('Hainaut');
                    break;
                    case "7":
                    echo ('Liège');
                    break;
                    case "8":
                    echo ('Luxembourg');
                    break;
                    case "9":
                    echo ('Namur');
                    break;
                } ?>
            </li>
        <?php endwhile; ?>
    </ul>
</div>
<div class="location container-tabs -dropdown nodesktop">
    <div class="dropdown">
        <div class="chosen-filter">
            <span class="name"></span> <img src="<?php echo get_template_directory_uri() . '/assets/img/chevron-down.png' ?>" alt="">
        </div>
        <ul class="nav nav-tabs dropdown-filters">
            <?php $i = 1; ?>
            <?php while ($i < 9) : ?>
                <li class="<?php if ($i === 1) : echo 'active'; ?>
                <?php endif; ?> tab-title" data-tab="#tab-<?php echo $i; ?>">
                <?php $i++; ?>
                <?php switch ($i) {
                    case "2":
                    echo ('Toutes les provinces');
                    break;
                    case "3":
                    echo ('Brabant');
                    break;
                    case "4":
                    echo ('Brabant Flamand');
                    break;
                    case "5":
                    echo ('Bruxelles');
                    break;
                    case "6":
                    echo ('Hainaut');
                    break;
                    case "7":
                    echo ('Liège');
                    break;
                    case "8":
                    echo ('Luxembourg');
                    break;
                    case "9":
                    echo ('Namur');
                    break;
                } ?>
            </li>
        <?php endwhile; ?>
        </ul>
    </div>
</div>

<!-- <?php $i = 0; ?> -->
<?php $i = 1; ?>
<?php while ($i < 9) : ?>
    <div class="tab-content">
        <div id="tab-<?php echo $i; ?>" class="tab-pane club-container active">
            <?php foreach ($clubs as $clubId => $club) : ?>
                <?php $dojos = $club['Dojos']; ?>
                <!-- Toutes les provinces -->
                <?php if ($i === 1) : ?>
                    <div class="club">
                    <div class="image-container">
                        <img class="club-image" src="https://afamanager.aikido.be/uploads/clubs/<?php echo $clubId ?>.png" alt="<?php echo $club['Name'] ?>">
                        <?php // echo get_template_directory_uri() . '/assets/img/logo-footer-1.png'
                        ?>
                    </div>
                    <div class="inner-content">
                        <h3><?php echo $club['Name'] ?></h3>
                        <div class="city">
                            <?php if (count($dojos)) : ?>
                                <p><?php echo $dojos[0]['Zip'] ?></p>
                                <p><?php echo $dojos[0]['City'] ?></p>
                            <?php endif; ?>
                        </div>
                        <?php $idnumber = intval($clubId);
                        $hours = $club['Classes'];
                        ?>
                        <p class="public">
                            <?php echo $club['Type'] // Information fournies directement plus besoin de "calculé" l'information ?>
                        </p>
                        <?php foreach ($dojos as $dojo): ?>
                            <?php $listAdresses[] = $dojo['Address'] . " " . $dojo['Zip'] . " " . $dojo['City']; ?>
                        <?php endforeach; ?>
                        <?php $stringAdresses = implode(',', $listAdresses) ?>

                        <div class="info container-tabs">
                            <div class="cta-container nav-club">
                                <a data-club-tab="#club-<?php echo $clubId; ?>" class="discover-club cta -contrast" href="#map-clubs" data-address="<?php echo $stringAdresses ?>">Découvrir</a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Brabant -->
              <?php elseif ($i === 2 && $club['Province'] === 'Brabant Wallon') : ?>
                    <div class="club">
                    <div class="image-container">
                        <img src="https://afamanager.aikido.be/uploads/clubs/<?php echo $clubId ?>.png" alt="<?php echo $club['Name'] ?>">
                    </div>
                    <div class="inner-content">
                        <h3><?php echo $club['Name'] ?></h3>
                        <div class="city">
                            <?php if (count($dojos)) : ?>
                                <p><?php echo $dojos[0]['Zip'] ?></p>
                                <p><?php echo $dojos[0]['City'] ?></p>
                            <?php endif; ?>
                        </div>
                        <p class="public">
                            <?php echo $club['Type'] ?>
                        </p>
                        <?php foreach ($dojos as $dojo): ?>
                            <?php $listAdresses[] = $dojo['Address'] . " " . $dojo['Zip'] . " " . $dojo['City']; ?>
                        <?php endforeach; ?>
                        <?php $stringAdresses = implode(',', $listAdresses) ?>

                        <div class="info container-tabs">
                            <div class="cta-container nav-club">
                                <a data-club-tab="#club-<?php echo $clubId; ?>" class="discover-club cta -contrast" href="#map-clubs" data-address="<?php echo $stringAdresses ?>">Découvrir</a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Brabant Flamand -->
              <?php elseif ($i === 3 && $club['Province'] === 'Brabant Flamand') : ?>
                    <div class="club">
                    <div class="image-container">
                        <img src="https://afamanager.aikido.be/uploads/clubs/<?php echo $clubId ?>.png" alt="<?php echo $club['Name'] ?>">
                    </div>
                    <div class="inner-content">
                        <h3><?php echo $club['Name'] ?></h3>
                        <div class="city">
                            <?php if (count($dojos)) : ?>
                                <p><?php echo $dojos[0]['Zip'] ?></p>
                                <p><?php echo $dojos[0]['City'] ?></p>
                            <?php endif; ?>
                        </div>
                        <p class="public">
                            <?php echo $club['Type'] ?>
                        </p>
                        <?php foreach ($dojos as $dojo): ?>
                            <?php $listAdresses[] = $dojo['Address'] . " " . $dojo['Zip'] . " " . $dojo['City']; ?>
                        <?php endforeach; ?>
                        <?php $stringAdresses = implode(',', $listAdresses) ?>

                        <div class="info container-tabs">
                            <div class="cta-container nav-club">
                                <a data-club-tab="#club-<?php echo $clubId; ?>" class="discover-club cta -contrast" href="#map-clubs" data-address="<?php echo $stringAdresses ?>">Découvrir</a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Bruxelles -->
              <?php elseif ($i === 4 && $club['Province'] === 'Bruxelles') : ?>
                    <div class="club">
                    <div class="image-container">
                        <img src="https://afamanager.aikido.be/uploads/clubs/<?php echo $clubId ?>.png" alt="<?php echo $club['Name'] ?>">
                    </div>
                    <div class="inner-content">
                        <h3><?php echo $club['Name'] ?></h3>
                        <div class="city">
                            <?php if (count($dojos)) : ?>
                                <p><?php echo $dojos[0]['Zip'] ?></p>
                                <p><?php echo $dojos[0]['City'] ?></p>
                            <?php endif; ?>
                        </div>
                        <p class="public">
                            <?php echo $club['Type'] ?>
                        </p>
                        <?php foreach ($dojos as $dojo): ?>
                            <?php $listAdresses[] = $dojo['Address'] . " " . $dojo['Zip'] . " " . $dojo['City']; ?>
                        <?php endforeach; ?>
                        <?php $stringAdresses = implode(',', $listAdresses) ?>

                        <div class="info container-tabs">
                            <div class="cta-container nav-club">
                                <a data-club-tab="#club-<?php echo $clubId; ?>" class="discover-club cta -contrast" href="#map-clubs" data-address="<?php echo $stringAdresses ?>">Découvrir</a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Hainaut -->
              <?php elseif ($i === 5 && $club['Province'] === 'Hainaut') : ?>
                    <div class="club">
                    <div class="image-container">
                        <img src="https://afamanager.aikido.be/uploads/clubs/<?php echo $clubId ?>.png" alt="<?php echo $club['Name'] ?>">
                    </div>
                    <div class="inner-content">
                        <h3><?php echo $club['Name'] ?></h3>
                        <div class="city">
                            <?php if (count($dojos)) : ?>
                                <p><?php echo $dojos[0]['Zip'] ?></p>
                                <p><?php echo $dojos[0]['City'] ?></p>
                            <?php endif; ?>
                        </div>
                        <p class="public">
                            <?php echo $club['Type'] ?>
                        </p>
                        <?php foreach ($dojos as $dojo): ?>
                            <?php $listAdresses[] = $dojo['Address'] . " " . $dojo['Zip'] . " " . $dojo['City']; ?>
                        <?php endforeach; ?>
                        <?php $stringAdresses = implode(',', $listAdresses) ?>

                        <div class="info container-tabs">
                            <div class="cta-container nav-club">
                                <a data-club-tab="#club-<?php echo $clubId; ?>" class="discover-club cta -contrast" href="#map-clubs" data-address="<?php echo $stringAdresses ?>">Découvrir</a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Liège -->
              <?php elseif ($i === 6 && $club['Province'] === 'Liège') : ?>
                    <div class="club">
                    <div class="image-container">
                        <img src="https://afamanager.aikido.be/uploads/clubs/<?php echo $clubId ?>.png" alt="<?php echo $club['Name'] ?>">
                    </div>
                    <div class="inner-content">
                        <h3><?php echo $club['Name'] ?></h3>
                        <div class="city">
                            <?php if (count($dojos)) : ?>
                                <p><?php echo $dojos[0]['Zip'] ?></p>
                                <p><?php echo $dojos[0]['City'] ?></p>
                            <?php endif; ?>
                        </div>
                        <p class="public">
                            <?php echo $club['Type'] ?>
                        </p>
                        <?php foreach ($dojos as $dojo): ?>
                            <?php $listAdresses[] = $dojo['Address'] . " " . $dojo['Zip'] . " " . $dojo['City']; ?>
                        <?php endforeach; ?>
                        <?php $stringAdresses = implode(',', $listAdresses) ?>

                        <div class="info container-tabs">
                            <div class="cta-container nav-club">
                                <a data-club-tab="#club-<?php echo $clubId; ?>" class="discover-club cta -contrast" href="#map-clubs" data-address="<?php echo $stringAdresses ?>">Découvrir</a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Luxembourg -->
              <?php elseif ($i === 7 && $club['Province'] === 'Luxembourg') : ?>
                    <div class="club">
                    <div class="image-container">
                        <img src="https://afamanager.aikido.be/uploads/clubs/<?php echo $clubId ?>.png" alt="<?php echo $club['Name'] ?>">
                    </div>
                    <div class="inner-content">
                        <h3><?php echo $club['Name'] ?></h3>
                        <div class="city">
                            <?php if (count($dojos)) : ?>
                                <p><?php echo $dojos[0]['Zip'] ?></p>
                                <p><?php echo $dojos[0]['City'] ?></p>
                            <?php endif; ?>
                        </div>
                        <p class="public">
                            <?php echo $club['Type'] ?>
                        </p>
                        <?php foreach ($dojos as $dojo): ?>
                            <?php $listAdresses[] = $dojo['Address'] . " " . $dojo['Zip'] . " " . $dojo['City']; ?>
                        <?php endforeach; ?>
                        <?php $stringAdresses = implode(',', $listAdresses) ?>

                        <div class="info container-tabs">
                            <div class="cta-container nav-club">
                                <a data-club-tab="#club-<?php echo $clubId; ?>" class="discover-club cta -contrast" href="#map-clubs" data-address="<?php echo $stringAdresses ?>">Découvrir</a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Namur -->
              <?php elseif ($i === 8 && $club['Province'] === 'Namur') : ?>
                    <div class="club">
                    <div class="image-container">
                        <img src="https://afamanager.aikido.be/uploads/clubs/<?php echo $clubId ?>.png" alt="<?php echo $club['Name'] ?>">
                    </div>
                    <div class="inner-content">
                        <h3><?php echo $club['Name'] ?></h3>
                        <div class="city">
                            <?php if (count($dojos)) : ?>
                                <p><?php echo $dojos[0]['ZIP'] ?></p>
                                <p><?php echo $dojos[0]['City'] ?></p>
                            <?php endif; ?>
                        </div>
                        <p class="public">
                            <?php echo $club['Type'] ?>
                        </p>
                        <?php foreach ($dojos as $dojo): ?>
                            <?php $listAdresses[] = $dojo['Address'] . " " . $dojo['Zip'] . " " . $dojo['City']; ?>
                        <?php endforeach; ?>
                        <?php $stringAdresses = implode(',', $listAdresses) ?>

                        <div class="info container-tabs">
                            <div class="cta-container nav-club">
                                <a data-club-tab="#club-<?php echo $clubId; ?>" class="discover-club cta -contrast" href="#map-clubs" data-address="<?php echo $stringAdresses ?>">Découvrir</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            <?php endforeach ?>
        </div>
        <?php $i++; ?>
    </div>
<?php endwhile; ?>
</section>
<section class="section-map">
    <div id="map-clubs">
        <!-- <?php if ($clubs) : ?>
        <?php foreach ($clubs as $clubId => $club) : ?>
        <?php $postcode = $club['Dojos'][0]['Zip'] // Ces trois définitions sont toutes fausses et génère plusieurs message d'erreur, la carte n'a jamais foncitonné et c'est indépedant de mes modifications ?>
        <?php $adress = $club['Dojos'][0]['Address'] // Je les ai modifiés pour qu'elle ne génère plus d'erreur ?>
        <?php $commune = $club['Dojos'][0]['City'] ?>
        <div class="marker-club" data-postcode="<?php echo $postcode ?>" data-commune="<?php echo $commune ?>" data-adresse="<?php echo $adress ?>">
        <div class="infowindow">
        <div class="content">
        <h2><?php echo $postcode ?></h2>
        <p><?php echo $adress ?></p>
        <p><?php echo $commune ?></p>
        <h5>chich</h5>
    </div>
</div>
</div>
<?php endforeach; ?>
<?php endif; ?> -->
</div>
</section>
<section id="club-description" class="section-single-club">
    <?php $b = 1;
    foreach ($clubs as $clubId => $club) : ?>
    <div class="club-content <?php if ($b == 1) : ?><?php endif; ?>" id="club-<?php echo $clubId; ?>">
        <div class="club-details">
            <div class="top-detail">
                <div class="province">
                    <h2>
                        <?php echo $club['Province'] ?>
                    </h2>
                </div>
                <div class="number">
                    <?php if ($club['Contact']) : ?>
                        <span class="contact"><?php echo $club['Contact']; ?></span>
                    <?php endif; ?>
                    <?php if ($club['Phone']) : ?>
                        <a href="tel:<?php echo $club['Phone'] ?>"><?php echo $club['Phone'] ?></a>
                    <?php endif; ?>
                </div>
                <div class="socials">
                    <?php if ($club['Email']) : ?>
                        <a href="mailto:<?php echo $club['Email'] ?>" class="cta-social"><i class="fas fa-envelope"></i></a>
                    <?php endif; ?>
                    <?php if ($club['Site']) : ?>
                        <a href="<?php echo $club['Site'] ?>" rel="external" target="_blank" class="cta-social"><i class="fas fa-globe"></i></a>
                    <?php endif; ?>
                    <?php if ($club['Facebook']) : ?>
                        <a href="<?php echo $club['Facebook'] ?>" rel="external" target="_blank" class="cta-social -facebook"><i class="fab fa-facebook-f"></i></a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="mid-detail">
                <div class="club-name"><?php echo $club['Name'] ?></div>
                <div class="address">
                    <?php $dojos = $club['Dojos']; ?>
                    <?php foreach ($dojos as $dojo) : ?>
                        <div class="city">
                            <p><?php echo $dojo['Address'] ?></p>
                            <p><?php echo $dojo['Zip'] ?> <?php echo $dojo['City'] ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="bottom-detail">
                <div class="hourly content">
                    <p class="title">Horaires</p>
                    <?php
                    $hours = $club['Classes'];
                    ?>
                    <ul class="list">
                        <?php foreach ($hours as $hour) : ?>
                            <li class="item">
                                <?php echo $hour['Day'] ?>
                                de <?php echo $hour['Start'] ?>
                                à <?php echo $hour['End'] ?>
                                (<?php echo $hour['Type'] ?>)
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="teachers content">
                    <div class="title">Professeurs</div>
                    <?php
                    $teachers = $club['Teachers'];
                    ?>
                    <ul class="list">
                        <?php $results = array(); ?>
                        <?php foreach ($teachers as $teacher) : ?>
                            <li class="item <?php if ($teacher['Title'] == 'Dojo Cho') :?>-marginbottom<?php endif; ?>">
                                <div class="name">
                                    <strong><?php echo $teacher['Name'] ?> <?php echo $teacher['Firstname'] ?></strong>
                                    <?php $title = is_null($teacher['Aikikai']) ?>
                                    <?php if ($title) : ?>
                                        <div class="title"> <?php echo $teacher['Aikikai']; ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="infos">
                                    <?php if (!is_null($teacher['Title'])) : ?>
                                        <span><?php echo $teacher['Title'] ?></span>
                                    <?php endif; ?>
                                    <span><?php echo $teacher['Grade'] ?></span>
                                    <?php $adeps = !is_null($teacher['Adeps']) ?>
                                    <?php if ($adeps) : ?>
                                        <span> <?php echo $teacher['Adeps']; ?></span>
                                    <?php endif; ?>
                                </div>
                            </li>


                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <?php $b++;
    endforeach ?>
</section>

<section class="section-portail -club">
    <?php get_template_part('template-parts/portail'); ?>
</section>
<?php
get_footer();
