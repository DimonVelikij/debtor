<?php

namespace AppBundle\Admin\traits;

use AppBundle\Entity\User;
use Symfony\Component\DependencyInjection\Container;

trait UserTrait
{
    /**
     * @return Container
     */
    abstract public function getContainer();

    /**
     * @return User
     * @throws \Exception
     */
    public function getUser()
    {
        return $this->getContainer()->get('security.token_storage')->getToken()->getUser();
    }
}