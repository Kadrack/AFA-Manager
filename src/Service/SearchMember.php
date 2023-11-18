<?php
// src/Service/SearchMember.php
namespace App\Service;

use App\Entity\Member;

use DateTime;

use Doctrine\Persistence\ManagerRegistry;

use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class Tools
 * @package App\Service
 */
class SearchMember
{
    private Access $access;

    private ManagerRegistry $doctrine;

    /**
     * Access constructor.
     */
    public function __construct(Access $access, ManagerRegistry $doctrine)
    {
        $this->access = $access;

        $this->doctrine = $doctrine;
    }

    /**
     * @param string $search
     * @param int|null $examType
     * @return array|null
     */
    public function getResults(string $search, ?int $examType = null): ?array
    {
        $results = $this->doctrine->getRepository(Member::class)->getSearchMembers($search);

        $session = new Session();

        $club = null;

        if ($session->has('Club'))
        {
            $club = $session->get('Club');
        }

        $searchResults = array();

        foreach ($results as $result)
        {
            if ($examType === 1)
            {
                if (($result->getMemberLastGrade()?->getGradeRank() < 6) || ($result->getMemberLastGrade()?->getGradeRank() > 12))
                {
                    continue;
                }
            }
            elseif ($examType === 2)
            {
                if ($result->getMemberLastGrade()?->getGradeRank() < 6)
                {
                    continue;
                }
            }

            if ($this->access->check('Search-FullAccess'))
            {
                $searchResults[] = $this->fillArray($result);

                continue;
            }
            elseif ($result->getMemberOutdate())
            {
                continue;
            }

            if (isset($club) && ($club->getClubId() === $result->getMemberActualClub()?->getClubId()))
            {
                if ($this->access->isManager($club))
                {
                    $searchResults[] = $this->fillArray($result);

                    continue;
                }

                if ($this->access->isDojoCho($club))
                {
                    $searchResults[] = $this->fillArray($result);

                    continue;
                }

                if ($this->access->isTeacherChilds($club) && ($result->getMemberSubscriptionList() > 1))
                {
                    $searchResults[] = $this->fillArray($result);

                    continue;
                }

                if ($this->access->isTeacherAdults($club) && (($result->getMemberSubscriptionList() == 1) || ($result->getMemberSubscriptionList() == 3)))
                {
                    $searchResults[] = $this->fillArray($result);

                    continue;
                }
            }

            if (($this->access->check('Search-ActualMember')))
            {
                $searchResults[] = $this->fillArray($result);
            }
            elseif (($this->access->check('Search-Adult')) && ($result->getMemberBirthday() < new DateTime('-12 year today')))
            {
                $searchResults[] = $this->fillArray($result);
            }
            elseif (($this->access->check('Search-Candidate')) && ($result->getMemberLastGrade()->getGradeRank() >= 6))
            {
                $searchResults[] = $this->fillArray($result);
            }
            elseif (($this->access->check('Search-Child')) && ($result->getMemberBirthday() > new DateTime('-17 year today')))
            {
                $searchResults[] = $this->fillArray($result);
            }
            elseif (($this->access->check('Search-Kyu')) && ($result->getMemberLastGrade()->getGradeRank() < 6))
            {
                $searchResults[] = $this->fillArray($result);
            }
            elseif (($this->access->check('Search-Yudansha')) && ($result->getMemberLastGrade()->getGradeRank() > 6))
            {
                $searchResults[] = $this->fillArray($result);
            }
        }

        return $searchResults;
    }

    private function fillArray(Member $result): array
    {
        $data['FirstName'] = $result->getMemberFirstname();
        $data['Id']        = $result->getMemberId();
        $data['Name']      = $result->getMemberName();
        $data['Photo']     = $result->getMemberPhoto();

        if (is_null($result->getMemberLastLicence()?->getMemberLicenceDeadline()))
        {
            $data['Deadline'] = 'Inconnue';
        }
        else
        {
            $data['Deadline']  = $result->getMemberLastLicence()->getMemberLicenceDeadline();
        }

        if (is_null($result->getMemberActualClub()))
        {
            $data['Club']   = '';
            $data['ClubId'] = '- Inconnu';
        }
        else
        {
            $data['Club']   = $result->getMemberActualClub()->getClubName();
            $data['ClubId'] = $result->getMemberActualClub()->getClubId();
        }

        return $data;
    }
}
