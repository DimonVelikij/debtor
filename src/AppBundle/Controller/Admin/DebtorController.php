<?php

namespace AppBundle\Controller\Admin;

use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\Request;

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

    public function typesAction(Request $request)
    {
        dump(1);die;
    }
}