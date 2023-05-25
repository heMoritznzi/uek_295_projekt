<?php

namespace App\Tests;


use App\DTO\CreateUpdateFaecher;
use App\DTO\CreateUpdateNote;
use App\DTO\FilterFaecher;
use App\DTO\ShowFaecher;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\StringInput;


class EntitysTest extends WebTestCase
{


    protected static $application;

    private static $client;

    public static function setUpBeforeClass(): void
    {
        self::$client = new Client([
                "base_uri" => "http://localhost:8000/index_test.php/"

            ]);

        $client = self::createClient();
        self::$application = new Application($client->getKernel());
        self::$application->setAutoExit(false);


        self::$application->run(new StringInput("doctrine:database:drop --force --quiet"));
        self::$application->run(new StringInput("doctrine:database:create --quiet"));
        self::$application->run(new StringInput("doctrine:schema:create --quiet"));
        self::$application->run(new StringInput("doctrine:fixtures:load --quiet"));

    }


    public function testLoadAll()
    {

        $request = self::$client->request("POST", "api/login_check",
            [
                "body" => json_encode([
                    "username" => "Test",
                    "password" => "test1234"
                ])
            ]);

        $token = json_decode($request->getBody())->token;

        $request = self::$client->request("GET", "api/faecher",
            [
                "headers" => [
                    "Authorization" => "Bearer " . $token
                ]
            ]);

        $this->assertTrue($request->getStatusCode() == 200);

        $this->assertIsArray(json_decode($request->getBody()));
    }


    public function testLoadNote()
    {

        $request = self::$client->request("POST", "api/login_check",
            [
                "body" => json_encode([
                    "username" => "Test",
                    "password" => "test1234"
                ])
            ]);

        $token = json_decode($request->getBody())->token;

        $request = self::$client->request("GET", "api/noten",

            [
                "headers" => [
                    "Authorization" => "Bearer " . $token
                ]
            ]);

        $this->assertTrue($request->getStatusCode() == 200);

        $this->assertIsArray(json_decode($request->getBody()));
    }

    public function testCreate()
        {
           $dto = new CreateUpdateFaecher();
           $dto->fach = "Sport";

           $request = self::$client->request("POST", "api/login_check",
           [
               "body" => json_encode([
                   "username" => "Test",
                   "password" => "test1234"
               ])
           ]);

           $token = json_decode($request->getBody())->token;

            $request = self::$client->request("POST", "api/faecher",
                [
                    "body" => json_encode($dto),
                    "headers" => [
                        "Authorization" => "Bearer " . $token
                    ]
                ]);


           $response = json_decode($request->getBody());


           $this->assertTrue($request->getStatusCode() == 200);
           $this->assertTrue($response->fach == "Sport");

        }


    public function testCreateNote()
    {
        $dto = new CreateUpdateNote();
        $dto->note = 2.5;
        $dto->fach = 1;




        $request = self::$client->request("POST", "api/login_check",
            [
                "body" => json_encode([
                    "username" => "Test",
                    "password" => "test1234"
                ])
            ]);

        $token = json_decode($request->getBody())->token;

        $request = self::$client->request("POST", "api/noten",
            [
                "body" => json_encode($dto),
                "headers" => [
                    "Authorization" => "Bearer " . $token
                ]
            ]);


        $response = json_decode($request->getBody());


        $this->assertTrue($request->getStatusCode() == 200);

    }

    public function testputNote(): void {


        $dto = new CreateUpdateNote();

        $dto->note = 4;

        $request = self::$client->request("POST", "api/login_check",
            [
                "body" => json_encode([
                    "username" => "Test",
                    "password" => "test1234"
                ])
            ]);

        $token = json_decode($request->getBody())->token;

        $drequest = self::$client->request("PUT", "api/noten/1",
            [
                "body" => json_encode($dto),
                "headers" => [
                    "Authorization" => "Bearer " . $token
                ]
            ]);


        $this->assertTrue($drequest->getStatusCode() == 200);

    }


    public function testdeleteNote(): void {

        $request = self::$client->request("POST", "api/login_check",
            [
                "body" => json_encode([
                    "username" => "Test",
                    "password" => "test1234"
                ])
            ]);

        $token = json_decode($request->getBody())->token;

        $drequest = self::$client->request("DELETE", "api/faecher/1",
            [
                "headers" => [
                    "Authorization" => "Bearer " . $token
                ]
            ]);

        $this->assertTrue($drequest->getStatusCode() == 200);
    }


    public function testdurchschnitt(): void {

        $request = self::$client->request("POST", "api/login_check",
            [
                "body" => json_encode([
                    "username" => "Test",
                    "password" => "test1234"
                ])
            ]);

        $token = json_decode($request->getBody())->token;

        $drequest = self::$client->request("GET", "api/faecher/2/notenschnitt",
            [
                "headers" => [
                    "Authorization" => "Bearer " . $token
                ]
            ]);

        $this->assertTrue($drequest->getStatusCode() == 200);

    }



    public function testSomething(): void
    {
        $this->assertTrue(true);
    }

}
