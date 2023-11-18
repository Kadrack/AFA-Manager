<?php
// src/Repository/GradeSessionCandidateRepository.php
namespace App\Repository;

use App\Entity\Club;
use App\Entity\Grade;
use App\Entity\GradeSessionCandidate;
use App\Entity\Member;
use App\Entity\MemberLicence;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GradeSessionCandidate|null find($id, $lockMode = null, $lockVersion = null)
 * @method GradeSessionCandidate|null findOneBy(array $criteria, array $orderBy = null)
 * @method GradeSessionCandidate[]    findAll()
 * @method GradeSessionCandidate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GradeSessionCandidateRepository extends ServiceEntityRepository
{
    /**
     * GradeSessionRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GradeSessionCandidate::class);
    }

    /**
     * @param int $session
     * @return array|null
     */
    public function getSessionForms(int $session): ?array
    {
        $qb = $this->createQueryBuilder('s');

        return $qb->select('m.memberId AS Id', 'm.memberFirstname AS Firstname', 'm.memberName AS Name', 'm.memberPhoto AS Photo', 'm.memberBirthday AS Birthday', 'c.clubName AS Club', 'c.clubId AS ClubId', 'max(g.gradeRank) AS ActualGrade', 'max(g.gradeDate) AS ActualGradeDate', 'm.memberStartPractice AS StartPractice', 's.gradeSessionCandidateRank AS Grade', 's.gradeSessionCandidateJury AS Jury', 's.gradeSessionCandidatePosition AS Position')
            ->join(Member::class, 'm', 'WITH', $qb->expr()->eq('m.memberId', 's.gradeSessionCandidateMember'))
            ->join(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.memberId', 'l.memberLicenceMember'))
            ->join(Grade::class, 'g', 'WITH', $qb->expr()->eq('m.memberId', 'g.gradeMember'))
            ->join(Club::class, 'c', 'WITH', $qb->expr()->eq('l.memberLicenceClub', 'c.clubId'))
            ->where($qb->expr()->eq('s.gradeSessionCandidateExam', $session))
            ->andWhere($qb->expr()->eq('s.gradeSessionCandidateStatus', 1))
            ->andWhere($qb->expr()->isNull('s.gradeSessionCandidateResult'))
            ->groupBy('Id')
            ->orderBy('Grade', 'ASC')
            ->addOrderBy('Firstname', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }
}
