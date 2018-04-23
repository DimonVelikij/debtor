<?php

namespace AppBundle\Controller\Admin;

use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class FlatAdminController extends CRUDController
{
    /**
     * получение списка должников
     * @param Request $request
     * @return JsonResponse
     */
    public function debtorsAction(Request $request)
    {
        return new JsonResponse([]);
    }
}