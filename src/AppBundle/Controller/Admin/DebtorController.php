<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Debtor;
use AppBundle\Entity\DebtorType;
use AppBundle\Entity\OwnershipStatus;
use AppBundle\Entity\User;
use AppBundle\Service\DebtorValidator;
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

    public function saveAction(Request $request)
    {
        /** @var DebtorValidator $debtorValidator */
        $debtorValidator = $this->get('app.service.debtor_validator');
        $debtorData = $debtorValidator->prepareData(json_decode($request->getContent(), true));
        $validationResult = $debtorValidator->validate($debtorData);

        if (!$validationResult['success']) {
            return new JsonResponse($validationResult);
        }

        /** @var DebtorType $debtorType */
        $debtorType = $this->getDoctrine()
            ->getRepository('AppBundle:DebtorType')
            ->find($debtorData['debtorType']['id']);

        /** @var OwnershipStatus $ownershipStatus */
        $ownershipStatus = $this->getDoctrine()
            ->getRepository('AppBundle:OwnershipStatus')
            ->find(18);

        /** @var User $user */
        $user = $this->getUser();

        $debtor = new Debtor();
        $debtor
            ->setCompany($user->getCompany())
            ->setDebtorType($debtorType)
            ->setOwnershipStatus($ownershipStatus)
            ->setName($debtorData['name'])
            ->setPhone($debtorData['phone'])
            ->setEmail($debtorData['email'])
            ->setLocation($debtorData['location'])
            ->setStartDateOwnership(null)
            ->setEndDateOwnership(null)
            ->setStartDebtPeriod(null/*\DateTime::createFromFormat('dmY', $debtorData['startDebtPeriod'])*/)
            ->setEndDebtPeriod(null/*\DateTime::createFromFormat('dmY', $debtorData['endDebtPeriod'])*/)
            ->setDateFillDebt(null/*\DateTime::createFromFormat('dmY', $debtorData['dateFillDebt'])*/)
            ->setSumDebt($debtorData['sumDebt'])
            ->setPeriodAccruedDebt($debtorData['periodAccruedDebt'])
            ->setPeriodPayDebt($debtorData['periodPayDebt'])
            ->setDateFillFine(null/*\DateTime::createFromFormat('dmY', $debtorData['dateFillFine'])*/)
            ->setSumFine($debtorData['sumFine'])
            ->setPeriodAccruedFine($debtorData['periodAccruedFine'])
            ->setPeriodPayFine($debtorData['periodPayFine'])
            ->setArhive(false)
            ->setDateOfBirth(null/*\DateTime::createFromFormat('dmY', $debtorData['dateOfBirth'])*/)
            ->setPlaceOfBirth($debtorData['placeOfBirth'])
            ->setOwnerName(null)
            ->setOgrnip(null)
            ->setInn(null)
            ->setOgrn(null)
            ->setBossName(null)
            ->setBossPosition(null)
        ;

        $em = $this->getDoctrine()->getManager();
        $em->persist($debtor);
        $em->flush();

        //редирект на редактирование
    }
}