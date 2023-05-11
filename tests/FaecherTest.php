<?php

namespace App\Tests;


use App\DTO\CreateUpdateFaecher;
use App\DTO\ShowFaecher;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\StringInput;


class FaecherTest extends WebTestCase
{


    protected static $application;

    private static $client;

    public static function setUpBeforeClass(): void
    {
        self::$client = new Client([
                "base_uri" => "http://localhost:8000"

            ]);

        $client = self::createClient();
        self::$application = new Application($client->getKernel());
        self::$application->setAutoExit(false);


        self::$application->run(new StringInput("doctrine:database:drop --force"));
        self::$application->run(new StringInput("doctrine:database:create"));
        self::$application->run(new StringInput("doctrine:schema:create"));
        self::$application->run(new StringInput("doctrine:fixtures:load"));

    }




        public function testCreate()
        {
           $dto = new CreateUpdateFaecher();
           $dto->fach = "Sport";

           $request = self::$client->request("POST", "/api/faecher",
           [
               "body" => json_encode($dto)
           ]);


           $response = json_decode($request->getBody());


           $this->assertTrue($request->getStatusCode() == 200);
           $this->assertTrue($response->fach == "Sport");

        }




    public function testSomething(): void
    {
        $this->assertTrue(true);
    }

}
