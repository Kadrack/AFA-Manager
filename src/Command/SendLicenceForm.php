<?php
// src/Command/SendLicenceForm.php
namespace App\Command;

use App\Entity\Club;
use App\Entity\Member;

use App\Service\EmailSender;
use App\Service\FileGenerator;

use DateTime;

use Doctrine\ORM\EntityManagerInterface;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Address;

use Twig\Environment;

use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

#[AsCommand(name: 'app:send-licence-form')]
class SendLicenceForm extends Command
{
    protected static $defaultDescription = 'Send renew form to club manager.';

    private EntityManagerInterface $entityManager;

    /**
     * @param EmailSender $email
     * @param EntityManagerInterface $entityManager
     * @param FileGenerator $fileGenerator
     * @param Environment $twig
     * @param ParameterBagInterface $parameters
     */
    public function __construct(private EmailSender $email, EntityManagerInterface $entityManager, private FileGenerator $fileGenerator, private Environment $twig, private ParameterBagInterface $parameters)
    {
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws TransportExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $today = new DateTime();

        $data = array();

        if ((intval($today->format('n')) + 2) > 12)
        {
            $data['MonthStart'] = intval($today->format('n')) - 10;

            $data['YearStart'] = intval($today->format('Y')) + 1;

            $data['MonthEnd'] = $data['MonthStart'] + 1;

            $data['YearEnd'] = $data['YearStart'];
        }
        elseif ((intval($today->format('n')) + 3) > 12)
        {
            $data['MonthStart'] = intval($today->format('n')) + 2;

            $data['YearStart'] = intval($today->format('Y'));

            $data['MonthEnd'] = $data['MonthStart'] - 9;

            $data['YearEnd'] = $data['YearStart'] + 1;
        }
        else
        {
            $data['MonthStart'] = intval($today->format('n')) + 1;

            $data['YearStart'] = intval($today->format('Y'));

            $data['MonthEnd'] = $data['MonthStart'] + 2;

            $data['YearEnd'] = $data['YearStart'];
        }

        $start = date('Y-m-d', mktime(0, 0, 0, $data['MonthStart'], 1, $data['YearStart']));
        $end   = date('Y-m-d', mktime(0, 0, 0, $data['MonthEnd'], 0, $data['YearEnd']));

        $clubs = $this->entityManager->getRepository(Club::class)->getClubList(null, null, true);

        foreach ($clubs as $club)
        {
            $fileList = array();

            $members = $this->entityManager->getRepository(Member::class)->getClubRenewForms($club, $start, $end);

            if (sizeof($members) > 0)
            {
                $data['NoMail'] = array();

                foreach ($members as $member)
                {
                    $form['Member'] = $member;

                    $licenceForm = $this->twig->render('Member/Print/licenceForm.html.twig', array('data' => $form));

                    $filename = str_replace(' ', '', $member->getMemberId() . '-' . strtolower($member->getMemberName()) . '.pdf');

                    $pdf = $this->fileGenerator->pdfGenerator($this->parameters->get('kernel.project_dir') . '/private/temp/' . $filename, $licenceForm);

                    if (str_contains($member->getMemberActualClub()->getClubMainManagerMail(), '@aikido.be'))
                    {
                        $email['From'] = $member->getMemberActualClub()->getClubMainManagerMail(true);
                    }
                    else
                    {
                        $email['From'] = new Address('afa-manager@aikido.be', $club->getClubMainManagerName() . ' via AFA-Manager');
                    }

                    $email['Attach']  = [$pdf];
                    $email['ReplyTo'] = $email['From'];
                    $email['Member']  = $member;

                    if (!is_null($member->getMemberEmail()))
                    {
                        $this->email->formToMember($email);
                    }
                    else
                    {
                        $data['NoMail'][] = $member;
                    }

                    $fileList[] = $pdf;
                }

                $data['Attach']   = $fileList;
                $data['From']     = new Address('afa@aikido.be', 'Secrétariat AFA');
                $data['ReplyTo']  = $data['From'];
                $data['Club']     = $club;
                $data['Inactive'] = $this->entityManager->getRepository(Member::class)->getRecentExpired($club);

                $this->email->formToClub($data);
            }
        }

        $this->email->endTask($data);

        return Command::SUCCESS;
    }

    protected function configure(): void
    {
        $this->setHelp('This command is dedicated to be used with Cron.');
    }
}