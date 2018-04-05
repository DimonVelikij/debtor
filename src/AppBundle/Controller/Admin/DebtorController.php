<?php

namespace AppBundle\Controller\Admin;

use JMS\Serializer\SerializationContext;
use Sonata\AdminBundle\Controller\CRUDController;
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
}