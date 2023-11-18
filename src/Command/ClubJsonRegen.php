<?php
// src/Command/ClubJsonRegen.php
namespace App\Command;

use App\Entity\Club;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

#[AsCommand(name: 'app:club-json-regen', description: 'Regen clubs.json file for Wordpress.', hidden: false)]
class ClubJsonRegen extends Command
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

        $list = $this->em->getRepository(Club::class)->getClubList(null);

        foreach ($list as $club)
        {
            $classes = array();

            foreach ($club->getClubClasses() as $class)

            {
                $classes[] = array(
                    'Day'   => $class->getClubClassDay(true),
                    'Start' => $class->getClubClassStart(true),
                    'End'   => $class->getClubClassEnd(true),
                    'Type'  => $class->getClubClassType(true)
                );
            }

            $dojos = array();

            foreach($club->getClubDojos() as $dojo)
            {
                $dojos[] = array(
                    'Address' => $dojo->getClubDojoStreet(),
                    'Zip'     => $dojo->getClubDojoZip(),
                    'City'    => $dojo->getClubDojoCity()
                );
            }

            $teachers = array();

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

            $content[$club->getClubId()] = array(
                'Name'      => $club->getClubName(),
                'Province'  => $club->getClubProvince(true),
                'Email'     => $club->getClubEmail(),
                'Phone'     => $club->getClubPhone(),
                'Contact'   => $club->getClubContact(),
                'Url'       => $club->getClubUrl(),
                'Facebook'  => $club->getClubFacebook(),
                'Instagram' => $club->getClubInstagram(),
                'Youtube'   => $club->getClubYoutube(),
                'Type'      => $club->getClubClassType(),
                'Classes'   => $classes,
                'Dojos'     => $dojos,
                'Teachers'  => $teachers
            );
        }

        file_put_contents('/home/fzns3054/aikido.be/wordpress/prod/wp-content/uploads/json/clubs.json', json_encode($content));

        return Command::SUCCESS;
    }

    protected function configure(): void
    {
        $this->setHelp('Command to regen clubs.json file.');
    }
}
