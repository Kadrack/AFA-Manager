<?php
// src/Service/Access.php
namespace App\Service;

use App\Entity\Club;
use App\Entity\Cluster;
use App\Entity\ClusterMember;

use Doctrine\Persistence\ManagerRegistry;

use Symfony\Bundle\SecurityBundle\Security;

use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class Tools
 *
 * @package App\Service
 */
class Access
{
    /**
     * @var array
     */
    private array $access = array();

    /**
     * @var array|null
     */
    private ?array $clusters = array();

    /**
     * @var array|null
     */
    private ?array $managers = array();

    /**
     * @var array|null
     */
    private ?array $teachers = array();

    /**
     * Access constructor.
     */
    public function __construct(private readonly ManagerRegistry $doctrine, private readonly Security $security)
    {
        $session = new Session();

        if (!is_null($security->getUser()))
        {
            foreach ($security->getUser()->getManagers() AS $manager)
            {
                $this->managers[] = $manager;

                if (!$session->has('Id') && !$session->has('Cluster'))
                {
                    $this->setListAccess($manager->getClubManagerClub()->getClubId());
                }
            }

            if (!is_null($security->getUser()->getMember()?->getMemberClubManagers()))
            {
                foreach ($security->getUser()->getMember()->getMemberClubManagers() AS $manager)
                {
                    $this->managers[] = $manager;

                    if (!$session->has('Id') && !$session->has('Cluster'))
                    {
                        $this->setListAccess($manager->getClubManagerClub()->getClubId());
                    }
                }
            }

            if (!is_null($security->getUser()->getMember()?->getMemberClubTeachers()))
            {
                foreach ($security->getUser()->getMember()->getMemberClubTeachers() AS $teacher)
                {
                    $this->teachers[] = $teacher;

                    if (!$session->has('Id') && !$session->has('Cluster'))
                    {
                        $this->setListAccess($teacher->getClubTeacherClub()->getClubId());
                    }
                }
            }

            foreach ($security->getUser()->getClusters() AS $cluster)
            {
                if ($cluster->getClusterMemberActive())
                {
                    $this->clusters[] = $cluster;

                    if (!$session->has('Id') && !$session->has('Club'))
                    {
                        $this->setListAccess($cluster->getClusterMemberCluster()->getClusterId());
                    }
                }
            }

            if (!is_null($security->getUser()->getMember()?->getMemberClusterMembers()))
            {
                foreach ($security->getUser()->getMember()->getMemberClusterMembers() AS $cluster)
                {
                    if ($cluster->getClusterMemberActive())
                    {
                        $this->clusters[] = $cluster;

                        if (!$session->has('Id') && !$session->has('Club'))
                        {
                            $this->setListAccess($cluster->getClusterMemberCluster()->getClusterId());
                        }
                    }
                }
            }

            if ($session->has('Id') && $session->has('Club'))
            {
                $this->setListAccess($session->get('Club')->getClubId());
            }
            elseif ($session->has('Id') && $session->has('Cluster'))
            {
                $this->setListAccess($session->get('Id'));
            }

            if (!$session->has('Id') && !is_null($security->getUser()->getMember()))
            {
                $this->setMemberAccess();
            }
        }
    }

    /**
     * @return bool
     */
    public function isWebmaster(): bool
    {
        foreach ($this->clusters as $cluster)
        {
            if ($cluster->getClusterMemberCluster()->getClusterId() == 12)
            {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isCAPresident(): bool
    {
        foreach ($this->clusters as $cluster)
        {
            if (($cluster->getClusterMemberCluster()->getClusterId() == 3) && ($cluster->getClusterMemberTitle() == 1))
            {
                return true;
            }
        }

        return $this->isWebmaster();
    }

    /**
     * @return bool
     */
    public function isCASecretary(): bool
    {
        foreach ($this->clusters as $cluster)
        {
            if (($cluster->getClusterMemberCluster()->getClusterId() == 3) && ($cluster->getClusterMemberTitle() == 3))
            {
                return true;
            }
        }

        return $this->isWebmaster();
    }

    /**
     * @return bool
     */
    public function isCATreasurer(): bool
    {
        foreach ($this->clusters as $cluster)
        {
            if (($cluster->getClusterMemberCluster()->getClusterId() == 3) && ($cluster->getClusterMemberTitle() == 4))
            {
                return true;
            }
        }

        return $this->isWebmaster();
    }

    /**
     * @return bool
     */
    public function isCTPresident(): bool
    {
        foreach ($this->clusters as $cluster)
        {
            if (($cluster->getClusterMembercluster()->getClusterId() == 1) && ($cluster->getClusterMemberTitle() == 1))
            {
                return true;
            }
        }

        return $this->isWebmaster();
    }

    /**
     * @return bool
     */
    public function isCTSecretary(): bool
    {
        foreach ($this->clusters as $cluster)
        {
            if (($cluster->getClusterMemberCluster()->getClusterId() == 1) && ($cluster->getClusterMemberTitle() == 10))
            {
                return true;
            }
        }

        return $this->isWebmaster();
    }

    /**
     * @return bool
     */
    public function isCPSecretary(): bool
    {
        foreach ($this->clusters as $cluster)
        {
            if (($cluster->getClusterMemberCluster()->getClusterId() == 4) && ($cluster->getClusterMemberTitle() == 10))
            {
                return true;
            }
        }

        return $this->isWebmaster();
    }

    /**
     * @param Club|null $club
     * @return bool
     */
    public function isManager(?Club $club = null): bool
    {
        if (is_null($club) && sizeof($this->managers) > 0)
        {
            return true;
        }

        foreach ($this->managers as $manager)
        {
            if ($manager->getClubManagerClub()->getClubId() === $club->getClubId())
            {
                return true;
            }
        }

        return $this->isWebmaster();
    }

    /**
     * @param Club|null $club
     * @return bool
     */
    public function isDojoCho(?Club $club = null): bool
    {
        foreach ($this->teachers as $teacher)
        {
            if ($teacher->getClubTeacherTitle() == 1)
            {
                if (is_null($club))
                {
                    return true;
                }

                if ($teacher->getClubTeacherClub()->getClubId() === $club->getClubId())
                {
                    return true;
                }
            }
        }

        return $this->isWebmaster();
    }

    /**
     * @param Club|null $club
     * @return bool
     */
    public function isTeacher(?Club $club = null): bool
    {
        if (is_null($club) && sizeof($this->teachers) > 0)
        {
            return true;
        }

        foreach ($this->teachers as $teacher)
        {
            if ($teacher->getClubTeacherClub()->getClubId() === $club->getClubId())
            {
                return true;
            }
        }

        return $this->isWebmaster();
    }

    /**
     * @param Club $club
     * @return bool
     */
    public function isTeacherAdults(Club $club): bool
    {
        foreach ($this->teachers as $teacher)
        {
            if (($teacher->getClubTeacherClub()->getClubId() === $club->getClubId()) && ($teacher->getClubTeacherType() != 2))
            {
                return true;
            }
        }

        return $this->isWebmaster();
    }

    /**
     * @param Club $club
     * @return bool
     */
    public function isTeacherChilds(Club $club): bool
    {
        foreach ($this->teachers as $teacher)
        {
            if (($teacher->getClubTeacherClub()->getClubId() === $club->getClubId()) && ($teacher->getClubTeacherType() != 1))
            {
                return true;
            }
        }

        return $this->isWebmaster();
    }

    /**
     * @return array
     */
    public function getAccessList(): array
    {
        $list = array();

        foreach ($this->managers as $manager)
        {
            $list[$manager->getClubManagerClub()->getClubId()] = ucwords($manager->getClubManagerClub()->getClubName());
        }

        foreach ($this->teachers as $teacher)
        {
            $list[$teacher->getClubTeacherClub()->getClubId()] = ucwords($teacher->getClubTeacherClub()->getClubName());
        }

        if ($this->isWebmaster())
        {
            foreach ($this->doctrine->getRepository(Cluster::class)->findAll() as $cluster)
            {
                if (!$cluster->getClusterGiveAccess())
                {
                    continue;
                }

                $list[$cluster->getClusterId()] = $cluster->getClusterName();
            }
        }
        else
        {
            foreach ($this->clusters as $cluster)
            {
                if (!$cluster->getClusterMembercluster()->getClusterGiveAccess())
                {
                    continue;
                }

                $list[$cluster->getClusterMemberCluster()->getClusterId()] = $cluster->getClusterMemberCLuster()->getClusterName();
            }
        }

        asort($list);

        return $list;
    }

    /**
     * @param int $id
     *
     * @return bool
     */
    public function setListAccess(int $id): bool
    {
        $club    = null;
        $cluster = null;

        if ($id < 1000)
        {
            if ($this->isWebmaster())
            {
                $cluster = $this->doctrine->getRepository(ClusterMember::class)->findOneBy(array('clusterMemberCluster' => 12, 'clusterMember' => $this->security->getUser()?->getMember()->getMemberId()));

                !is_null($cluster) ?: $cluster = $this->doctrine->getRepository(ClusterMember::class)->findOneBy(array('cluster' => 12, 'clusterMemberUser' => $this->security->getUser()->getId()));
            }
            else
            {
                $clusters = $this->doctrine->getRepository(ClusterMember::class)->findBy(array('clusterMemberCluster' => $id, 'clusterMemberUser' => $this->security->getUser()->getId()));
                $clusters = array_merge($clusters, $this->doctrine->getRepository(ClusterMember::class)->findBy(array('clusterMemberCluster' => $id, 'clusterMember' => $this->security->getUser()->getMember()?->getMemberId())));

                foreach ($clusters as $clusterMember)
                {
                    if ($clusterMember->getClusterMemberActive())
                    {
                        $cluster = $clusterMember;
                    }
                }
            }
        }
        else
        {
            $club = $this->doctrine->getRepository(Club::class)->findOneBy(array('clubId' => $id));
        }

        $session = new Session();

        $list = $this->getAccessList();

        if (!is_null($club) && isset($list[$club->getClubId()]))
        {
            $session->set('Id', $club->getClubId());
            $session->set('Club', $club);

            $session->remove('Cluster');

            $this->setClubAccess($club);

            return true;
        }
        elseif (!is_null($cluster) && isset($list[$id]))
        {
            $session->set('Id', $id);
            $session->set('Cluster', $cluster);

            $session->remove('Club');

            $this->setClusterAccess($id);

            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function setMemberAccess(): bool
    {
        $dojo     = array('Club-DojoTab', 'Club-EmailTab', 'Club-Index', 'Club-Menu');
        $grade    = array('Grade-List', 'Grade-Menu');
        $mailing  = array('Mailing-ClubManager', 'Mailing-ClubTeacher');
        $member   = array('Member-GradeTab', 'Member-Index', 'Member-LicenceFormPrint', 'Member-LicenceStampPrint', 'Member-LicenceTab', 'Member-Menu', 'Member-PersonalEdit', 'Member-PersonalTab', 'Member-TitleTab', 'Member-TrainingTab');
        $training = array('Training-Menu');

        $this->access = array_merge($dojo, $grade, $mailing, $member, $training);

        return true;
    }

    /**
     * @param Club $club
     * @return bool
     */
    public function setClubAccess(Club $club): bool
    {
        $dojo     = array('Club-DojoTab', 'Club-EmailTab', 'Club-Index', 'Club-LessonAdd', 'Club-LessonEdit', 'Club-LessonIndex', 'Club-MemberDetail', 'Club-Menu', 'Club-SubscriptionEdit', 'Club-SubscriptionList', 'Club-TrainingAdd', 'Club-TrainingOld');
        $grade    = array('Grade-CandidatesAwaiting', 'Grade-CandidatesRejected', 'Grade-Criteria', 'Grade-Index', 'Grade-List', 'Grade-Menu', 'Grade-Search', 'Grade-ValidatedAwaiting', 'Grade-ValidatedFail', 'Grade-ValidatedNoShow', 'Grade-ValidatedSuccess');
        $mailing  = array('Mailing-ClubManager', 'Mailing-ClubTeacher');
        $member   = array('Member-EmailTab', 'Member-GradeAdd', 'Member-GradeKyuEdit', 'Member-GradeTab', 'Member-Index', 'Member-LicenceTab', 'Member-Menu', 'Member-TitleTab', 'Member-TrainingTab');
        $search   = array('Search-Member');
        $training = array('Training-AttendancesDetails', 'Training-AttendancesDetailsRestricted', 'Training-Index', 'Training-Menu');

        if ($this->isManager($club))
        {
            $dojo     = array_merge($dojo, array('Club-AdultTab', 'Club-AttendanceAdd', 'Club-AttendanceDelete', 'Club-AttendanceTab', 'Club-AssociationEdit', 'Club-ChildTab', 'Club-ClassAdd', 'Club-ClassEdit', 'Club-CommiteeEdit', 'Club-DojoAdd', 'Club-DojoEdit', 'Club-FormDownload', 'Club-FormRenew', 'Club-ManagementTab', 'Club-ManagerEdit', 'Club-PhotoEdit', 'Club-SecretariatTab', 'Club-SocialEdit', 'Club-TeacherAdd', 'Club-TeacherEdit', 'Club-WebsiteEdit'));
            $grade    = array_merge($grade, array('Grade-PaymentView'));
            $mailing  = array_merge($mailing, array('Mailing-ClubAdult', 'Mailing-ClubChild', 'Mailing-OtherClubs'));
            $member   = array_merge($member, array('Member-LicenceFormPrint', 'Member-LicenceStampPrint', 'Member-PersonalEdit', 'Member-PersonalTab'));
            $search   = array_merge($search, array());
            $training = array_merge($training, array());
        }

        if ($this->isTeacher($club))
        {
            $dojo     = array_merge($dojo, array('Club-AttendanceAdd', 'Club-AttendanceDelete', 'Club-AttendanceTab'));
            $grade    = array_merge($grade, array());
            $mailing  = array_merge($mailing, array());
            $member   = array_merge($member, array());
            $search   = array_merge($search, array());
            $training = array_merge($training, array());

            if ($this->isDojoCho($club))
            {
                $dojo    = array_merge($dojo, array('Club-AdultTab', 'Club-ChildTab'));
                $mailing = array_merge($mailing, array('Mailing-ClubAdult', 'Mailing-ClubChild', 'Mailing-OtherClub'));
            }
            else
            {
                if ($this->isTeacherAdults($club))
                {
                    $dojo    = array_merge($dojo, array('Club-AdultTab'));
                    $mailing = array_merge($mailing, array('Mailing-ClubAdult'));
                }

                if ($this->isTeacherChilds($club))
                {
                    $dojo    = array_merge($dojo, array('Club-ChildTab'));
                    $mailing = array_merge($mailing, array('Mailing-ClubChild'));
                }
            }
        }

        $this->access = array_merge($dojo, $grade, $mailing, $member, $search, $training);

        return true;
    }

    /**
     * @param int $cluster
     * @return bool
     */
    public function setClusterAccess(int $cluster): bool
    {
        if ($cluster == 1)
        {
            $admin    = array('Admin-List', 'Admin-Menu');
            $dojo     = array('Club-DojoTab', 'Club-EmailTab', 'Club-Index', 'Club-MemberDetail', 'Club-ListOpen', 'Club-Menu');
            $grade    = array('Grade-CandidatesAwaiting', 'Grade-CandidatesAwaitingAction', 'Grade-CandidatesRejected', 'Grade-Index', 'Grade-List', 'Grade-Kagami', 'Grade-Menu', 'Grade-ValidatedAwaiting', 'Grade-ValidatedFail', 'Grade-ValidatedNoShow', 'Grade-ValidatedSuccess');
            $list     = array('List-Various');
            $mailing  = array('Mailing-ClubManager', 'Mailing-ClubTeacher');
            $member   = array('Member-EmailTab', 'Member-GradeTab', 'Member-Index', 'Member-ListGradeDan', 'Member-Menu', 'Member-TitleTab', 'Member-TrainingTab');
            $search   = array('Search-Candidate', 'Search-Member');
            $training = array('Training-AttendancesDetails', 'Training-Index', 'Training-Menu');

            if ($this->isCTPresident())
            {
                $admin   = array_merge($admin, array('Admin-Mailing'));
                $mailing = array_merge($mailing, array('Mailing-DojoCho', 'Mailing-Manager', 'Mailing-Teacher'));
                $search  = array_merge($search, array('Search-ActualMember'));
            }

            if ($this->isCTSecretary())
            {
                $admin   = array_merge($admin, array('Admin-Mailing'));
                $grade   = array_merge($grade, array('Grade-AikikaiList', 'Grade-Assignment', 'Grade-CandidateEmail', 'Grade-CandidateForms', 'Grade-CandidateList', 'Grade-CandidateValidate', 'Grade-CandidatesRejectedAction', 'Grade-GradeValidate', 'Grade-PaymentView', 'Grade-Publication', 'Grade-Search', 'Grade-SessionAdd', 'Grade-SessionEdit', 'Grade-ValidatedAction'));
                $mailing = array_merge($mailing, array('Mailing-DojoCho', 'Mailing-Manager', 'Mailing-Teacher'));
                $member  = array_merge($member, array('Member-AikikaiIdEdit', 'Member-GradeAdd', 'Member-GradeKyuEdit', 'Member-GradeDanEdit', 'Member-ListGradeDan', 'Member-ListGradeKyu', 'Member-TitleAdd', 'Member-TitleEdit'));
                $search  = array_merge($search, array('Search-ActualMember'));
            }

            $this->access = array_merge($admin, $dojo, $grade, $list, $mailing, $member, $search, $training);
        }
        elseif ($cluster == 2)
        {
            $admin    = array('Admin-List', 'Admin-Mailing', 'Admin-Menu');
            $dojo     = array('Club-DojoTab', 'Club-EmailTab', 'Club-Index', 'Club-ListOpen', 'Club-MemberDetail', 'Club-Menu');
            $list     = array('List-Various');
            $mailing  = array('Mailing-ClubManager', 'Mailing-ClubTeacher', 'Mailing-DojoCho', 'Mailing-Manager', 'Mailing-Teacher');
            $member   = array('Member-GradeTab', 'Member-Index', 'Member-Menu', 'Member-EmailTab', 'Member-TitleTab', 'Member-TrainingTab');
            $training = array('Training-Add', 'Training-AttendanceAdd', 'Training-AttendancesDetails', 'Training-AttendanceDetailsRestricted', 'Training-Edit', 'Training-Index', 'Training-Menu', 'Training-SessionAdd', 'Training-SessionEdit');
            $search   = array('Search-Child', 'Search-Member');

            $this->access = array_merge($admin, $dojo, $list, $mailing, $member, $search, $training);
        }
        elseif ($cluster == 3)
        {
            $admin    = array('Admin-List', 'Admin-Menu');
            $dojo     = array('Club-DojoTab', 'Club-EmailTab', 'Club-Index', 'Club-ListOpen', 'Club-MemberDetail', 'Club-Menu');
            $grade    = array();
            $list     = array('List-Various');
            $mailing  = array('Mailing-ClubManager', 'Mailing-ClubTeacher');
            $member   = array('Member-EmailTab', 'Member-Index', 'Member-LicenceTab', 'Member-Menu');
            $news     = array('News-Create', 'News-Delete', 'News-Edit', 'News-FullList', 'News-Send');
            $search   = array('Search-ActualMember', 'Search-Member');
            $training = array();

            if ($this->isCAPresident())
            {
                $admin    = array_merge($admin, array());
                $list     = array_merge($list, array('List-Licence'));
                $mailing  = array_merge($mailing, array('Mailing-CA', 'Mailing-CT', 'Mailing-CJ', 'Mailing-CP', 'Mailing-DojoCho', 'Mailing-Manager', 'Mailing-Menu', 'Mailing-Preview', 'Mailing-Teacher'));
                $training = array_merge($training, array('Training-Add', 'Training-AttendanceAdd', 'Training-AttendancesDetails', 'Training-DiscountAdd', 'Training-Edit', 'Training-FullAccess', 'Training-Index', 'Training-MemberPayment', 'Training-Menu', 'Training-SessionAdd', 'Training-SessionEdit', 'Training-TotalPayment'));
            }

            if ($this->isCASecretary())
            {
                $admin    = array_merge($admin, array('Admin-Cluster', 'Admin-Login', 'Admin-Mailing', 'Admin-PaymentAdd'));
                $dojo     = array_merge($dojo, array('Club-AdultTab', 'Club-AssociationEdit', 'Club-ChildTab', 'Club-ClubAdd', 'Club-CommiteeEdit', 'Club-DojoAdd', 'Club-DojoEdit', 'Club-FormDownload', 'Club-FormRenew', 'Club-LessonAdd', 'Club-LessonEdit', 'Club-ListClose', 'Club-ManagementTab', 'Club-ManagerAdd', 'Club-ManagerEdit', 'Club-MemberAdd', 'Club-PhotoEdit', 'Club-PrintStamp', 'Club-SocialEdit', 'Club-TeacherAdd', 'Club-TeacherEdit', 'Club-SecretariatTab', 'Club-WebsiteEdit'));
                $grade    = array_merge($grade, array('Grade-AikikaiList', 'Grade-Assignment', 'Grade-CandidateEmail', 'Grade-CandidateForms', 'Grade-CandidateList', 'Grade-CandidatesAwaiting', 'Grade-CandidatesRejected', 'Grade-List', 'Grade-Menu', 'Grade-PaymentEdit', 'Grade-PaymentView', 'Grade-Search', 'Grade-SessionAdd', 'Grade-SessionEdit', 'Grade-ValidatedAwaiting', 'Grade-ValidatedFail', 'Grade-ValidatedNoShow', 'Grade-ValidatedSuccess'));
                $list     = array_merge($list, array('List-Licence'));
                $mailing  = array_merge($mailing, array('Mailing-CA', 'Mailing-CJ', 'Mailing-CP', 'Mailing-CT', 'Mailing-DojoCho', 'Mailing-Manager', 'Mailing-Menu', 'Mailing-Preview', 'Mailing-Teacher'));
                $member   = array_merge($member, array('Member-AikikaiIdEdit', 'Member-FormationAdd', 'Member-FormationEdit', 'Member-GradeAdd', 'Member-GradeKyuEdit', 'Member-GradeDanEdit', 'Member-GradeStartEdit', 'Member-GradeTab', 'Member-LicenceAdd', 'Member-LicenceCardPrint', 'Member-LicenceEdit', 'Member-LicenceFormPrint', 'Member-LicenceStampPrint', 'Member-LicenceTab', 'Member-ListGradeDan', 'Member-ListGradeKyu', 'Member-Menu', 'Member-PersonalEdit', 'Member-PersonalTab', 'Member-TitleAdd', 'Member-TitleEdit', 'Member-TitleTab', 'Member-TrainingTab'));
                $search   = array_merge($search, array('Search-FullAccess'));
                $training = array_merge($training, array('Training-Add', 'Training-AttendanceAdd', 'Training-AttendancesDetails', 'Training-Edit', 'Training-FullAccess', 'Training-Index', 'Training-MemberPayment', 'Training-Menu', 'Training-SessionAdd', 'Training-SessionEdit'));
            }

            if ($this->isCATreasurer())
            {
                $admin    = array_merge($admin, array('Admin-PaymentAdd'));
                $list     = array_merge($list, array('List-Licence'));
                $training = array_merge($training, array('Training-Add', 'Training-AttendanceAdd', 'Training-AttendancesDetails', 'Training-DiscountAdd', 'Training-Edit', 'Training-FullAccess', 'Training-Index', 'Training-MemberPayment', 'Training-Menu', 'Training-SessionAdd', 'Training-SessionEdit', 'Training-TotalPayment'));
            }

            $this->access = array_merge($admin, $dojo, $grade, $list, $mailing, $member, $news, $search, $training);
        }
        elseif ($cluster == 4)
        {
            $admin     = array('Admin-List', 'Admin-Menu');
            $dojo      = array('Club-DojoTab', 'Club-EmailTab', 'Club-Index', 'Club-ListOpen', 'Club-MemberDetail', 'Club-Menu');
            $formation = array('Formation-Menu', 'Formation-SessionAdd', 'Formation-SessionEdit', 'Formation-SessionList', 'Formation-SessionManagement');
            $list      = array('List-Various');
            $mailing   = array('Mailing-ClubManager', 'Mailing-ClubTeacher');

            if ($this->isCPSecretary())
            {
                $admin    = array_merge($admin, array('Admin-Mailing'));
                $mailing  = array_merge($mailing, array('Mailing-CPAnimateur', 'Mailing-DojoCho', 'Mailing-Manager', 'Mailing-Teacher'));
            }

            $this->access = array_merge($admin, $dojo, $formation, $list, $mailing);
        }
        elseif ($cluster == 8)
        {
            $admin     = array('Admin-Cluster', 'Admin-List', 'Admin-Login', 'Admin-Mailing', 'Admin-Menu', 'Admin-PaymentAdd');
            $dojo      = array('Club-AdultTab', 'Club-AssociationEdit', 'Club-ChildTab', 'Club-ClassAdd', 'Club-ClassEdit', 'Club-ClubAdd', 'Club-CommiteeEdit', 'Club-DojoAdd', 'Club-DojoEdit', 'Club-DojoTab', 'Club-EmailTab', 'Club-FormDownload', 'Club-FormRenew', 'Club-HistoryEdit', 'Club-Index', 'Club-LessonAdd', 'Club-LessonEdit', 'Club-ListClose', 'Club-ListOpen', 'Club-ManagementTab', 'Club-ManagerAdd', 'Club-ManagerEdit', 'Club-MemberAdd', 'Club-MemberDetail', 'Club-Menu', 'Club-PhotoEdit', 'Club-PrintStamp', 'Club-SocialEdit', 'Club-TeacherAdd', 'Club-TeacherEdit', 'Club-SecretariatTab', 'Club-WebsiteEdit');
            $formation = array('Formation-Menu', 'Formation-SessionAdd', 'Formation-SessionList', 'Formation-SessionManagement');
            $grade     = array('Grade-AikikaiList', 'Grade-Assignment', 'Grade-CandidateEmail', 'Grade-CandidateForms', 'Grade-CandidateList', 'Grade-CandidatesAwaiting', 'Grade-CandidatesRejected', 'Grade-Index', 'Grade-List', 'Grade-Menu', 'Grade-PaymentEdit', 'Grade-PaymentView', 'Grade-Search', 'Grade-SessionAdd', 'Grade-SessionEdit', 'Grade-ValidatedAwaiting', 'Grade-ValidatedFail', 'Grade-ValidatedNoShow', 'Grade-ValidatedSuccess');
            $list      = array('List-Licence', 'List-Various');
            $mailing   = array('Mailing-CA', 'Mailing-CJ', 'Mailing-CP', 'Mailing-CT', 'Mailing-ClubManager', 'Mailing-ClubTeacher', 'Mailing-DojoCho', 'Mailing-Manager', 'Mailing-Menu', 'Mailing-Preview', 'Mailing-Teacher');
            $member    = array('Member-AikikaiIdEdit', 'Member-EmailTab', 'Member-FormationAdd', 'Member-FormationEdit', 'Member-GradeAdd', 'Member-GradeKyuEdit', 'Member-GradeDanEdit', 'Member-GradeStartEdit', 'Member-GradeTab', 'Member-Index', 'Member-LicenceAdd', 'Member-LicenceCardPrint', 'Member-LicenceEdit', 'Member-LicenceFormPrint', 'Member-LicenceStampPrint', 'Member-LicenceTab', 'Member-ListGradeDan', 'Member-ListGradeKyu', 'Member-Menu', 'Member-PersonalEdit', 'Member-PersonalTab', 'Member-TitleAdd', 'Member-TitleEdit', 'Member-TitleTab', 'Member-TrainingTab');
            $news      = array('News-Create', 'News-Delete', 'News-Edit', 'News-FullList', 'News-Send');
            $search    = array('Search-FullAccess', 'Search-Member');
            $training  = array('Training-Add', 'Training-AttendanceAdd', 'Training-AttendancesDetails', 'Training-Edit', 'Training-FullAccess', 'Training-Index', 'Training-MemberPayment', 'Training-Menu', 'Training-SessionAdd', 'Training-SessionEdit');

            $this->access = array_merge($admin, $dojo, $formation, $grade, $list, $mailing, $member, $news, $search, $training);
        }
        elseif ($cluster == 10)
        {
            $admin    = array('Admin-List', 'Admin-Menu');
            $dojo     = array('Club-DojoTab', 'Club-EmailTab', 'Club-Index', 'Club-MemberDetail', 'Club-Menu');
            $grade    = array('Grade-CandidatesAwaiting', 'Grade-CandidatesAwaitingAction', 'Grade-CandidatesRejected', 'Grade-Index', 'Grade-Kagami', 'Grade-List', 'Grade-Menu', 'Grade-ValidatedAwaiting', 'Grade-ValidatedFail', 'Grade-ValidatedNoShow', 'Grade-ValidatedSuccess');
            $list     = array('List-Various');
            $mailing  = array('Mailing-ClubManager', 'Mailing-ClubTeacher');
            $member   = array('Member-EmailTab', 'Member-GradeTab', 'Member-Index', 'Member-ListGradeDan', 'Member-Menu', 'Member-TitleTab', 'Member-TrainingTab');
            $search   = array('Search-Member', 'Search-Yudansha');
            $training = array('Training-AttendancesDetails', 'Training-Index', 'Training-Menu');

            $this->access = array_merge($admin, $dojo, $grade, $list, $mailing, $member, $search, $training);
        }
        elseif ($cluster == 11)
        {
            $search   = array('Search-ActualMember');
            $training = array('Training-Add', 'Training-AttendanceAdd', 'Training-AttendancesDetails', 'Training-Edit', 'Training-Index', 'Training-MemberPayment', 'Training-Menu', 'Training-SessionAdd', 'Training-SessionEdit');

            $this->access = array_merge($search, $training);
        }

        return true;
    }

    /**
     * @param string $access
     * @return bool
     */
    public function check(string $access): bool
    {
        if (is_int(array_search($access, $this->access, true)))
        {
            return true;
        }

        return false;
    }
}
