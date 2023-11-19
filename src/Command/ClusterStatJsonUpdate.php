<?php
// src/Command/ClusterStatJsonUpdate.php
namespace App\Command;

use App\Entity\Cluster;
use App\Entity\Member;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

#[AsCommand(name: 'app:cluster-stat-json-update', description: 'Update Composition & Stat json files for Wordpress.', hidden: false)]
class ClusterStatJsonUpdate extends Command
{
    private EntityManagerInterface $em;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;

        parent::__construct();
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     * @throws TransportExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $content = array();

        $list = array(1, 2, 3, 4, 5, 6, 9, 10);

        foreach ($list as $id)
        {
            $cluster = $this->em->getRepository(Cluster::class)->findOneBy(array('clusterId' => $id));

            $clusterMembers = array();

            foreach ($cluster->getClusterActiveMembers() as $clusterMember)
            {
                if ($clusterMember->getClusterMemberTitle(true) == 'Président(e)')
                {
                    $number = 1;
                }
                elseif ($clusterMember->getClusterMemberTitle(true) == 'Vice-Président(e)')
                {
                    $number = 2;
                }
                elseif ($clusterMember->getClusterMemberTitle(true) == 'Secrétaire' || $clusterMember->getClusterMemberTitle(true) == 'Secrétaire général(e)')
                {
                    $number = 3;
                }
                elseif ($clusterMember->getClusterMemberTitle(true) == 'Trésorier(ère) général(e)')
                {
                    $number = 4;
                }
                else
                {
                    $number = 5;
                }

                $clusterMembers[] = array(
                    'Firstname'   => $clusterMember->getClusterMemberFirstname(),
                    'Name'        => $clusterMember->getClusterMemberName(),
                    'Title'       => $clusterMember->getClusterMemberTitle(true),
                    'TitleNumber' => $number,
                    'Email'       => $id == 9 ? null : $clusterMember->getClusterMemberEmail(),
                    'Phone'       => $id == 9 ? null : $clusterMember->getClusterMember()?->getMemberPhone()
                );
            }

            $content[$id] = array('GroupName' => $cluster->getClusterName(), 'Members' => $clusterMembers);
        }

        file_put_contents('/home/fzns3054/aikido.be/wordpress/prod/wp-content/uploads/json/composition.json', json_encode($content));

        $stat = $this->em->getRepository(Member::class)->getStatGrade();

        file_put_contents('/home/fzns3054/aikido.be/wordpress/prod/wp-content/uploads/json/stat.json', json_encode($stat));

        return Command::SUCCESS;
    }

    protected function configure(): void
    {
        $this->setHelp('This command is dedicated to be used daily with Cron.');
    }
}
