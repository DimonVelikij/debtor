<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Debtor;
use AppBundle\Entity\User;
use AppBundle\Service\DebtorValidator;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use JMS\Serializer\SerializationContext;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DebtorController
 * @package AppBundle\Controller\Admin
 */
class DebtorController extends CRUDController
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction()
    {
        $newObject = $this->admin->getNewInstance();

        return $this->render('AppBundle:Admin/Debtor:create.html.twig', [
            'action'    =>  'create',
            'object'    =>  $newObject
        ]);
    }

    /**
     * получение типов должников
     * @param Request $request
     * @return Response
     */
    public function typesAction(Request $request)
    {
        $debtorTypes = $this->getDoctrine()->getRepository('AppBundle:DebtorType')->findAll();

        return new Response(
            $this->get('jms_serializer')->serialize(
                $debtorTypes,
                'json',
                SerializationContext::create()->setGroups(['cms-debtor'])
            )
        );
    }

    /**
     * Получение списка управляющих компаний
     * @param Request $request
     * @return Response
     */
    public function companiesAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();

        $companies = $user->isSuperAdmin() ?
            $this->getDoctrine()->getRepository('AppBundle:Company')->findAll():
            [$user->getCompany()];

        return new Response(
            $this->get('jms_serializer')->serialize(
                $companies,
                'json',
                SerializationContext::create()->setGroups(['cms-debtor'])
            )
        );
    }

    /**
     * получение статусов собственности
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function ownershipStatusesAction(Request $request)
    {
        if (!$request->get('debtor_type')) {
            throw new \Exception("Undefined parameter 'debtor_type'");
        }

        /** @var array $resultStatuses */
        $resultStatuses = null;

        /** @var EntityRepository $ownershipStatusesRepo */
        $ownershipStatusesRepo = $this->getDoctrine()->getRepository('AppBundle:OwnershipStatus');

        $selectedStatusId = $request->get('selected_status_id');
        $ownershipStatuses = $selectedStatusId ?
            $ownershipStatusesRepo->findBy(['parent' => $selectedStatusId]) :
            $ownershipStatusesRepo->findAll();

        switch ($request->get('debtor_type')) {
            case 'individual':
                /** @var OwnershipStatus $ownershipStatus */
                foreach ($ownershipStatuses as $ownershipStatus) {
                    if (!$ownershipStatus->getParent() || $selectedStatusId) {
                        $resultStatuses[] = $ownershipStatus;
                    }
                }
                break;
            case 'businessman':
            case 'legal_entity':
                /** @var OwnershipStatus $ownershipStatus */
                foreach ($ownershipStatuses as $ownershipStatus) {
                    if ($ownershipStatus->getParent() && $ownershipStatus->getParent()->getAlias() == 'owner') {
                        $resultStatuses[] = $ownershipStatus;
                    }
                }
                break;
            default:
                throw new \Exception("Fail parameter 'debtor_type'. Available 'individual', 'businessman', 'legal_entity'");
        }

        return new Response(
            $this->get('jms_serializer')->serialize(
                $resultStatuses,
                'json',
                SerializationContext::create()->setGroups(['cms-debtor'])
            )
        );
    }

    /**
     * сохранение данных пользователя
     * @param Request $request
     * @return JsonResponse
     */
    public function saveAction(Request $request)
    {
        /** @var DebtorValidator $debtorValidator */
        $debtorValidator = $this->get('app.service.debtor_validator');
        $debtorData = $debtorValidator->prepareData(json_decode($request->getContent(), true));
        $validationResult = $debtorValidator->validate($debtorData);

        if (!$validationResult['success']) {
            return new JsonResponse($validationResult);
        }

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $debtor = new Debtor();
        $debtor
            ->setCompany($em->getReference('AppBundle:Company', $debtorData['company']))
            ->setDebtorType($em->getReference('AppBundle:DebtorType', $debtorData['debtorType']['id']))
            ->setOwnershipStatus($em->getReference('AppBundle:OwnershipStatus', 18))
            ->setName($debtorData['name'])
            ->setPhone($debtorData['phone'])
            ->setEmail($debtorData['email'])
            ->setLocation($debtorData['location'])
            ->setStartDateOwnership(null)
            ->setEndDateOwnership(null)
            ->setStartDebtPeriod($debtorData['startDebtPeriod'] ? \DateTime::createFromFormat('dmY', $debtorData['startDebtPeriod']) : null)
            ->setEndDebtPeriod($debtorData['endDebtPeriod'] ? \DateTime::createFromFormat('dmY', $debtorData['endDebtPeriod']) : null)
            ->setDateFillDebt($debtorData['dateFillDebt'] ? \DateTime::createFromFormat('dmY', $debtorData['dateFillDebt']) : null)
            ->setSumDebt($debtorData['sumDebt'] ?? null)
            ->setPeriodAccruedDebt($debtorData['periodAccruedDebt'])
            ->setPeriodPayDebt($debtorData['periodPayDebt'])
            ->setDateFillFine($debtorData['dateFillFine'] ? \DateTime::createFromFormat('dmY', $debtorData['dateFillFine']) : null)
            ->setSumFine($debtorData['sumFine'] ?? null)
            ->setPeriodAccruedFine($debtorData['periodAccruedFine'])
            ->setPeriodPayFine($debtorData['periodPayFine'])
            ->setArhive(false)
            ->setDateOfBirth($debtorData['dateOfBirth'] ? \DateTime::createFromFormat('dmY', $debtorData['dateOfBirth']) : null)
            ->setPlaceOfBirth($debtorData['placeOfBirth'] ?? null)
            ->setOwnerName(null)
            ->setOgrnip($debtorData['ogrnip'] ?? null)
            ->setInn($debtorData['inn'] ?? null)
            ->setOgrn($debtorData['ogrn'] ?? null)
            ->setBossName($debtorData['bossName'] ?? null)
            ->setBossPosition($debtorData['bossPosition'] ?? null);

        $em->persist($debtor);
        $em->flush();

        return new JsonResponse([
            'success'   =>  true,
            'edit'      =>  $this->generateUrl('admin_app_debtor_edit', ['id' => $debtor->getId()])
        ]);
    }
}