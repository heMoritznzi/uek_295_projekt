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


        self::$application->run(new StringInput("doctrine:database:drop --force"));
        self::$application->run(new StringInput("doctrine:database:create"));
        self::$application->run(new StringInput("doctrine:schema:create"));
        self::$application->run(new StringInput("doctrine:fixtures:load"));

    }


    public function testLoadAll()
    {

        $request = self::$client->request('GET', 'api/faecher', []);

        $this->assertTrue($request->getStatusCode() == 200);

        $this->assertIsArray(json_decode($request->getBody()));
    }


    public function testLoadNote()
    {

        $request = self::$client->request('GET', 'api/noten', []);

        $this->assertTrue($request->getStatusCode() == 200);

        $this->assertIsArray(json_decode($request->getBody()));
    }

    public function testCreate()
        {
           $dto = new CreateUpdateFaecher();
           $dto->fach = "Sport";

           $request = self::$client->request("POST", "api/faecher",
           [
               "body" => json_encode($dto)
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




        $request = self::$client->request("POST", "api/noten",
            [
                "body" => json_encode($dto)
            ]);


        $response = json_decode($request->getBody());


        $this->assertTrue($request->getStatusCode() == 200);

    }

    public function testputNote(): void {


        $dto = new CreateUpdateNote();

        $dto->note = 4;

        $putrequest = self::$client->request("PUT", "api/noten/1", [

            "body" => json_encode($dto)

        ]);


        $this->assertTrue($putrequest->getStatusCode() == 200);

    }


    public function testdeleteNote(): void {

        $deleterequest = self::$client->request("DELETE", "api/noten/1");

        $this->assertTrue($deleterequest->getStatusCode() == 200);
    }


    public function testdurchschnitt(): void {
        $durchschnittrequest = self::$client->request("GET", "api/faecher/1/notenschnitt");

        $this->assertTrue($durchschnittrequest->getStatusCode() == 200);

    }



    public function testSomething(): void
    {
        $this->assertTrue(true);
    }

}
