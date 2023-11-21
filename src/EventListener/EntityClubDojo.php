<?php
// src/EventListener/EntityClubDojo.php
namespace App\EventListener;

use App\Entity\ClubDojo;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;

use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostRemoveEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;

use Doctrine\ORM\Events;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[AsEntityListener(event: Events::postPersist, method: 'clubDojo', entity: ClubDojo::class)]
#[AsEntityListener(event: Events::postRemove, method: 'clubDojo', entity: ClubDojo::class)]
#[AsEntityListener(event: Events::postUpdate, method: 'clubDojo', entity: ClubDojo::class)]
class EntityClubDojo
{
    private ParameterBagInterface $parameters;

    public function __construct(ParameterBagInterface $parameters)
    {
        $this->parameters = $parameters;
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
}
