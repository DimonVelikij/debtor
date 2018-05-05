<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Debtor;
use AppBundle\Entity\Flat;
use AppBundle\Entity\OwnershipStatus;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use JMS\Serializer\SerializationContext;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class FlatAdminController extends CRUDController
{
    /**
     * получение списка должников по id помещения
     * @param Request $request
     * @param $flat_id
     * @return Response
     * @throws \Exception
     */
    public function debtorListAction(Request $request, $flat_id)
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user) {
            throw new AccessDeniedException('Bad credentials');
        }

        /** @var Flat $flat */
        $flat = $this->getDoctrine()->getRepository('AppBundle:Flat')->findOneBy(['id' => $flat_id]);

        if (!$flat) {
            throw new \Exception("Undefined flat by id: '{$flat_id}'");
        }

        if (//если пользователь не суперадмин и помещение не обслуживается УК пользователя
            !$user->isSuperAdmin() &&
            $flat->getHouse()->getCompany()->getId() != $user->getCompany()->getId()
        ) {
            throw new AccessDeniedException('Bad credentials');
        }

        return new Response(
            $this->get('jms_serializer')->serialize($flat->getDebtors(), 'json', SerializationContext::create()->setGroups(['cms-debtor']))
        );
    }

    /**
     * получение типов должников
     * @param Request $request
     * @return Response
     */
    public function debtorTypesAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user) {
            throw new AccessDeniedException('Bad credentials');
        }

        $debtorTypes = $this->getDoctrine()->getRepository('AppBundle:DebtorType')->findAll();

        return new Response(
            $this->get('jms_serializer')->serialize($debtorTypes, 'json', SerializationContext::create()->setGroups(['cms-debtor']))
        );
    }

    /**
     * получение статусов собственности
     * @param Request $request
     * @return Response
     */
    public function ownershipStatusesAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user) {
            throw new AccessDeniedException('Bad credentials');
        }

        /** @var EntityRepository $ownershipStatusRepo */
        $ownershipStatusRepo = $this->getDoctrine()->getRepository('AppBundle:OwnershipStatus');

        /** @var QueryBuilder $ownershipStatusQueryBuilder */
        $ownershipStatusQueryBuilder = $ownershipStatusRepo
            ->createQueryBuilder('status');

        $ownershipStatuses = $ownershipStatusQueryBuilder
            ->where($ownershipStatusQueryBuilder->expr()->isNull('status.parent'))
            ->getQuery()
            ->getResult();

        return new Response(
            $this->get('jms_serializer')->serialize(
                $ownershipStatuses,
                'json',
                SerializationContext::create()->setGroups(['cms-debtor'])
            )
        );
    }

    public function submitDebtorAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user) {
            throw new AccessDeniedException('Bad credentials');
        }

        /** @var array $data */
        $data = json_decode($request->getContent(), true);

        //собираем данные, пришедшие с фронта
        $input = [
            'flatId'            =>  $data['flatId'] ?? null,
            'id'                =>  $data['id'] ?? null,
            'type'              =>  $data['type'] ?? null,
            'name'              =>  $data['name'] ?? null,
            'phone'             =>  $data['phone'] ?? null,
            'email'             =>  $data['email'] ?? null,
            'location'          =>  $data['location'] ?? null,
            'archive'           =>  $data['archive'] ?? false,
            'subscriber'        =>  $data['subscriber'] ?? false,
            'ownershipStatus'   =>  $data['ownershipStatus'] ?? null,
            'startDateOwnership'=>  $data['startDateOwnership'] ?? null,
            'endDateOwnership'  =>  $data['endDateOwnership'] ?? null,
            'shareSize'         =>  $data['shareSize'] ?? null,
            'ownerName'         =>  $data['ownerName'] ?? null,
            'dateOfBirth'       =>  $data['dateOfBirth'] ?? null,
            'placeOfBirth'      =>  $data['placeOfBirth'] ?? null,
            'ogrnip'            =>  $data['ogrnip'] ?? null,
            'inn'               =>  $data['inn'] ?? null,
            'ogrn'              =>  $data['ogrn'] ?? null,
            'bossName'          =>  $data['bossName'] ?? null,
            'bossPosition'      =>  $data['bossPosition'] ?? null
        ];

        //если не указан id помещения, то не к чему привязать должника
        if (!$input['flatId']) {
            throw new \Exception("Undefined parameter 'flatId'");
        }

        //валидация формы
        $errors = $this->validateDebtor($input);

        if (count($errors)) {
            return new JsonResponse([
                'success'   =>  false,
                'errors'    =>  $errors
            ]);
        }

        //получаем статус собственности
        $ownershipStatus = $this->getDoctrine()->getRepository('AppBundle:OwnershipStatus')->find($input['ownershipStatus']['id']);

        if (count($ownershipStatus->getChildren())) {
            //если у выбранного статуса есть дочение статусы - нужно выбрать один из дочерних
            return new JsonResponse([
                'success'   =>  false,
                'errors'    =>  [
                    'ownershipStatus'  =>  'Укажите статус собственности'
                ]
            ]);
        }

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        //создаем или получаем объект должника
        $debtor = $this->findOrCreateDebtor($input);
        $debtor
            ->setName($input['name'])
            ->setPhone($input['phone'])
            ->setEmail($input['email'])
            ->setLocation($input['location'])
            ->setStartDateOwnership($input['startDateOwnership'] ? \DateTime::createFromFormat('dmY', $input['startDateOwnership']) : null)
            ->setEndDateOwnership($input['endDateOwnership'] ? \DateTime::createFromFormat('dmY', $input['endDateOwnership']) : null)
            ->setArchive($input['archive'])
            ->setSubscriber($input['subscriber'])
            ->setShareSize($input['shareSize'])
            ->setDateOfBirth($input['dateOfBirth'] ? \DateTime::createFromFormat('dmY', $input['dateOfBirth']) : null)
            ->setPlaceOfBirth($input['placeOfBirth'])
            ->setOwnerName($input['ownerName'])
            ->setOgrnip($input['ogrnip'])
            ->setInn($input['inn'])
            ->setOgrn($input['ogrn'])
            ->setBossName($input['bossName'])
            ->setBossPosition($input['bossPosition'])
            ->setType($em->getReference('AppBundle:DebtorType', $input['type']['id']))
            ->setOwnershipStatus($em->getReference('AppBundle:OwnershipStatus', $input['ownershipStatus']['id']))
            ->setFlat($em->getReference('AppBundle:Flat', $input['flatId']));

        $em->persist($debtor);
        $em->flush();

        return new JsonResponse([
            'success'   =>  true,
            'debtor'    =>  json_decode($this->get('jms_serializer')
                ->serialize($debtor, 'json', SerializationContext::create()->setGroups(['cms-debtor']))
            )
        ]);
    }

    /**
     * валидация формы
     * @param array $input
     * @return array
     * @throws \Exception
     */
    private function validateDebtor(array $input)
    {
        if (!$input['type']) {
            return [
                'type'  =>  'Укажите тип должника'
            ];
        }

        $errors = [];

        $validateMethod = 'get' . ucfirst($input['type']['alias']) . 'Constraints';

        if (!method_exists($this, $validateMethod)) {
            throw new \Exception("Undefined method '{$validateMethod}'");
        }


        /** @var ValidatorInterface $validator */
        $validator = $this->get('validator');

        $constraints = $this->$validateMethod($input);

        foreach ($constraints as $name => $constraint) {
            $validationResult = $validator->validate($input[$name], $constraint);

            if (count($validationResult)) {
                $errors[$name] = $this->get('translator')->trans($validationResult[0]->getMessage());
            }
        }

        return $errors;
    }

    /**
     * базовые ограничения
     * @param array $input
     * @return array
     */
    private function getBaseConstraints(array $input)
    {
        $constraints = [
            'type'              =>  [
                new NotBlank(['message' =>  'Укажите тип должника'])
            ],
            'phone'             =>  [
                new Regex(['pattern'    =>  '/^\d+$/', 'message'    =>  'Невено указан телефон'])
            ],
            'email'             =>  [
                new Email(['message'    =>  'Неверно введен E-mail'])
            ],
            'ownershipStatus'   =>  [
                new NotBlank(['message' =>  'Укажите статус собственности'])
            ],
            'startDateOwnership'=>  [
                new Regex(['pattern'    =>  '/^([0-2]\d|3[01])(0\d|1[012])(19|20)(\d\d)$/', 'message'   =>  'Неверно указана дата начала собственности'])
            ],
            'endDateOwnership'  =>  [
                new Regex(['pattern'    =>  '/^([0-2]\d|3[01])(0\d|1[012])(19|20)(\d\d)$/', 'message'   =>  'Неверно указана дата окончания собственности'])
            ]
        ];

        if ($input['ownershipStatus']) {
            if (
                $input['ownershipStatus']['alias'] == 'owner_shared' ||
                $input['ownershipStatus']['alias'] == 'legal_representative_underage_owner_shared'
            ) {
                $constraints['shareSize'] = [
                    new NotBlank(['message' => 'Укажите размер доли']),
                    new Regex(['pattern' => '/^\d\/\d{1,2}$/', 'message' => 'Неверно указан размер доли'])
                ];
            }

            if (
                $input['ownershipStatus']['alias'] == 'legal_representative_underage_owner_individual' ||
                $input['ownershipStatus']['alias'] == 'legal_representative_underage_owner_shared' ||
                $input['ownershipStatus']['alias'] == 'legal_representative_underage_owner_joint'
            ) {
                $constraints['ownerName'] = [
                    new NotBlank(['message' => 'Укажите ФИО собственника'])
                ];
            }
        }

        return $constraints;
    }

    /**
     * получение ограничений для формы физ лица
     * @param array $input
     * @return array
     */
    private function getIndividualConstraints(array $input)
    {
        $baseConstraints = $this->getBaseConstraints($input);

        $individualConstraints = [
            'name'  =>  [
                new NotBlank(['message' => 'Укажите ФИО'])
            ],
            'dateOfBirth'   =>  [
                new Regex(['pattern' => '/^([0-2]\d|3[01])(0\d|1[012])(19|20)(\d\d)$/', 'message' => 'Неверно указана дата рождения'])
            ]
        ];

        return array_merge($baseConstraints, $individualConstraints);
    }

    /**
     * получение ограничений для формы ИП
     * @param array $input
     * @return array
     */
    private function getBusinessmanConstraints(array $input)
    {
        $baseConstraints = $this->getBaseConstraints($input);

        $businessmanConstraints = [
            'name'  =>  [
                new NotBlank(['message' => 'Укажите ФИО'])
            ]
        ];

        return array_merge($baseConstraints, $businessmanConstraints);
    }

    /**
     * получение ограничений для формы Юр лица
     * @param array $input
     * @return array
     */
    private function getLegalConstraints(array $input)
    {
        $baseConstraints = $this->getBaseConstraints($input);

        $legalConstraints = [
            'name'  =>  [
                new NotBlank(['message' => 'Укажите наименование'])
            ]
        ];

        return array_merge($baseConstraints, $legalConstraints);
    }

    /**
     * поиск или создание объекта должника
     * @param array $input
     * @return Debtor|object
     * @throws \Exception
     */
    private function findOrCreateDebtor(array $input)
    {
        if (!$input['id']) {
            return new Debtor();
        }

        $debtor = $this->getDoctrine()->getRepository('AppBundle:Debtor')->find($input['id']);

        if (!$debtor) {
            throw new \Exception("Undefined debtor by id {$input['id']}");
        }

        return $debtor;
    }
}