<?php
// src/EventListener/EntityClubClass.php
namespace App\EventListener;

use App\Entity\ClubClass;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;

use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostRemoveEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;

use Doctrine\ORM\Events;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[AsEntityListener(event: Events::postPersist, method: 'clubClass', entity: ClubClass::class)]
#[AsEntityListener(event: Events::postRemove, method: 'clubClass', entity: ClubClass::class)]
#[AsEntityListener(event: Events::postUpdate, method: 'clubClass', entity: ClubClass::class)]
class EntityClubClass
{
    private ParameterBagInterface $parameters;

    public function __construct(ParameterBagInterface $parameters)
    {
        $this->parameters = $parameters;
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
}
