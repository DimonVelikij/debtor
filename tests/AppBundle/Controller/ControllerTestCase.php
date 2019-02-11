<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Tests\AppBundle\AppBundleTestCase;

class ControllerTestCase extends AppBundleTestCase
{
    /** @var  Client */
    private $client;

    protected function setUp()
    {
        parent::setUp();

        $this->client = self::createClient();
    }

    /**
     * корректный ответ
     * @param Response $response
     */
    protected function assertResponseIsCorrect(Response $response)
    {
        $this->assertTrue($response->isSuccessful());
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());
    }

    /**
     * некорректный ответ
     * @param Response $response
     */
    protected function assertResponseIsInCorrect(Response $response)
    {
        $this->assertTrue($response->isClientError() || $response->isServerError());
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());
    }

    /**
     * @return Client
     */
    protected function getClient()
    {
        return $this->client;
    }

    /**
     * залогиненный как суперадмин клиент
     * @return Client
     */
    protected function getSuperAdminClient()
    {
        $this->client->getCookieJar()->set($this->logIn());

        return $this->client;
    }

    /**
     * залогинен как обычный пользователь
     * @return Client
     */
    protected function getUserAdminClient()
    {
        $this->client->getCookieJar()->set($this->logIn(false));

        return $this->client;
    }

    /**
     * авторизация
     * @param bool $isSuperAdmin
     * @return Cookie
     */
    private function logIn($isSuperAdmin = true)
    {
        $session = $this->getContainer()->get('session');
        $firewall = $this->getContainer()->getParameter('fos_user.firewall_name');

        /** @var User $user */
        $user = $this->getEntityManager()->getRepository('AppBundle:User')->findOneBy(['username' => $isSuperAdmin ? 'admin' : 'user']);

        $token = new UsernamePasswordToken($user, null, $firewall, $user->getRoles());
        $session->set('_security_' . $firewall, serialize($token));
        $session->save();

        return new Cookie($session->getName(), $session->getId());
    }
}
