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

        return $qb->select('m.member_id AS Id', 'm.member_firstname AS Firstname', 'm.member_name AS Name', 'm.member_photo AS Photo', 'm.member_birthday AS Birthday', 'c.club_name AS Club', 'c.club_id AS ClubId', 'max(g.grade_rank) AS ActualGrade', 'max(g.grade_date) AS ActualGradeDate', 'm.member_start_practice AS StartPractice', 's.grade_session_candidate_rank AS Grade', 's.grade_session_candidate_jury AS Jury', 's.grade_session_candidate_position AS Position')
            ->join(Member::class, 'm', 'WITH', $qb->expr()->eq('m.member_id', 's.grade_session_candidate_member'))
            ->join(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.member_id', 'l.member_licence'))
            ->join(Grade::class, 'g', 'WITH', $qb->expr()->eq('m.member_id', 'g.grade_member'))
            ->join(Club::class, 'c', 'WITH', $qb->expr()->eq('l.member_licence_club', 'c.club_id'))
            ->where($qb->expr()->eq('s.grade_session_candidate_exam', $session))
            ->andWhere($qb->expr()->eq('s.grade_session_candidate_status', 1))
            ->andWhere($qb->expr()->isNull('s.grade_session_candidate_result'))
            ->groupBy('Id')
            ->orderBy('Grade', 'ASC')
            ->addOrderBy('Firstname', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }
}
