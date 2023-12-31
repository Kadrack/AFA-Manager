<?php
// src/Service/EmailSender.php
namespace App\Service;

use App\Entity\Club;
use App\Entity\ClubManager;
use App\Entity\ClubTeacher;
use App\Entity\ClusterMember;
use App\Entity\GradeSession;
use App\Entity\GradeSessionCandidate;
use App\Entity\Member;

use Doctrine\Persistence\ManagerRegistry;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;

use Symfony\Bundle\SecurityBundle\Security;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

/**
 * Class Tools
 * @package App\Service
 */
class EmailSender
{
    /**
     * @var array()
     */
    private array $email = array();

    /**
     * @var Access
     */
    private Access $access;

    /**
     * @var ListData
     */
    private ListData $listData;

    /**
     * @var ManagerRegistry
     */
    private ManagerRegistry $doctrine;

    /**
     * @var MailerInterface
     */
    private MailerInterface $mailer;

    /**
     * @var Security
     */
    private Security $security;

    /**
     * @var ParameterBagInterface
     */
    private ParameterBagInterface $parameters;

    /**
     * EmailSender constructor.
     * @param Access $access
     * @param MailerInterface $mailer
     * @param ListData $listData
     * @param ManagerRegistry $doctrine
     * @param ParameterBagInterface $parameters
     * @param Security $security
     */
    public function __construct(Access $access, MailerInterface $mailer, ListData $listData, ManagerRegistry $doctrine, ParameterBagInterface $parameters, Security $security)
    {
        $this->access     = $access;
        $this->doctrine   = $doctrine;
        $this->listData   = $listData;
        $this->mailer     = $mailer;
        $this->parameters = $parameters;
        $this->security   = $security;

        $this->getSender();
    }

    /**
     * @return void
     */
    public function getSender(): void
    {
        $user = $this->security->getUser();

        if (!is_null($user))
        {
            if (is_null($user->getMember()))
            {
                $this->email['Firstname'] = ucwords($user->getFirstname());
                $this->email['Name']      = ucwords($user->getName());
                $this->email['Phone']     = null;
                $this->email['ReplyTo']   = $user->getEmail();
            }
            else
            {
                $this->email['Firstname'] = ucwords($user->getMember()->getMemberFirstname());
                $this->email['Name']      = ucwords($user->getMember()->getMemberName());
                $this->email['Phone']     = $user->getMember()->getMemberPhone();
                $this->email['ReplyTo']   = $user->getMember()->getMemberEmail();
            }
        }

        $session = new Session;

        if ($session->has('Club'))
        {
            $this->email['Title']    = null;
            $this->email['ReplyTo']  = is_null($user->getEmail()) ? $user->getMember()->getMemberEmail() : $user->getEmail();
            $this->email['Official'] = false;
        }
        elseif ($session->has('Cluster'))
        {
            $this->email['Title']    = $session->get('Cluster')->getClusterMemberTitle();
            $this->email['ReplyTo']  = $session->get('Cluster')->getClusterMemberEmail();
            $this->email['Cluster']  = $session->get('Cluster')->getClusterMemberCluster()->getClusterName();
            $this->email['Official'] = true;
        }

        if (isset($this->email['ReplyTo']) && str_contains($this->email['ReplyTo'], '@aikido.be'))
        {
            $this->email['From'] = new Address($this->email['ReplyTo'], $this->email['Firstname'] . ' ' . $this->email['Name']);
        }
        else
        {
            $this->email['From'] = new Address('afa-manager@aikido.be', 'AFA-Manager');
        }
    }

    /**
     * @param string $subject
     * @return void
     */
    public function setSubject(string $subject): void
    {
        $this->email['Subject'] = $subject;
    }

    /**
     * @param string $content
     * @return void
     */
    public function setContent(string $content): void
    {
        $this->email['Content'] = $content;
    }

    /**
     * @param array $attachment
     * @return void
     */
    public function setAttachment(array $attachment): void
    {
        foreach ($attachment as $file)
        {
            $filename = preg_replace( '/[^a-zA-Z0-9_-]+/', '-', strtolower( $file->getClientOriginalName() ) ) . '.' . $file->guessExtension();

            $file->move($this->parameters->get('kernel.project_dir') . '/private/temp/' . $this->security->getUser()->getId(), $filename);

            $this->email['Attach'][] = $this->parameters->get('kernel.project_dir') . '/private/temp/' . $this->security->getUser()->getId() . '/' . $filename;
        }
    }

    /**
     * @param array $data
     * @return bool
     * @throws TransportExceptionInterface
     */
    public function formToMember(array $data): bool
    {
        $this->email['Attach']   = $data['Attach'];
        $this->email['From']     = $data['From'];
        $this->email['ReplyTo']  = $data['ReplyTo'];
        $this->email['To']       = [is_null($data['Member']->getMemberEmail()) ? null : new Address($data['Member']->getMemberEmail(), ucwords($data['Member']->getMemberFirstname()) . ' ' . ucwords($data['Member']->getMemberName()))];
        $this->email['Cc']       = array();
        $this->email['Bcc']      = array();
        $this->email['Subject']  = 'Renouvellement licence AFA';
        $this->email['Template'] = 'Mails/autoMailToMember.html.twig';
        $this->email['Context']  = array('subject' => $this->email['Subject'], 'data' => $data);

        $this->send();

        return true;
    }

    /**
     * @param array $data
     * @return bool
     * @throws TransportExceptionInterface
     */
    public function formToClub(array $data): bool
    {
        $this->email['Attach']   = $data['Attach'];
        $this->email['From']     = $data['From'];
        $this->email['ReplyTo']  = $data['ReplyTo'];
        $this->email['To']       = $data['Club']->getClubData('EmailManagers', true);
        $this->email['Cc']       = array();
        $this->email['Bcc']      = array(new Address('afa@aikido.be', 'Secrétariat AFA'));
        $this->email['Subject']  = 'Renouvellement licences ' . $this->listData->getMonth($data['MonthStart']);
        $this->email['Template'] = 'Mails/autoMailToClub.html.twig';
        $this->email['Context']  = array('subject' => $this->email['Subject'], 'data' => $data);

        $this->send();

        return true;
    }

    /**
     * @param array $data
     * @return bool
     * @throws TransportExceptionInterface
     */
    public function stampToClub(array $data): bool
    {
        $this->email['From']     = new Address('afa@aikido.be', 'Secrétariat AFA');
        $this->email['ReplyTo']  = new Address('afa@aikido.be', 'Secrétariat AFA');
        $this->email['To']       = $data['Club']->getClubData('EmailManagers', true);
        $this->email['Cc']       = array();
        $this->email['Bcc']      = array(new Address('afa@aikido.be', 'Secrétariat AFA'));
        $this->email['Subject']  = 'Envoie des timbres';
        $this->email['Template'] = 'Mails/autoMailStamp.html.twig';
        $this->email['Context']  = array('subject' => $this->email['Subject'], 'data' => $data);

        $this->send();

        return true;
    }

    /**
     * @param array $data
     * @return bool
     * @throws TransportExceptionInterface
     */
    public function endTask(array $data): bool
    {
        $this->email['Attach']   = null;
        $this->email['From']     = new Address('afa@aikido.be', 'Secrétariat AFA');
        $this->email['ReplyTo']  = $this->email['From'];
        $this->email['To']       = array(new Address('afa@aikido.be', 'Secrétariat AFA'));
        $this->email['Cc']       = array();
        $this->email['Bcc']      = array();
        $this->email['Subject']  = 'Fin Renouvellement licences ' . $this->listData->getMonth($data['MonthStart']);
        $this->email['Template'] = 'Mails/endTask.html.twig';
        $this->email['Context']  = array('subject' => $this->email['Subject']);

        $this->send();

        return true;
    }

    /**
     * @param Member $member
     * @param bool $manager
     * @param bool $dojoCho
     * @return bool
     * @throws TransportExceptionInterface
     */
    public function toMember(Member $member, bool $manager = false, bool $dojoCho = false): bool
    {
        $club = $member->getMemberActualClub();

        $staffEmail = array();

        if ($manager && $dojoCho)
        {
            $staffEmail = $club->getClubData('EmailStaff', true);
        }
        else
        {
            !$manager ?: $staffEmail = $club->getClubData('EmailManager');
            !$dojoCho ?: $staffEmail = $club->getClubData('EmailDojoCho');
        }

        $this->email['To']       = [is_null($member->getMemberEmail()) ? null : new Address($member->getMemberEmail(), ucwords($member->getMemberFirstname()) . ' ' . ucwords($member->getMemberName()))];
        $this->email['Cc']       = $staffEmail;
        $this->email['Bcc']      = array($this->email['From']);
        $this->email['Template'] = 'Mails/template.html.twig';
        $this->email['Context']  = array('data' => $this->email);

        $this->send();

        return true;
    }

    /**
     * @return array
     */
    public function getClubMemberMailingList(): array
    {
        $list = array();

        !$this->access->check('Mailing-ClubAdult')   ?: $list = array_merge($list, array('Adultes' => 1));
        !$this->access->check('Mailing-ClubChild')   ?: $list = array_merge($list, array('Enfants' => 2));
        !$this->access->check('Mailing-ClubTeacher') ?: $list = array_merge($list, array('Professeur(s) et Assistant(s)' => 3));
        !$this->access->check('Mailing-ClubManager') ?: $list = array_merge($list, array('Gestionnaire(s) du club' => 4));
        !$this->access->check('Mailing-OtherClubs')  ?: $list = array_merge($list, array('Les autres clubs' => 5));

        return $list;
    }

    /**
     * @param Club $club
     * @param int $list
     * @return bool
     * @throws TransportExceptionInterface
     */
    public function toClubMember(Club $club, int $list): bool
    {
        $this->email['To']       = array($this->email['ReplyTo']);
        $this->email['Cc']       = $club->getClubData('EmailStaff', true);
        $this->email['Bcc']      = array();
        $this->email['Template'] = 'Mails/template.html.twig';
        $this->email['Context']  = array('data' => $this->email);

        switch ($list)
        {
            case 1:
                foreach ($this->doctrine->getRepository(Member::class)->getClubActiveMemberList($club, true) as $member)
                {
                    is_null($member->getMemberEmail()) ?: $this->email['Bcc'][] = $member->getMemberEmail();
                }

                break;
            case 2:
                foreach ($this->doctrine->getRepository(Member::class)->getClubActiveMemberList($club, false) as $member)
                {
                    is_null($member->getMemberEmail()) ?: $this->email['Bcc'][] = $member->getMemberEmail();
                }

                break;
            case 3:
                foreach ($this->doctrine->getRepository(ClubTeacher::class)->findBy(array('clubTeacher' => $club->getClubId())) as $teacher)
                {
                    is_null($teacher->getClubTeacherEmail()) ?: $this->email['Bcc'][] = $teacher->getClubTeacherEmail(true);
                }

                break;
            case 4:
                $this->email['Bcc'] = $club->getClubData('EmailManagers');

                break;
            case 5:
                foreach ($this->doctrine->getRepository(ClubManager::class)->findBy(array()) as $manager)
                {
                    if ($manager->getClubManagerClub() === $club)
                    {
                        continue;
                    }

                    is_null($manager->getClubManagerEmail()) ?: $this->email['Bcc'][] = $manager->getClubManagerEmail(true);
                }

                break;
        }

        $this->send();

        return true;
    }

    /**
     * @return array
     */
    public function getMemberMailingList(): array
    {
        $list = array();

        !$this->access->check('Mailing-DojoCho')      ?: $list = array_merge($list, array('Dojo-Cho' => 1));
        !$this->access->check('Mailing-Manager')      ?: $list = array_merge($list, array('Gestionnaires de club' => 2));
        !$this->access->check('Mailing-TeacherAdult') ?: $list = array_merge($list, array('Professeurs Adultes' => 3));
        !$this->access->check('Mailing-TeacherChild') ?: $list = array_merge($list, array('Professeurs Enfants' => 4));
        !$this->access->check('Mailing-CA')           ?: $list = array_merge($list, array('Conseil d\'administration' => 5));
        !$this->access->check('Mailing-CT')           ?: $list = array_merge($list, array('Commission technique' => 6));
        !$this->access->check('Mailing-CJ')           ?: $list = array_merge($list, array('Commission junior' => 7));
        !$this->access->check('Mailing-CP')           ?: $list = array_merge($list, array('Commission pédagogique' => 8));
        !$this->access->check('Mailing-CPAnimateur')  ?: $list = array_merge($list, array('Candidats Animateur' => 9));
        !$this->access->check('Mailing-Global')       ?: $list = array_merge($list, array('Tous les membres' => 10));
        !$this->access->check('Mailing-Preview')      ?: $list = array_merge($list, array('Secrétariat' => 100));

        return $list;
    }

    /**
     * @param array $email
     * @param array $lists
     * @return bool
     * @throws TransportExceptionInterface
     */
    public function toMemberMailing(array $email, array $lists): bool
    {
        $this->email['Subject'] = $email['Subject'];
        $this->email['Content'] = $email['Content'];

        if (str_contains($this->email['ReplyTo'], '@aikido.be'))
        {
            $this->email['From'] = new Address($this->email['ReplyTo'], $this->email['Firstname'] . ' ' . $this->email['Name']);
        }
        else
        {
            $this->email['From'] = new Address('afamanager@aikido.be', 'AFA-Manager');
        }

        $this->email['To']       = array($this->email['ReplyTo']);
        $this->email['Cc']       = array();
        $this->email['Template'] = 'Mails/template.html.twig';
        $this->email['Context']  = array('data' => $this->email);

        $Bcc = array();

        foreach ($lists as $list)
        {
            switch ($list)
            {
                case 1:
                    foreach ($this->doctrine->getRepository(ClubTeacher::class)->findBy(array('clubTeacherTitle' => 1)) as $member)
                    {
                        is_null($member->getClubTeacherMember()?->getMemberEmail()) ?: $Bcc[] = $member->getClubTeacherMember()?->getMemberEmail();
                    }

                    break;
                case 2:
                    foreach ($this->doctrine->getRepository(ClubManager::class)->findAll() as $member)
                    {
                        is_null($member->getClubManagerMember()?->getMemberEmail()) ?: $Bcc[] = $member->getClubManagerMember()?->getMemberEmail();
                        is_null($member->getClubManagerUser()?->getEmail()) ?: $Bcc[] = $member->getClubManagerUser()?->getEmail();
                    }

                    break;
                case 3:
                    foreach ($this->doctrine->getRepository(ClubTeacher::class)->findBy(array('clubTeacherType' => array(1, 3))) as $member)
                    {
                        is_null($member->getClubTeacherMember()?->getMemberEmail()) ?: $Bcc[] = $member->getClubTeacherMember()?->getMemberEmail();
                    }

                    break;
                case 4:
                    foreach ($this->doctrine->getRepository(ClubTeacher::class)->findBy(array('clubTeacherType' => array(2, 3))) as $member)
                    {
                        is_null($member->getClubTeacherMember()?->getMemberEmail()) ?: $Bcc[] = $member->getClubTeacherMember()?->getMemberEmail();
                    }

                    break;
                case 5:
                    foreach ($this->doctrine->getRepository(ClusterMember::class)->findBy(array('clusterMemberCluster' => 3)) as $member)
                    {
                        !$member->getClusterMemberActive() ?: $Bcc[] = $member->getClusterMemberEmail();
                    }

                    break;
                case 6:
                    foreach ($this->doctrine->getRepository(ClusterMember::class)->findBy(array('clusterMemberCluster' => 1)) as $member)
                    {
                        !$member->getClusterMemberActive() ?: $Bcc[] = $member->getClusterMemberEmail();
                    }

                    break;
                case 7:
                    foreach ($this->doctrine->getRepository(ClusterMember::class)->findBy(array('clusterMemberCluster' => 2)) as $member)
                    {
                        !$member->getClusterMemberActive() ?: $Bcc[] = $member->getClusterMemberEmail();
                    }

                    break;
                case 8:
                    foreach ($this->doctrine->getRepository(ClusterMember::class)->findBy(array('clusterMemberCluster' => 4)) as $member)
                    {
                        !$member->getClusterMemberActive() ?: $Bcc[] = $member->getClusterMemberEmail();
                    }

                    break;
                case 9:
                    foreach ($this->doctrine->getRepository(Member::class)->getCPAnimateurCandidate() as $member)
                    {
                        $Bcc[] = $member->getMemberEmail();
                    }

                    break;
                case 10:
                    foreach ($this->doctrine->getRepository(Member::class)->getActiveMemberEmailList() as $member)
                    {
                        $Bcc[] = $member['Email'];
                    }

                    break;
                case 100:
                    $Bcc[] = array();

                    break;
            }
        }

        $Bcc = array_unique($Bcc);

        foreach ($Bcc as $email)
        {
            $this->email['Bcc'][] = $email;

            if (sizeof($this->email['Bcc']) >= 100)
            {
                $this->send();

                $this->email['Bcc'] = array();
            }
        }

        if (sizeof($this->email['Bcc']) >= 1)
        {
            $this->send();
        }

        return true;
    }

    /**
     * @param Member $member
     * @param GradeSession $gradeSession
     * @return bool
     * @throws TransportExceptionInterface
     */
    public function examApplicationValidated(Member $member, GradeSession $gradeSession): bool
    {
        $this->email['Attach']   = [$this->parameters->get('kernel.project_dir').'/private/Consignes.doc'];
        $this->email['Cc']       = $member->getMemberActualClub()->getClubData('EmailStaff', true);
        $this->email['Context']  = ['dateExam' => $gradeSession->getGradeSessionDate(), 'address' => $gradeSession->getGradeSessionStreet(), 'zip' => $gradeSession->getGradeSessionZip(), 'city' => $gradeSession->getGradeSessionCity()];
        $this->email['From']     = new Address('ct@aikido.be', 'Secrétariat CT');
        $this->email['ReplyTo']  = $this->email['From'];
        $this->email['Subject']  = 'Candidature validée';
        $this->email['Template'] = 'Grade/Email/validation.html.twig';
        $this->email['To']       = [is_null($member->getMemberEmail()) ? null : $member->getMemberEmail(true)];
        $this->email['Bcc']      = array($this->email['From']);

        if (is_null($this->email['To'][0]))
        {
            $this->email['To'] = $this->email['Cc'];
            $this->email['Cc'] = array();
        }

        $this->send();

        return true;
    }

    /**
     * @param Member $member
     * @param GradeSessionCandidate $gradeSession
     * @return bool
     * @throws TransportExceptionInterface
     */
    public function examApplicationRejected(Member $member, GradeSessionCandidate $gradeSession): bool
    {
        $this->email['Cc']       = $member->getMemberActualClub()->getClubData('Emailstaff');
        $this->email['Context']  = ['member' => $member, 'reason' => $gradeSession->getGradeSessionCandidateStaffComment()];
        $this->email['From']     = new Address('ct@aikido.be', 'Secrétariat CT');
        $this->email['ReplyTo']  = $this->email['From'];
        $this->email['Subject']  = 'Candidature refusée';
        $this->email['Template'] = 'Grade/Email/rejection.html.twig';
        $this->email['To']       = [is_null($member->getMemberEmail()) ? null : new Address($member->getMemberEmail(), ucwords($member->getMemberFirstname()) . ' ' . ucwords($member->getMemberName()))];
        $this->email['Bcc']      = array($this->email['From']);

        if (is_null($this->email['To'][0]))
        {
            $this->email['To'] = $this->email['Cc'];
            $this->email['Cc'] = array();
        }

        $this->send();

        return true;
    }

    /**
     * @param array $data
     * @param GradeSession $gradeSession
     * @return bool
     * @throws TransportExceptionInterface
     */
    public function examSchedule(array $data, GradeSession $gradeSession): bool
    {
        $this->email['Bcc'] = array();

        $candidates = $gradeSession->getGradeSessionCandidates();

        $club = array();

        foreach ($candidates as $candidate)
        {
            $club[$candidate->getCandidateMemberClub()->getClubId()] = true;

            if (($candidate->getGradeSessionCandidateStatus() == 1) && (!is_null($candidate->getCandidateMemberEmail())))
            {
                $this->email['Bcc'][] = new Address($candidate->getCandidateMemberEmail(), ucwords(($candidate->getCandidateMemberFirstname())) . ' ' . ucwords($candidate->getCandidateMemberName()));
            }
        }

        $dojoChos = $this->doctrine->getRepository(ClubTeacher::class)->getDojoCho(null, true);

        foreach ($dojoChos as $dojoCho)
        {
            if (!isset($club[$dojoCho->getMemberActualClub()->getClubId()]))
            {
                continue;
            }

            $this->email['Bcc'][] = new Address($dojoCho->getMemberEmail(), ucwords($dojoCho->getMemberFirstname()) . ' ' . ucwords($dojoCho->getMemberName()));
        }

        $this->email['Context']  = ['data' => $data, 'gradeSession' => $gradeSession];
        $this->email['From']     = new Address('ct@aikido.be', 'Secrétariat CT');
        $this->email['ReplyTo']  = $this->email['From'];
        $this->email['Cc']       = array();
        $this->email['Subject']  = 'Horaire des sessions d\'examens';
        $this->email['Template'] = 'Grade/Email/schedule.html.twig';
        $this->email['To']       = [new Address('ct@aikido.be', 'Secrétariat CT')];

        $this->send();

        return true;
    }

    /**
     * @return void
     * @throws TransportExceptionInterface
     */
    private function send(): void
    {
        $email = (new TemplatedEmail())
            ->from($this->email['From'])
            ->to(...$this->email['To'])
            ->cc(...$this->email['Cc'])
            ->bcc(...$this->email['Bcc'])
            ->replyTo($this->email['ReplyTo'])
            ->subject($this->email['Subject'])
            ->htmlTemplate($this->email['Template'])
            ->context($this->email['Context']);

        if (isset($this->email['Attach']))
        {
            foreach ($this->email['Attach'] as $file)
            {
                $email->attachFromPath($file);
            }
        }

        $email->getHeaders()->addTextHeader('X-Auto-Response-Suppress', 'OOF, DR, RN, NRN, AutoReply');

        $this->mailer->send($email);
    }
}
