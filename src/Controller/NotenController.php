<?php

namespace App\Controller;

use App\DTO\CreateUpdateFaecher;
use App\DTO\CreateUpdateNote;
use App\DTO\FilterFaecher;
use App\DTO\Mapper\ShowNoteMapper;
use App\DTO\ShowFaecher;
use App\Entity\Note;
use App\Repository\FaecherRepository;
use App\Repository\NoteRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes\Delete;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\Items;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Post;
use OpenApi\Attributes\Put;
use OpenApi\Attributes\RequestBody;
use OpenApi\Attributes\Response;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\DTO\FilterNote;
use Symfony\Component\Validator\Validator\ValidatorInterface;


#[Route("/api", name: "api_")]
class NotenController extends AbstractFOSRestController
{

    public function __construct(private SerializerInterface $serializer, private NoteRepository $noteRepository, private FaecherRepository $faecherRepository, private ShowNoteMapper $mapper, private ValidatorInterface $validator, private LoggerInterface $logger){

    }


    /**
     * gibt alle noten mit deren fächer aus
     * @param Request $request
     * @param NoteRepository $noteRepository
     * @return JsonResponse ausgabe der fächer mir deren noten
     */
    #[Get(requestBody: new RequestBody(
        content: new JsonContent(
            ref: new Model(
                type: FilterFaecher::class
            )
        )
    ))]
    #[Response(
        response: 200,
        description: "gibt alle noten mit deren fächer aus",
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
    #[Rest\Get('/noten', name: 'app_noten')]
    public function index(Request $request, NoteRepository $noteRepository): JsonResponse
    {

        // findet alle noten vom notenRepository
        $noten = $noteRepository->findAll();


        $this->logger->info("noten wurde ausgegeben");


        // Serialisierung der gefundenen noten als JSON-Antwort:
        return (new JsonResponse())->setContent(
            $this->serializer->serialize(
                $this->mapper->mapEntitiesToDTOs($noten), "json")
        );
    }


    /**
     * erstellt neue noten mit angabe vom fach fremdschlüssel
     * @param Request $request
     * @return JsonResponse bestätigung oder fehler erstellt
     */
    #[Post(
        requestBody: new RequestBody(
            content: new JsonContent(
                ref: new Model(
                    type: CreateUpdateNote::class,
                    groups: ["create"]
                )
            )
        )

    )]
    #[Rest\Post('/noten', name: 'app_noten_post')]
    public function create(Request $request) : JsonResponse {


        // Deserialisierung des Request Body in DTO-Objekt:
        $dto = $this->serializer->deserialize($request->getContent(), CreateUpdateNote::class, "json");

        // Validierung des DTO-Objekts:
        $errorResponse = $this->validateDto($dto, ["create"]);


        if ($errorResponse) {
            return $errorResponse;
        }

        // fach im faecherRepository finden
        $fach = $this->faecherRepository->find($dto->fach);


        // erstellung einer neuen entity
        $entity = new Note();
        $entity->setNoteFach($fach);
        $entity->setNote($dto->note);

        // speicherung der entity in der datenbank
        $this->noteRepository->save($entity, true);

        $this->logger->info("note {note} wurde erstellt", ["note" => $dto->note]);


        // mit dem serializer entity zu json respons serialisieren und ausgeben
        return (new JsonResponse())->setContent(
            $this->serializer->serialize(
                $this->mapper->mapEntityToDTO($entity), "json")
        );
    }


    /**
     * löscht die ausgewählte note
     * @param Request $request
     * @param $id
     * @return JsonResponse bestätigung oder fehler ob gelöscht
     */
    #[Response(
        response: 200,
        description: "löscht die ausgewählte note",
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
    #[Rest\Delete('/noten/{id}', name: 'app_noten_delete')]
    public function delete(Request $request, $id) : JsonResponse
    {
        // note mit der gegebenen id suchen
        $entitynote = $this->noteRepository->find($id);
        if(!$entitynote) {
            // gibt 403 fehler zurück wenn die id nicht existiert
            $this->logger->info("note {note} wurde nicht gefunden", ["note" => $entitynote->getId()]);
            return $this->json("Story with ID {$id} does not exist!", status: 403);
        }

        // entity wird von der datenbank gelöscht
        $this->noteRepository->remove($entitynote, true);

        $this->logger->info("note {note} wurde erfolgreich geloescht", ["note" => $entitynote->getId()]);

        // ausgabe einer löschungsbestätigung
        return $this->json("Story with ID " . $id . " Succesfully Deleted");
    }


    /**
     * änderung der ausgewählten note
     * @param Request $request
     * @param $id
     * @return JsonResponse bestätigung oder fehler änderung
     */
    #[Response(
        response: 200,
        description: "ändert die ausgewählte note",
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
    #[Rest\Put('/noten/{id}', name: 'app_noten_put')]
    public function update(Request $request, $id) : JsonResponse
    {
        // deserialize den request body in ein CreateUpdateNote object
        $dto = $this->serializer->deserialize($request->getContent(), CreateUpdateNote::class, "json");

        // faecher entity mir der gegebenen id finden
        $entitynote = $this->noteRepository->find($id);

        if(!$entitynote) {
            // gibt 403 fehler zurück wenn die id nicht existiert
            $this->logger->info("note {note} wurde nicht gefunden", ["fach" => $entitynote->getId()]);
            return $this->json("note with ID " . $id . " does not exist! ", status: 403);
        }

        $entitynote->setNote($dto->note);


        // Speichere das Entity mit dem neuen Namen.
        $this->noteRepository->save($entitynote, true);

        $this->logger->info("note {note} wurde geändert", ["note" => $entitynote->getFach()]);

        // gibt eine bestätigung zurück
        return $this->json("Note with ID " . $id . " Succesfully Changed");
    }

    private function validateDto($dto, $groups = ["create"]) {

        $errors = $this->validator->validate($dto, groups: $groups);

        if ($errors->count() > 0){
            $errorStringArray = [];
            foreach ($errors as $error){
                $errorStringArray[] = $error->getMessage();
            }

            return $this->json($errorStringArray, 400);
        }


    }



}
