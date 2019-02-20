<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Flat;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/personal-account")
 *
 * Class PersonalAccountController
 * @package AppBundle\Controller
 */
class PersonalAccountController extends Controller
{
    /**
     * получение списка лицевых счетов помещения
     * @Route("/list/{flatId}", requirements={"flatId": "\d+"})
     * @param $flatId
     * @return JsonResponse
     */
    public function listAction($flatId)
    {
        /** @var Flat $flat */
        $flat = $this->getDoctrine()->getRepository('AppBundle:Flat')->find($flatId);
        //проверка на пользователя
        return new JsonResponse([]);
    }
}
