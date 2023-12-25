<?php
// src/Controller/StatisticsController.php
namespace App\Controller;

use App\Entity\Club;

use App\Service\ListData;

use DateTime;

use Doctrine\Persistence\ManagerRegistry;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\ExpressionLanguage\Expression;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Attribute\Route;

use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Class AdministrationController
 * @package App\Controller
 *
 * @IsGranted("ROLE_USER")
 */
#[Route('/statistique', name:'statistics-')]
#[IsGranted(new Expression('is_granted("ROLE_USER")'))]
class StatisticsController extends AbstractController
{
    /**
     * @param ManagerRegistry $doctrine
     * @param string|null     $date
     *
     * @return Response
     */
    #[Route('/index/{date}', name:'index')]
    public function index(ManagerRegistry $doctrine, ?string $date = null): Response
    {
        $data['Statistics'] = array();

        $provinces = new listData();

        foreach ($provinces->getProvince(0) as $province)
        {
            $data['Statistics'][$province]['Id'] = $province;

            for ($i = 1; $i <= 12; $i++)
            {
                $data['Statistics'][$province]['Limits'][$i] = 0;
            }

            for ($i = 1; $i <= 12; $i++)
            {
                $data['Statistics'][$province]['ADEPS'][$i] = 0;
            }

            for ($i = 1; $i <= 20; $i++)
            {
                $data['Statistics'][$province]['Grade'][$i] = 0;
            }
        }

        $data['Date'] = DateTime::createFromFormat("Y-m-d", $date);

        if ((!$data['Date']) || ($data['Date'] < new DateTime('2020-10-01')))
        {
            $data['Date'] = new DateTime();
        }

        $query = $doctrine->getRepository(Club::class)->getMembers($data['Date']);

        $limit = $this->getLimit($data['Date']);

        foreach ($query as $member)
        {
            if ($member['Birthday']->format('Ymd') > $limit[0])
            {
                $member['Sex'] == 1 ? $data['Statistics'][$member['Province']]['Limits'][1]++ : $data['Statistics'][$member['Province']]['Limits'][2]++;
            }
            elseif ($member['Birthday']->format('Ymd') > $limit[1])
            {
                $member['Sex'] == 1 ? $data['Statistics'][$member['Province']]['Limits'][3]++ : $data['Statistics'][$member['Province']]['Limits'][4]++;
            }
            elseif ($member['Birthday']->format('Ymd') > $limit[2])
            {
                $member['Sex'] == 1 ? $data['Statistics'][$member['Province']]['Limits'][5]++ : $data['Statistics'][$member['Province']]['Limits'][6]++;
            }
            elseif ($member['Birthday']->format('Ymd') > $limit[3])
            {
                $member['Sex'] == 1 ? $data['Statistics'][$member['Province']]['Limits'][7]++ : $data['Statistics'][$member['Province']]['Limits'][8]++;
            }
            elseif ($member['Birthday']->format('Ymd') > $limit[4])
            {
                $member['Sex'] == 1 ? $data['Statistics'][$member['Province']]['Limits'][9]++ : $data['Statistics'][$member['Province']]['Limits'][10]++;
            }
            else
            {
                $member['Sex'] == 1 ? $data['Statistics'][$member['Province']]['Limits'][11]++ : $data['Statistics'][$member['Province']]['Limits'][12]++;
            }

            if (!is_null($member['Level']))
            {
                $member['Sex'] == 1 ? $member['Level'] = $member['Level'] * 2 - 1 : $member['Level'] = $member['Level'] * 2;

                $data['Statistics'][$member['Province']]['ADEPS'][$member['Level']]++;
            }

            $data['Statistics'][$member['Province']]['Grade'][$member['Grade']]++;
        }

        return $this->render('Statistics/index.html.twig', array('data' => $data));
    }

    /**
     * @param ManagerRegistry $doctrine
     * @param int             $province
     * @param string|null     $date
     *
     * @return Response
     */
    #[Route('/province/{province<\d+>}/{date}', name:'province')]
    public function province(ManagerRegistry $doctrine, int $province, ?string $date = null): Response
    {
        $data['Date'] = DateTime::createFromFormat("Y-m-d", $date);

        if ((!$data['Date']) || ($data['Date'] < new DateTime('2020-10-01')))
        {
            $data['Date'] = new DateTime();
        }

        $query = $doctrine->getRepository(Club::class)->getMembers($data['Date'], $province);

        $limit = $this->getLimit($data['Date']);

        $data['Statistics'] = array();

        foreach ($query as $member)
        {
            if (!isset($data['Statistics'][$member['ClubId']]))
            {
                $data['Statistics'][$member['ClubId']]['ClubId']   = $member['ClubId'];
                $data['Statistics'][$member['ClubId']]['ClubName'] = $member['ClubName'];

                for ($i = 1; $i <= 12; $i++)
                {
                    $data['Statistics'][$member['ClubId']]['Limits'][$i] = 0;
                }

                for ($i = 1; $i <= 12; $i++)
                {
                    $data['Statistics'][$member['ClubId']]['ADEPS'][$i] = 0;
                }

                for ($i = 1; $i <= 20; $i++)
                {
                    $data['Statistics'][$member['ClubId']]['Grade'][$i] = 0;
                }
            }
        }

        foreach ($query as $member)
        {
            if ($member['Birthday']->format('Ymd') > $limit[0])
            {
                $member['Sex'] == 1 ? $data['Statistics'][$member['ClubId']]['Limits'][1]++ : $data['Statistics'][$member['ClubId']]['Limits'][2]++;
            }
            elseif ($member['Birthday']->format('Ymd') > $limit[1])
            {
                $member['Sex'] == 1 ? $data['Statistics'][$member['ClubId']]['Limits'][3]++ : $data['Statistics'][$member['ClubId']]['Limits'][4]++;
            }
            elseif ($member['Birthday']->format('Ymd') > $limit[2])
            {
                $member['Sex'] == 1 ? $data['Statistics'][$member['ClubId']]['Limits'][5]++ : $data['Statistics'][$member['ClubId']]['Limits'][6]++;
            }
            elseif ($member['Birthday']->format('Ymd') > $limit[3])
            {
                $member['Sex'] == 1 ? $data['Statistics'][$member['ClubId']]['Limits'][7]++ : $data['Statistics'][$member['ClubId']]['Limits'][8]++;
            }
            elseif ($member['Birthday']->format('Ymd') > $limit[4])
            {
                $member['Sex'] == 1 ? $data['Statistics'][$member['ClubId']]['Limits'][9]++ : $data['Statistics'][$member['ClubId']]['Limits'][10]++;
            }
            else
            {
                $member['Sex'] == 1 ? $data['Statistics'][$member['ClubId']]['Limits'][11]++ : $data['Statistics'][$member['ClubId']]['Limits'][12]++;
            }

            if (!is_null($member['Level']))
            {
                $member['Sex'] == 1 ? $member['Level'] = $member['Level'] * 2 - 1 : $member['Level'] = $member['Level'] * 2;

                $data['Statistics'][$member['ClubId']]['ADEPS'][$member['Level']]++;
            }

            $data['Statistics'][$member['ClubId']]['Grade'][$member['Grade']]++;
        }

        $data['Province'] = $province;

        return $this->render('Statistics/province.html.twig', array('data' => $data));
    }

    /**
     * @param DateTime $date
     * @return array
     */
    public function getLimit(DateTime $date): array
    {
        $limit[0] = date('Ymd', strtotime('-6 years', $date->getTimestamp()));
        $limit[1] = date('Ymd', strtotime('-12 years', $date->getTimestamp()));
        $limit[2] = date('Ymd', strtotime('-18 years', $date->getTimestamp()));
        $limit[3] = date('Ymd', strtotime('-25 years', $date->getTimestamp()));
        $limit[4] = date('Ymd', strtotime('-35 years', $date->getTimestamp()));

        return $limit;
    }
}
