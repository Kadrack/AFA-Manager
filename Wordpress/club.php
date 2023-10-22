<?php
/* Template Name: Club */
get_header();

?>
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
/* Liste des clubs actif */
$sqlClub = "SELECT c.club_id AS Id, c.club_name AS Name, c.club_province as Province, c.club_zip as Zip, c.club_phone_public as Phone, c.club_contact_public as Contact, c.club_email_public AS Email, c.club_url as Site, c.club_facebook AS Facebook
FROM club c
INNER JOIN club_history h ON (h.club_history_join_club = c.club_id )
GROUP BY c.club_id
HAVING max(h.club_history_status) = 1
ORDER BY c.club_id ASC";

$clubs = $db->query($sqlClub)->fetchAll();
//var_dump($club);
?>

<?php $grades = array(
    1 => '6ème kyu',
    2 => '5ème kyu',
    3 => '4ème kyu',
    4 => '3ème kyu',
    5 => '2ème kyu',
    6 => '1er kyu',
    7 => 'Shodan National',
    8 => 'Shodan Aïkikaï',
    9 => 'Nidan National',
    10 => 'Nidan Aïkikaï',
    11 => 'Sandan National',
    12 => 'Sandan Aïkikaï',
    13 => 'Yondan National',
    14 => 'Yondan Aïkikaï',
    15 => 'Godan National',
    16 => 'Godan Aïkikaï',
    17 => 'Rokudan National',
    18 => 'Rokudan Aïkikaï',
    19 => 'Nanadan National',
    20 => 'Nanadan Aïkikaï',
    21 => 'Hachidan National',
    22 => 'Hachidan Aïkikaï',
    23 => 'Kudan National',
    24 => 'Kudan Aïkikaï'
); ?>

<?php $fonctions = array(
    1 => 'Dojo Cho',
    2 => 'Professeur',
    3 => 'Assistant'
); ?>

<?php $allTitles = array(
    1 => 'Fuku Shidoïn',
    2 => 'Shidoïn',
    3 => 'Shihan',
    4 => 'Initiateur',
    5 => 'Aide-Moniteur',
    6 => 'Moniteur',
    7 => 'Moniteur Animateur',
    8 => 'Moniteur Initiateur',
    9 => 'Moniteur Educateur',
    10 => 'Autre'
); ?>

<?php $adepsTitles = array(
    1 => 'Initiateur',
    2 => 'Aide-Moniteur',
    3 => 'Moniteur',
    4 => 'Moniteur Animateur',
    5 => 'Moniteur Initiateur',
    6 => 'Moniteur Educateur',
); ?>

<?php
$week = array(
    1 => "Lundi",
    2 => "Mardi",
    3 => "Mercredi",
    4 => "Jeudi",
    5 => "Vendredi",
    6 => "Samedi",
    7 => "Dimanche",
) ?>

<?php $lessonTypes = array(
    1 => 'Cours Adultes', 2 => 'Cours Enfants', 3 => 'Cours Adultes/Enfants'
) ?>

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
            <?php foreach ($clubs as $club) : ?>
                <?php
                $listAdresses = [];
                $clubId = $club['Id'];
                $sqlDojo = "SELECT d.club_dojo_id AS Id, d.club_dojo_name AS Name, d.club_dojo_street AS Address, d.club_dojo_zip AS ZIP, d.club_dojo_city AS City, d.club_dojo_tatamis AS Tatamis, d.club_dojo_dea AS DEA, d.club_dojo_join_club AS Club
                FROM club_dojo d
                WHERE d.club_dojo_join_club = $clubId";
                ?>
                <?php $dojos = $db->query($sqlDojo)->fetchAll(); ?>
                <!-- Toutes les provinces -->
                <?php if ($i === 1) : ?>
                    <div class="club">
                    <div class="image-container">
                        <img class="club-image" src="https://afamanager.aikido.be/uploads/clubs/<?php echo $club['Id'] ?>.png" alt="<?php echo $club['Name'] ?>">
                        <?php // echo get_template_directory_uri() . '/assets/img/logo-footer-1.png'
                        ?>
                    </div>
                    <div class="inner-content">
                        <h3><?php echo $club['Name'] ?></h3>
                        <div class="city">
                            <?php if (count($dojos)) : ?>
                                <p><?php echo $dojos[0]['ZIP'] ?></p>
                                <p><?php echo $dojos[0]['City'] ?></p>
                            <?php endif; ?>
                        </div>
                        <?php $idnumber = intval($club['Id']);
                        $sqlHourly = "SELECT l.club_class_id AS Id, l.club_class_day AS Day, l.club_class_starting_hour AS Start, l.club_class_ending_hour AS End, l.club_class_type AS Type, l.club_class_join_club_dojo AS Dojo
                        FROM club_class l
                        WHERE l.club_class_join_club = $idnumber
                        ORDER BY Day ASC, Start ASC";
                        $hours = $db->query($sqlHourly)->fetchAll();
                        ?>
                        <p class="public">
                            <?php $type = []; ?>
                            <?php foreach ($hours as $hour): ?>
                                <?php $type[] = intval($hour['Type']); ?>
                            <?php endforeach; ?>
                            <?php if (in_array(3, $type) || in_array(2, $type)): ?>
                                Cours adultes/enfants
                            <?php else: ?>
                                Cours adultes
                            <?php endif; ?>
                        </p>
                        <?php foreach ($dojos as $dojo): ?>
                            <?php $listAdresses[] = $dojo['Address'] . " " . $dojo['ZIP'] . " " . $dojo['City']; ?>
                        <?php endforeach; ?>
                        <?php $stringAdresses = implode(',', $listAdresses) ?>

                        <div class="info container-tabs">
                            <div class="cta-container nav-club">
                                <a data-club-tab="#club-<?php echo $club['Id']; ?>" class="discover-club cta -contrast" href="#map-clubs" data-address="<?php echo $stringAdresses ?>">Découvrir</a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Brabant -->
              <?php elseif ($i === 2 && $club['Province'] === 2) : ?>
                    <div class="club">
                    <div class="image-container">
                        <img src="https://afamanager.aikido.be/uploads/clubs/<?php echo $club['Id'] ?>.png" alt="<?php echo $club['Name'] ?>">
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
                            <?php $type = []; ?>
                            <?php foreach ($hours as $hour): ?>
                                <?php $type[] = intval($hour['Type']); ?>
                            <?php endforeach; ?>
                            <?php if (in_array(3, $type) || in_array(2, $type)): ?>
                                Cours adultes/enfants
                            <?php else: ?>
                                Cours adultes
                            <?php endif; ?>
                        </p>
                        <?php foreach ($dojos as $dojo): ?>
                            <?php $listAdresses[] = $dojo['Address'] . " " . $dojo['ZIP'] . " " . $dojo['City']; ?>
                        <?php endforeach; ?>
                        <?php $stringAdresses = implode(',', $listAdresses) ?>

                        <div class="info container-tabs">
                            <div class="cta-container nav-club">
                                <a data-club-tab="#club-<?php echo $club['Id']; ?>" class="discover-club cta -contrast" href="#map-clubs" data-address="<?php echo $stringAdresses ?>">Découvrir</a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Brabant Flamand -->
              <?php elseif ($i === 3 && $club['Province'] === 7) : ?>
                    <div class="club">
                    <div class="image-container">
                        <img src="https://afamanager.aikido.be/uploads/clubs/<?php echo $club['Id'] ?>.png" alt="<?php echo $club['Name'] ?>">
                    </div>
                    <div class="inner-content">
                        <h3><?php echo $club['Name'] ?></h3>
                        <div class="city">
                            <?php if (count($dojos)) : ?>
                                <p><?php echo $dojos[0]['ZIP'] ?></p>
                                <p><?php echo $dojos[0]['City'] ?></p>
                            <?php endif; ?>
                        </div>
                        <?php $idnumber = intval($club['Id']);
                        $sqlHourly = "SELECT l.club_class_id AS Id, l.club_class_day AS Day, l.club_class_starting_hour AS Start, l.club_class_ending_hour AS End, l.club_class_type AS Type, l.club_class_join_club_dojo AS Dojo
                        FROM club_class l
                        WHERE l.club_class_join_club = $idnumber
                        ORDER BY Day ASC, Start ASC";
                        $hours = $db->query($sqlHourly)->fetchAll();
                        ?>
                        <p class="public">
                            <?php $type = []; ?>
                            <?php foreach ($hours as $hour): ?>
                                <?php $type[] = intval($hour['Type']); ?>
                            <?php endforeach; ?>
                            <?php if (in_array(3, $type) || in_array(2, $type)): ?>
                                Cours adultes/enfants
                            <?php else: ?>
                                Cours adultes
                            <?php endif; ?>
                        </p>
                        <?php foreach ($dojos as $dojo): ?>
                            <?php $listAdresses[] = $dojo['Address'] . " " . $dojo['ZIP'] . " " . $dojo['City']; ?>
                        <?php endforeach; ?>
                        <?php $stringAdresses = implode(',', $listAdresses) ?>

                        <div class="info container-tabs">
                            <div class="cta-container nav-club">
                                <a data-club-tab="#club-<?php echo $club['Id']; ?>" class="discover-club cta -contrast" href="#map-clubs" data-address="<?php echo $stringAdresses ?>">Découvrir</a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Bruxelles -->
              <?php elseif ($i === 4 && $club['Province'] === 1) : ?>
                    <div class="club">
                    <div class="image-container">
                        <img src="https://afamanager.aikido.be/uploads/clubs/<?php echo $club['Id'] ?>.png" alt="<?php echo $club['Name'] ?>">
                    </div>
                    <div class="inner-content">
                        <h3><?php echo $club['Name'] ?></h3>
                        <div class="city">
                            <?php if (count($dojos)) : ?>
                                <p><?php echo $dojos[0]['ZIP'] ?></p>
                                <p><?php echo $dojos[0]['City'] ?></p>
                            <?php endif; ?>
                        </div>
                        <?php $idnumber = intval($club['Id']);
                        $sqlHourly = "SELECT l.club_class_id AS Id, l.club_class_day AS Day, l.club_class_starting_hour AS Start, l.club_class_ending_hour AS End, l.club_class_type AS Type, l.club_class_join_club_dojo AS Dojo
                        FROM club_class l
                        WHERE l.club_class_join_club = $idnumber
                        ORDER BY Day ASC, Start ASC";
                        $hours = $db->query($sqlHourly)->fetchAll();
                        ?>
                        <p class="public">
                            <?php $type = []; ?>
                            <?php foreach ($hours as $hour): ?>
                                <?php $type[] = intval($hour['Type']); ?>
                            <?php endforeach; ?>
                            <?php if (in_array(3, $type) || in_array(2, $type)): ?>
                                Cours adultes/enfants
                            <?php else: ?>
                                Cours adultes
                            <?php endif; ?>
                        </p>
                        <?php foreach ($dojos as $dojo): ?>
                            <?php $listAdresses[] = $dojo['Address'] . " " . $dojo['ZIP'] . " " . $dojo['City']; ?>
                        <?php endforeach; ?>
                        <?php $stringAdresses = implode(',', $listAdresses) ?>

                        <div class="info container-tabs">
                            <div class="cta-container nav-club">
                                <a data-club-tab="#club-<?php echo $club['Id']; ?>" class="discover-club cta -contrast" href="#map-clubs" data-address="<?php echo $stringAdresses ?>">Découvrir</a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Hainaut -->
              <?php elseif ($i === 5 && $club['Province'] === 3) : ?>
                    <div class="club">
                    <div class="image-container">
                        <img src="https://afamanager.aikido.be/uploads/clubs/<?php echo $club['Id'] ?>.png" alt="<?php echo $club['Name'] ?>">
                    </div>
                    <div class="inner-content">
                        <h3><?php echo $club['Name'] ?></h3>
                        <div class="city">
                            <?php if (count($dojos)) : ?>
                                <p><?php echo $dojos[0]['ZIP'] ?></p>
                                <p><?php echo $dojos[0]['City'] ?></p>
                            <?php endif; ?>
                        </div>
                        <?php $idnumber = intval($club['Id']);
                        $sqlHourly = "SELECT l.club_class_id AS Id, l.club_class_day AS Day, l.club_class_starting_hour AS Start, l.club_class_ending_hour AS End, l.club_class_type AS Type, l.club_class_join_club_dojo AS Dojo
                        FROM club_class l
                        WHERE l.club_class_join_club = $idnumber
                        ORDER BY Day ASC, Start ASC";
                        $hours = $db->query($sqlHourly)->fetchAll();
                        ?>
                        <p class="public">
                            <?php $type = []; ?>
                            <?php foreach ($hours as $hour): ?>
                                <?php $type[] = intval($hour['Type']); ?>
                            <?php endforeach; ?>
                            <?php if (in_array(3, $type) || in_array(2, $type)): ?>
                                Cours adultes/enfants
                            <?php else: ?>
                                Cours adultes
                            <?php endif; ?>
                        </p>
                        <?php foreach ($dojos as $dojo): ?>
                            <?php $listAdresses[] = $dojo['Address'] . " " . $dojo['ZIP'] . " " . $dojo['City']; ?>
                        <?php endforeach; ?>
                        <?php $stringAdresses = implode(',', $listAdresses) ?>

                        <div class="info container-tabs">
                            <div class="cta-container nav-club">
                                <a data-club-tab="#club-<?php echo $club['Id']; ?>" class="discover-club cta -contrast" href="#map-clubs" data-address="<?php echo $stringAdresses ?>">Découvrir</a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Liège -->
              <?php elseif ($i === 6 && $club['Province'] === 4) : ?>
                    <div class="club">
                    <div class="image-container">
                        <img src="https://afamanager.aikido.be/uploads/clubs/<?php echo $club['Id'] ?>.png" alt="<?php echo $club['Name'] ?>">
                    </div>
                    <div class="inner-content">
                        <h3><?php echo $club['Name'] ?></h3>
                        <div class="city">
                            <?php if (count($dojos)) : ?>
                                <p><?php echo $dojos[0]['ZIP'] ?></p>
                                <p><?php echo $dojos[0]['City'] ?></p>
                            <?php endif; ?>
                        </div>
                        <?php $idnumber = intval($club['Id']);
                        $sqlHourly = "SELECT l.club_class_id AS Id, l.club_class_day AS Day, l.club_class_starting_hour AS Start, l.club_class_ending_hour AS End, l.club_class_type AS Type, l.club_class_join_club_dojo AS Dojo
                        FROM club_class l
                        WHERE l.club_class_join_club = $idnumber
                        ORDER BY Day ASC, Start ASC";
                        $hours = $db->query($sqlHourly)->fetchAll();
                        ?>
                        <p class="public">
                            <?php $type = []; ?>
                            <?php foreach ($hours as $hour): ?>
                                <?php $type[] = intval($hour['Type']); ?>
                            <?php endforeach; ?>
                            <?php if (in_array(3, $type) || in_array(2, $type)): ?>
                                Cours adultes/enfants
                            <?php else: ?>
                                Cours adultes
                            <?php endif; ?>
                        </p>
                        <?php foreach ($dojos as $dojo): ?>
                            <?php $listAdresses[] = $dojo['Address'] . " " . $dojo['ZIP'] . " " . $dojo['City']; ?>
                        <?php endforeach; ?>
                        <?php $stringAdresses = implode(',', $listAdresses) ?>

                        <div class="info container-tabs">
                            <div class="cta-container nav-club">
                                <a data-club-tab="#club-<?php echo $club['Id']; ?>" class="discover-club cta -contrast" href="#map-clubs" data-address="<?php echo $stringAdresses ?>">Découvrir</a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Luxembourg -->
              <?php elseif ($i === 7 && $club['Province'] === 5) : ?>
                    <div class="club">
                    <div class="image-container">
                        <img src="https://afamanager.aikido.be/uploads/clubs/<?php echo $club['Id'] ?>.png" alt="<?php echo $club['Name'] ?>">
                    </div>
                    <div class="inner-content">
                        <h3><?php echo $club['Name'] ?></h3>
                        <div class="city">
                            <?php if (count($dojos)) : ?>
                                <p><?php echo $dojos[0]['ZIP'] ?></p>
                                <p><?php echo $dojos[0]['City'] ?></p>
                            <?php endif; ?>
                        </div>
                        <?php $idnumber = intval($club['Id']);
                        $sqlHourly = "SELECT l.club_class_id AS Id, l.club_class_day AS Day, l.club_class_starting_hour AS Start, l.club_class_ending_hour AS End, l.club_class_type AS Type, l.club_class_join_club_dojo AS Dojo
                        FROM club_class l
                        WHERE l.club_class_join_club = $idnumber
                        ORDER BY Day ASC, Start ASC";
                        $hours = $db->query($sqlHourly)->fetchAll();
                        ?>
                        <p class="public">
                            <?php $type = []; ?>
                            <?php foreach ($hours as $hour): ?>
                                <?php $type[] = intval($hour['Type']); ?>
                            <?php endforeach; ?>
                            <?php if (in_array(3, $type) || in_array(2, $type)): ?>
                                Cours adultes/enfants
                            <?php else: ?>
                                Cours adultes
                            <?php endif; ?>
                        </p>
                        <?php foreach ($dojos as $dojo): ?>
                            <?php $listAdresses[] = $dojo['Address'] . " " . $dojo['ZIP'] . " " . $dojo['City']; ?>
                        <?php endforeach; ?>
                        <?php $stringAdresses = implode(',', $listAdresses) ?>

                        <div class="info container-tabs">
                            <div class="cta-container nav-club">
                                <a data-club-tab="#club-<?php echo $club['Id']; ?>" class="discover-club cta -contrast" href="#map-clubs" data-address="<?php echo $stringAdresses ?>">Découvrir</a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Namur -->
              <?php elseif ($i === 8 && $club['Province'] === 6) : ?>
                    <div class="club">
                    <div class="image-container">
                        <img src="https://afamanager.aikido.be/uploads/clubs/<?php echo $club['Id'] ?>.png" alt="<?php echo $club['Name'] ?>">
                    </div>
                    <div class="inner-content">
                        <h3><?php echo $club['Name'] ?></h3>
                        <div class="city">
                            <?php if (count($dojos)) : ?>
                                <p><?php echo $dojos[0]['ZIP'] ?></p>
                                <p><?php echo $dojos[0]['City'] ?></p>
                            <?php endif; ?>
                        </div>
                        <?php $idnumber = intval($club['Id']);
                        $sqlHourly = "SELECT l.club_class_id AS Id, l.club_class_day AS Day, l.club_class_starting_hour AS Start, l.club_class_ending_hour AS End, l.club_class_type AS Type, l.club_class_join_club_dojo AS Dojo
                        FROM club_class l
                        WHERE l.club_class_join_club = $idnumber
                        ORDER BY Day ASC, Start ASC";
                        $hours = $db->query($sqlHourly)->fetchAll();
                        ?>
                        <p class="public">
                            <?php $type = []; ?>
                            <?php foreach ($hours as $hour): ?>
                                <?php $type[] = intval($hour['Type']); ?>
                            <?php endforeach; ?>
                            <?php if (in_array(3, $type) || in_array(2, $type)): ?>
                                Cours adultes/enfants
                            <?php else: ?>
                                Cours adultes
                            <?php endif; ?>
                        </p>
                        <?php foreach ($dojos as $dojo): ?>
                            <?php $listAdresses[] = $dojo['Address'] . " " . $dojo['ZIP'] . " " . $dojo['City']; ?>
                        <?php endforeach; ?>
                        <?php $stringAdresses = implode(',', $listAdresses) ?>

                        <div class="info container-tabs">
                            <div class="cta-container nav-club">
                                <a data-club-tab="#club-<?php echo $club['Id']; ?>" class="discover-club cta -contrast" href="#map-clubs" data-address="<?php echo $stringAdresses ?>">Découvrir</a>
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
        <?php foreach ($clubs as $club) : ?>
        <?php $postcode = $club['ZIP'] ?>
        <?php $adress = $club['Address'] ?>
        <?php $commune = $club['club_city'] ?>
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
    foreach ($clubs as $club) : ?>
    <div class="club-content <?php if ($b == 1) : ?><?php endif; ?>" id="club-<?php echo $club['Id']; ?>">
        <div class="club-details">
            <div class="top-detail">
                <div class="province">
                    <h2>
                        <?php switch ($club['Province']) {
                            case "1":
                            echo ('Bruxelles');
                            break;
                            case "2":
                            echo ('Brabant');
                            break;
                            case "3":
                            echo ('Hainaut');
                            break;
                            case "4":
                            echo ('Liège');
                            break;
                            case "5":
                            echo ('Luxembourg');
                            break;
                            case "6":
                            echo ('Namur');
                            break;
                            case "7":
                            echo ('Brabant');
                            break;
                        } ?>
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
                    <?php
                    $clubId = $club['Id'];
                    $sqlDojos = "SELECT d.club_dojo_id AS Id, d.club_dojo_name AS Name, d.club_dojo_street AS Address, d.club_dojo_zip AS ZIP, d.club_dojo_city AS City, d.club_dojo_tatamis AS Tatamis, d.club_dojo_dea AS DEA, d.club_dojo_join_club AS Club
                    FROM club_dojo d
                    WHERE d.club_dojo_join_club = $clubId";
                    ?>
                    <?php $dojos = $db->query($sqlDojos)->fetchAll(); ?>
                    <?php foreach ($dojos as $dojo) : ?>
                        <div class="city">
                            <p><?php echo $dojo['Address'] ?></p>
                            <p><?php echo $dojo['ZIP'] ?> <?php echo $dojo['City'] ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="bottom-detail">
                <div class="hourly content">
                    <p class="title">Horaires</p>
                    <?php
                    $idnumber = intval($club['Id']);
                    $sqlHourly = "SELECT l.club_class_id AS Id, l.club_class_day AS Day, l.club_class_starting_hour AS Start, l.club_class_ending_hour AS End, l.club_class_type AS Type, l.club_class_join_club_dojo AS Dojo
                    FROM club_class l
                    WHERE l.club_class_join_club = $idnumber
                    ORDER BY Day ASC, Start ASC";
                    $hours = $db->query($sqlHourly)->fetchAll();
                    ?>
                    <ul class="list">
                        <?php foreach ($hours as $hour) : ?>
                            <?php
                            $day = intval($hour['Day']);
                            $start = $hour['Start'];
                            $end = $hour['End'];
                            $type = intval($hour['Type']);
                            $dojo = $hour['Dojo'];
                            ?>
                            <li class="item">
                                <?php echo $week[$hour['Day']] ?>
                                de <?php echo date("G", strtotime($start)) ?>h<?php echo date("i", strtotime($start)) ?>
                                à <?php echo date("G", strtotime($end)) ?>h<?php echo date("i", strtotime($end)) ?>
                                (<?php echo $lessonTypes[$type] ?>)
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="teachers content">
                    <div class="title">Professeurs</div>
                    <?php
                    $idnumber = intval($club['Id']);
                    $sqlAfaTeachers =
                    "SELECT t.club_teacher_id AS Id, t.club_teacher_title AS Function, t.club_teacher_type AS Type, m.member_firstname AS Firstname, m.member_name AS Name, max(g.grade_rank) AS Grade, max(ti.title_rank) AS Title, max(f.formation_rank) AS ADEPS
                    FROM club_teacher t
                    INNER JOIN member m ON (m.member_id = t.club_teacher_join_member)
                    INNER JOIN grade g ON (m.member_id = g.grade_join_member)
                    LEFT JOIN title ti ON (m.member_id = ti.title_join_member)
                    LEFT JOIN formation f ON (m.member_id = f.formation_join_member)
                    WHERE t.club_teacher_join_member IS NOT NULL AND t.club_teacher_join_club = $idnumber AND g.grade_status <> 4
                    GROUP BY Id
                    ORDER BY Function ASC, Grade DESC";

                    $teachers = $db->query($sqlAfaTeachers)->fetchAll();
                    ?>
                    <ul class="list">
                        <?php $results = array(); ?>
                        <?php foreach ($teachers as $teacher) : ?>
                            <li class="item <?php if ($teacher['Function'] == 1) :?>-marginbottom<?php endif; ?>">
                                <div class="name">
                                    <strong><?php echo $teacher['Name'] ?> <?php echo $teacher['Firstname'] ?></strong>
                                    <?php $title = $teacher['Title'] ?>
                                    <?php if ($title) : ?>
                                        <div class="title"> <?php echo $allTitles[$title]; ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="infos">
                                    <?php if ($teacher['Function'] != "") : ?>
                                        <?php $functionNumber = $teacher['Function'] ?>
                                        <span><?php echo $fonctions[$functionNumber] ?></span>
                                    <?php endif; ?>
                                    <?php if ($teacher['Grade'] != "" && $teacher['Grade'] > 6) : ?>
                                        <?php $gradeNumber = $teacher['Grade'] ?>
                                        <span><?php echo $grades[$gradeNumber] ?></span>
                                    <?php endif; ?>
                                    <?php $adeps = intval($teacher['ADEPS']) ?>
                                    <?php if ($adeps) : ?>
                                        <span> <?php echo $adepsTitles[$adeps]; ?></span>
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
