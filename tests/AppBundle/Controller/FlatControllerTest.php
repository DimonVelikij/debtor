<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\HttpFoundation\Response;

class FlatControllerTest extends ControllerTestCase
{
    /**
     * получение списка типов помещений
     */
    public function testTypesAction()
    {
        $client = $this->getSuperAdminClient();
        $client->request('GET', '/api/flat/types');

        /** @var Response $response */
        $response = $client->getResponse();
        /** @var array $flatTypes */
        $flatTypes = json_decode($response->getContent(), true);

        $this->assertResponseIsCorrect($response);

        foreach ($flatTypes as $flatType) {
            $this->assertFlatType($flatType);
        }
    }

    /**
     * @return array
     */
    public function dataHousesAction()
    {
        return [
            'суперадмин запрашивает список домов'       => [
                'SuperAdmin',
                2
            ],
            'не суперадмин запрашивает список домов'    =>  [
                'UserAdmin',
                1
            ]
        ];
    }

    /**
     * список домов
     * @dataProvider dataHousesAction
     * @param $user
     * @param $countHouses
     */
    public function testHousesAction($user, $countHouses)
    {
        $getClientMethod = 'get' . $user . 'Client';

        /** @var Client $client */
        $client = $this->$getClientMethod();
        $client->request('GET', '/api/flat/houses');

        /** @var Response $response */
        $response = $client->getResponse();
        /** @var array $houses */
        $houses = json_decode($response->getContent(), true);

        $this->assertResponseIsCorrect($response);

        $this->assertTrue(count($houses) === $countHouses);

        foreach ($houses as $house) {
            $this->assertIsHouse($house);
        }
    }

    /**
     * информация о помещении
     */
    public function testFlatAction()
    {
        $flat = $this->getEntityManager()->getRepository('AppBundle:Flat')
            ->createQueryBuilder('flat')
            ->select('flat.id')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
        $client = $this->getSuperAdminClient();
        $client->request('GET', '/api/flat/' . $flat[0]['id']);

        /** @var Response $response */
        $response = $client->getResponse();
        /** @var array $flat */
        $flat = json_decode($response->getContent(), true);

        $this->assertResponseIsCorrect($response);
        $this->assertIsFlat($flat);
    }

    /**
     * @param array $flatType
     */
    private function assertFlatType(array $flatType)
    {
        $this->assertTrue(count($flatType) === 2);

        $this->assertArrayHasKey('id', $flatType);
        $this->assertArrayHasKey('title', $flatType);

        $this->assertInternalType('integer', $flatType['id']);
        $this->assertInternalType('string', $flatType['title']);
    }

    /**
     * @param array $house
     */
    private function assertIsHouse(array $house)
    {
        $this->assertTrue(count($house) === 3);

        $this->assertArrayHasKey('id', $house);
        $this->assertArrayHasKey('number', $house);
        $this->assertArrayHasKey('address', $house);

        $this->assertInternalType('integer', $house['id']);
        $this->assertInternalType('string', $house['number']);
        $this->assertInternalType('string', $house['address']);
    }

    /**
     * @param array $flat
     */
    private function assertIsFlat(array $flat)
    {
        $this->assertTrue(count($flat) === 5);

        $this->assertArrayHasKey('id', $flat);
        $this->assertArrayHasKey('number', $flat);
        $this->assertArrayHasKey('archive', $flat);
        $this->assertArrayHasKey('house', $flat);
        $this->assertArrayHasKey('type', $flat);

        $this->assertInternalType('integer', $flat['id']);
        $this->assertInternalType('string', $flat['number']);
        $this->assertIsHouse($flat['house']);
        $this->assertFlatType($flat['type']);
    }
}
