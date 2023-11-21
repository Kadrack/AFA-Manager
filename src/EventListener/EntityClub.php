<?php
// src/EventListener/EntityClub.php
namespace App\EventListener;

use App\Entity\Club;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;

use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostRemoveEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;

use Doctrine\ORM\Events;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[AsEntityListener(event: Events::postPersist, method: 'club', entity: Club::class)]
#[AsEntityListener(event: Events::postRemove, method: 'club', entity: Club::class)]
#[AsEntityListener(event: Events::postUpdate, method: 'club', entity: Club::class)]
class EntityClub
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
}
