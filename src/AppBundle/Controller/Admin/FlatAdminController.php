<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Flat;
use AppBundle\Entity\User;
use JMS\Serializer\SerializationContext;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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
        $debtorTypes = $this->getDoctrine()->getRepository('AppBundle:DebtorType')->findAll();

        return new Response(
            $this->get('jms_serializer')->serialize($debtorTypes, 'json', SerializationContext::create()->setGroups(['cms-debtor']))
        );
    }
}