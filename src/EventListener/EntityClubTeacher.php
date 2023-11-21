<?php
// src/EventListener/EntityClubTeacher.php
namespace App\EventListener;

use App\Entity\ClubTeacher;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;

use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostRemoveEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;

use Doctrine\ORM\Events;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[AsEntityListener(event: Events::postPersist, method: 'clubTeacher', entity: ClubTeacher::class)]
#[AsEntityListener(event: Events::postRemove, method: 'clubTeacher', entity: ClubTeacher::class)]
#[AsEntityListener(event: Events::postUpdate, method: 'clubTeacher', entity: ClubTeacher::class)]
class EntityClubTeacher
{
    private ParameterBagInterface $parameters;

    public function __construct(ParameterBagInterface $parameters)
    {
        $this->parameters = $parameters;
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
