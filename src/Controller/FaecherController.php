<?php

namespace App\Controller;

use App\DTO\CreateUpdateFaecher;
use App\DTO\FilterFaecher;
use App\DTO\Mapper\ShowFaecherMapper;
use App\DTO\ShowFaecher;
use App\Entity\Faecher;
use App\Repository\FaecherRepository;
use App\Repository\NoteRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use JMS\Serializer\SerializerInterface;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes\Delete;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\Items;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Parameter;
use OpenApi\Attributes\Post;
use OpenApi\Attributes\Put;
use OpenApi\Attributes\RequestBody;
use phpDocumentor\Reflection\DocBlock\Tags\Reference\Reference;
use phpDocumentor\Reflection\Types\Context;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;


#[Route("/api", name: "api_")]
class FaecherController extends AbstractController
{

    public function __construct(private SerializerInterface $serializer, private FaecherRepository $faecherRepository, private ShowFaecherMapper $mapper, private ValidatorInterface $validator, private LoggerInterface $logger){

    }


    /**
     * gibt alle faecher inklusive deren noten an
     * @param Request $request
     * @param FaecherRepository $faecherRepository
     * @param FilterFaecher $filterFaecher
     * @return JsonResponse aller faecher
     */
    #[Get(requestBody: new RequestBody(
        content: new JsonContent(
            ref: new Model(
                type: FilterFaecher::class
            )
        )
    ))]
    #[\OpenApi\Attributes\Response(
        response: 200,
        description: "gibt alle faecher inklusive deren noten an",
        content:
        new JsonContent(
            type: 'array',
            items: new Items(
                ref: new Model(
                    type: ShowFaecher::class
                )
            )
        )
    )]
    #[Rest\Get('/faecher', name: 'app_faecher')]

    public function loadAll(Request $request, FaecherRepository $faecherRepository, FilterFaecher $filterFaecher): JsonResponse
    {
        // Deserialisierung des Request Body in DTO-Objekt:

        $dtoFilter = $this->serializer->deserialize(
            $request->getContent(),
            FilterFaecher::class,
            "json"
        );

        // Filterung der Fächer anhand des DTO-Objekts:
        $fach = $this->faecherRepository->filterAll($dtoFilter) ?? [];

        $this->logger->info("fach {fach} wurde ausgegeben", ["fach" => $dtoFilter->fach]);

        // Serialisierung der gefundenen Fächer als JSON-Antwort:
        return (new JsonResponse())->setContent(
            $this->serializer->serialize(
                $this->mapper->mapEntitiesToDTOs($fach), "json")
        );



    }


    /**
     * erstellt neue faecher
     * @param Request $request
     * @return JsonResponse mit dem fach und den noten (leeres array)
     *
     */
    #[Post(
        requestBody: new RequestBody(
            content: new JsonContent(
                ref: new Model(
                    type: CreateUpdateFaecher::class,
                    groups: ["create"]
                )
            )
        )

    )]
    #[Rest\Post('/faecher', name: 'app_faecher_post')]
    public function create(Request $request) : JsonResponse {

        // Deserialisierung des Request Body in DTO-Objekt:
        $dtoFach = $this->serializer->deserialize($request->getContent(), CreateUpdateFaecher::class, "json");

        // Validierung des DTO-Objekts:
        $errorResponse = $this->validateDto($dtoFach, ["create"]);

        if ($errorResponse) {
            return $errorResponse;
        }

        // erstellung einer neuen faecher entity
        $entity = new Faecher();
        $entity->setFach($dtoFach->fach);

        // speicherung der entity in der datenbank
        $this->faecherRepository->save($entity, true);


        $this->logger->info("fach {fach} wurde erstellt", ["fach" => $dtoFach->fach]);

        // mit dem serializer entity zu json respons serialisieren und ausgeben
        return (new JsonResponse())->setContent(
            $this->serializer->serialize(
                $this->mapper->mapEntityToDTO($entity), "json")


        );
    }


    /**
     * loescht das ausgewaehlte fach
     * @param $id
     * @return JsonResponse bestaetigung oder fehler des loeschens
     */
    #[\OpenApi\Attributes\Response(
        response: 200,
        description: "löscht das ausgewählten fach",
    )]
    #[Delete(
        requestBody: new RequestBody(
            content: new JsonContent(
                ref: new Model(
                    type: CreateUpdateFaecher::class,
                    groups: ["Delete"]
                )
            )
        )

    )]
    #[Rest\Delete('/faecher/{id}', name: 'app_faecher_delete')]
    public function delete($id) : JsonResponse
    {

        // Fach mit der gegebenen id suchen
        $entityFach = $this->faecherRepository->find($id);
        if(!$entityFach) {
            // gibt 403 fehler zurück wenn die id nicht existiert
            $this->logger->info("fach {fach} wurde nicht gefunden", ["fach" => $entityFach->getId()]);
            return $this->json("Story with ID {$id} does not exist!", status: 403);
        }

        // entity wird von der datenbank gelöscht
        $this->faecherRepository->remove($entityFach, true);

        $this->logger->info("fach {fach} wurde gelöscht", ["fach" => $entityFach->getFach()]);

        // ausgabe einer löschungsbestätigung
        return $this->json("Story with ID " . $id . " Succesfully Deleted");
    }


    /**
     * aendern des ausgewählten fachs
     * @param Request $request
     * @param $id
     * @return JsonResponse bestaetigung oder fehler der aenderung
     */
    #[\OpenApi\Attributes\Response(
        response: 200,
        description: "ändert das ausgewählte fach",
    )]
    #[Put(
        requestBody: new RequestBody(
            content: new JsonContent(
                ref: new Model(
                    type: CreateUpdateFaecher::class,
                    groups: ["Update"]
                )
            )
        )

    )]
    #[Rest\Put('/faecher', name: 'app_faecher_put')]
    public function update(Request $request, $id) : JsonResponse
    {

        // deserialize den request body in ein CreateUpdateFaecher object
        $dto = $this->serializer->deserialize($request->getContent(), CreateUpdateFaecher::class, "json");

        // faecher entity mir der gegebenen id finden
        $entityfach = $this->faecherRepository->find($id);



        if(!$entityfach) {
            // gibt 403 fehler zurück wenn die id nicht existiert
            $this->logger->info("fach {fach} wurde nicht gefunden", ["fach" => $entityfach->getId()]);
            return $this->json("Fach with ID " . $id . " does not exist! ", status: 403);
        }

        $entityfach->setFach($dto->fach);

        // Speichere das Entity mit dem neuen Namen.
        $this->faecherRepository->save($entityfach, true);

        $this->logger->info("fach {fach} wurde geändert", ["fach" => $entityfach->getFach()]);

        // gibt eine bestätigung zurück
        return $this->json("Fach with ID " . $id . " Succesfully Changed");
    }


    /**
     * ausgabe des notenschnitts eines ausgewählen fachs
     * @param Request $request
     * @param FaecherRepository $faecherRepository
     * @param int|null $id
     * @return JsonResponse des notenschnitts des fachs
     */

    #[Get(requestBody: new RequestBody(
        content: new JsonContent(
            ref: new Model(
                type: FilterFaecher::class
            )
        )
    ))]
    #[\OpenApi\Attributes\Response(
        response: 200,
        description: "gibt den notenschnitt eines ausgewählten fachs an",
        content:
        new JsonContent(
            type: 'array',
            items: new Items(
                ref: new Model(
                    type: ShowFaecher::class
                )
            )
        )
    )]
    #[Rest\Get('/faecher/{id}/notenschnitt', name: 'app_faecher_notenschnitt')]
    public function averageGradeBySubject(Request $request, FaecherRepository $faecherRepository, ?int $id): JsonResponse
    {
        // findet das fach mit gegebener id
        $fach = $faecherRepository->find($id);

        if (!$fach) {
            // wenn das fach mit der gegebenen id nicht gefunden wurde gibt es eine 404-Response zurück
            $this->logger->info("fach {fach} wurde nicht gefunden", ["fach" => $fach->getId()]);
            return (new JsonResponse())->setStatusCode(Response::HTTP_NOT_FOUND);
        }


        // Finde alle Noten des Faches.
        $notes = $fach->getFaecherNote();


        // Berechne den Notenschnitt.
        $count = count($notes);
        $sum = 0;
        foreach ($notes as $note) {
            $sum += $note->getNote();
        }
        $average = $count > 0 ? $sum / $count : 0;


        $this->logger->info("notenschnitt vom Fach {fach} wurde ausgegeben", ["fach" => $fach->getFach()]);

        // Gebe den Notenschnitt als JSON zurück.
        return (new JsonResponse())->setContent(
            $this->serializer->serialize(
                ['average' => $average], "json"
            )
        );
    }



    private function validateDto($dtoFach, $groups = ["create"]) {

        $errors = $this->validator->validate($dtoFach, groups: ["create"]);

        if ($errors->count() > 0){
            $errorStringArray = [];
            foreach ($errors as $error){
                $errorStringArray[] = $error->getMessage();
            }

            $this->logger->info("es wurde validiert");

            return $this->json($errorStringArray, 400);
        }


    }


}

