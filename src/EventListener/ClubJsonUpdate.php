<?php
// src/EventListener/ClubJsonUpdate.php
namespace App\EventListener;

use App\Entity\Club;
use App\Entity\ClubClass;
use App\Entity\ClubDojo;
use App\Entity\ClubTeacher;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;

use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostRemoveEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;

use Doctrine\ORM\Events;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[AsEntityListener(event: Events::postPersist, method: 'club', entity: Club::class)]
#[AsEntityListener(event: Events::postRemove, method: 'club', entity: Club::class)]
#[AsEntityListener(event: Events::postUpdate, method: 'club', entity: Club::class)]
#[AsEntityListener(event: Events::postPersist, method: 'clubClass', entity: ClubClass::class)]
#[AsEntityListener(event: Events::postRemove, method: 'clubClass', entity: ClubClass::class)]
#[AsEntityListener(event: Events::postUpdate, method: 'clubClass', entity: ClubClass::class)]
#[AsEntityListener(event: Events::postPersist, method: 'clubDojo', entity: ClubDojo::class)]
#[AsEntityListener(event: Events::postRemove, method: 'clubDojo', entity: ClubDojo::class)]
#[AsEntityListener(event: Events::postUpdate, method: 'clubDojo', entity: ClubDojo::class)]
#[AsEntityListener(event: Events::postPersist, method: 'clubTeacher', entity: ClubTeacher::class)]
#[AsEntityListener(event: Events::postRemove, method: 'clubTeacher', entity: ClubTeacher::class)]
#[AsEntityListener(event: Events::postUpdate, method: 'clubTeacher', entity: ClubTeacher::class)]
class ClubJsonUpdate
{
    private ParameterBagInterface $parameters;

    public function __construct(ParameterBagInterface $parameters)
    {
        $this->parameters = $parameters;
    }

    public function club(Club $club, PostPersistEventArgs|PostRemoveEventArgs|PostUpdateEventArgs $event): void
    {
        if ($club->getClubIsActive())
        {
            $clubs = json_decode(file_get_contents('/home/fzns3054/aikido.be/wordpress/prod/wp-content/uploads/json/clubs.json'), true);

            $clubs[$club->getClubId()]['Name'] = $club->getClubName();
            $clubs[$club->getClubId()]['Province'] = $club->getClubProvince(true);
            $clubs[$club->getClubId()]['Email'] = $club->getClubEmail();
            $clubs[$club->getClubId()]['Phone'] = $club->getClubPhone();
            $clubs[$club->getClubId()]['Contact'] = $club->getClubContact();
            $clubs[$club->getClubId()]['Url'] = $club->getClubUrl();
            $clubs[$club->getClubId()]['Facebook'] = $club->getClubFacebook();
            $clubs[$club->getClubId()]['Instagram'] = $club->getClubInstagram();
            $clubs[$club->getClubId()]['Youtube'] = $club->getClubYoutube();
            $clubs[$club->getClubId()]['Type'] = $club->getClubClassType();

            if ($this->parameters->get('kernel.environment') == 'prod')
            {
                file_put_contents('/home/fzns3054/aikido.be/wordpress/prod/wp-content/uploads/json/clubs.json', json_encode($clubs));
            }
        }
    }

    public function clubClass(ClubClass $clubClass, PostPersistEventArgs|PostRemoveEventArgs|PostUpdateEventArgs $event): void
    {
        if ($club->getClubIsActive())
        {
            $clubs = json_decode(file_get_contents('/home/fzns3054/aikido.be/wordpress/prod/wp-content/uploads/json/clubs.json'), true);

            $club = $clubClass->getClubClassClub();

            foreach ($club->getClubClasses() as $class)
            {
                $classes[] = array(
                    'Day'   => $class->getClubClassDay(true),
                    'Start' => $class->getClubClassStart(true),
                    'End'   => $class->getClubClassEnd(true),
                    'Type'  => $class->getClubClassType(true)
                );
            }

            $clubs[$club->getClubId()]['Classes'] = $classes;

            if ($this->parameters->get('kernel.environment') == 'prod')
            {
                file_put_contents('/home/fzns3054/aikido.be/wordpress/prod/wp-content/uploads/json/clubs.json', json_encode($clubs));
            }
        }
    }

    public function clubDojo(ClubDojo $clubDojo, PostPersistEventArgs|PostRemoveEventArgs|PostUpdateEventArgs $event): void
    {
        if ($club->getClubIsActive())
        {
            $clubs = json_decode(file_get_contents('/home/fzns3054/aikido.be/wordpress/prod/wp-content/uploads/json/clubs.json'), true);

            $club = $clubDojo->getClubDojoClub();

            foreach($club->getClubDojos() as $dojo)
            {
                $dojos[] = array(
                    'Address' => $dojo->getClubDojoStreet(),
                    'Zip'     => $dojo->getClubDojoZip(),
                    'City'    => $dojo->getClubDojoCity()
                );
            }

            $clubs[$club->getClubId()]['Dojos'] = $dojos;

            if ($this->parameters->get('kernel.environment') == 'prod')
            {
                file_put_contents('/home/fzns3054/aikido.be/wordpress/prod/wp-content/uploads/json/clubs.json', json_encode($clubs));
            }
        }
    }

    public function clubTeacher(ClubTeacher $clubTeacher, PostPersistEventArgs|PostRemoveEventArgs|PostUpdateEventArgs $event): void
    {
        if ($club->getClubIsActive())
        {
            $clubs = json_decode(file_get_contents('/home/fzns3054/aikido.be/wordpress/prod/wp-content/uploads/json/clubs.json'), true);

            $club = $clubTeacher->getClubTeacherClub();

            foreach ($club->getClubTeachers() as $teacher)
            {
                $teachers[] = array(
                    'Firstname' => $teacher->getClubTeacherFirstname(),
                    'Name'      => $teacher->getClubTeacherName(),
                    'Grade'     => $teacher->getClubTeacherGrade(true),
                    'Aikikai'   => $teacher->getClubTeacherTitleAikikai(true, true),
                    'Adeps'     => $teacher->getClubTeacherTitleAdeps(true, true),
                    'Title'     => $teacher->getClubTeacherTitle(true)
                );
            }

            $clubs[$club->getClubId()]['Teachers'] = $teachers;

            if ($this->parameters->get('kernel.environment') == 'prod')
            {
                file_put_contents('/home/fzns3054/aikido.be/wordpress/prod/wp-content/uploads/json/clubs.json', json_encode($clubs));
            }
        }
    }
}