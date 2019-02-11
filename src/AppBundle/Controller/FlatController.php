<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Flat;
use AppBundle\Entity\User;
use AppBundle\Validator\Constraints\FlatExist;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @Route("/flat")
 *
 * Class FlatController
 * @package AppBundle\Controller
 */
class FlatController extends Controller
{
    /**
     * получение типов помещений
     * @Route("/types")
     * @return JsonResponse
     */
    public function typesAction()
    {
        /** @var Serializer $serializer */
        $serializer = $this->get('jms_serializer');

        $flatTypes = $this->getDoctrine()->getRepository('AppBundle:FlatType')->findAll();

        $context = SerializationContext::create()->setSerializeNull(true)->setGroups(['cms-flat']);

        return new JsonResponse(json_decode($serializer->serialize($flatTypes, 'json', $context)));
    }

    /**
     * получение домов
     * @Route("/houses")
     * @return JsonResponse
     */
    public function housesAction()
    {
        /** @var User $user */
        $user = $this->getUser();

        /** @var QueryBuilder $housesQueryBuilder */
        $housesQueryBuilder = $this->getDoctrine()->getRepository('AppBundle:House')
            ->createQueryBuilder('house');

        if (!$user->isSuperAdmin()) {
            $housesQueryBuilder
                ->where($housesQueryBuilder->expr()->eq('house.company', $user->getCompany()->getId()));
        }

        $houses = $housesQueryBuilder->getQuery()->getResult();

        /** @var Serializer $serializer */
        $serializer = $this->get('jms_serializer');

        $context = SerializationContext::create()->setSerializeNull(true)->setGroups(['cms-flat']);

        return new JsonResponse(json_decode($serializer->serialize($houses, 'json', $context)));
    }

    /**
     * получение помещения
     * @Route("/{id}", requirements={"id": "\d+"})
     * @param $id
     * @return JsonResponse
     */
    public function flatAction($id)
    {
        /** @var Flat $flat */
        $flat = $this->getDoctrine()->getRepository('AppBundle:Flat')->find($id);

        if (!$flat) {
            throw $this->createNotFoundException('Flat not found');
        }

        /** @var Serializer $serializer */
        $serializer = $this->get('jms_serializer');

        $context = SerializationContext::create()->setSerializeNull(true)->setGroups(['cms-flat']);

        return new JsonResponse(json_decode($serializer->serialize($flat, 'json', $context)));
    }

    /**
     * сохранение помещения
     * @Route("/submit")
     * @param Request $request
     * @return JsonResponse
     */
    public function submitAction(Request $request)
    {
        /** @var array $data */
        $data = json_decode($request->getContent(), true);

        $input = [
            'id'        =>  $data['id'] ?? null,
            'number'    =>  $data['number'] ?? null,
            'archive'   =>  $data['archive'] ?? null,
            'house'     =>  $data['house'] ?? null,
            'type'      =>  $data['type'] ?? null
        ];

        $constraints = [
            'number'    =>  [
                new NotBlank(['message' => 'Укажите номер']),
                new FlatExist(['flatId' => $input['id']])
            ],
            'house'     =>  [
                new NotBlank(['message' => 'Укажите дом'])
            ],
            'type'      =>  [
                new NotBlank(['message' =>  'Укажите тип'])
            ]
        ];

        $errors = $this->get('app.service.form_validator')->validate($input, $constraints);

        if (count($errors)) {
            return new JsonResponse([
                'success'   =>  false,
                'errors'    =>  $errors
            ]);
        }

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $flat = $this->findOrCreateFlat($input);
        $flat
            ->setNumber($input['number'])
            ->setArchive($input['archive'])
            ->setHouse($em->getReference('AppBundle:House', $input['house']))
            ->setType($em->getReference('AppBundle:FlatType', $input['type']));

        $em->persist($flat);
        $em->flush();

        /** @var Serializer $serializer */
        $serializer = $this->get('jms_serializer');
        $context = SerializationContext::create()->setSerializeNull(true)->setGroups(['cms-flat']);

        return new JsonResponse(json_decode($serializer->serialize($flat, 'json', $context)));
    }

    /**
     * поиск или создание объекта помещения
     * @param array $input
     * @return Flat|object
     */
    private function findOrCreateFlat(array $input)
    {
        if (!$input['id']) {
            return new Flat();
        }

        $flat = $this->getDoctrine()->getRepository('AppBundle:Flat')->find($input['id']);

        if (!$flat) {
            throw $this->createNotFoundException('Not found flat by id: ' . $input['id']);
        }

        return $flat;
    }
}
