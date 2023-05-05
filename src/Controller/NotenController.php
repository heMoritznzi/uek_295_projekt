<?php

namespace App\Controller;

use App\DTO\CreateUpdateNote;
use App\DTO\FilterFaecher;
use App\DTO\Mapper\ShowNoteMapper;
use App\Entity\Note;
use App\Repository\FaecherRepository;
use App\Repository\NoteRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\DTO\FilterNote;
use Symfony\Component\Validator\Validator\ValidatorInterface;


#[Route("/api", name: "api_")]
class NotenController extends AbstractFOSRestController
{

    public function __construct(private SerializerInterface $serializer, private NoteRepository $noteRepository, private FaecherRepository $faecherRepository, private ShowNoteMapper $mapper, private ValidatorInterface $validator){

    }

    #[Rest\Get('/noten', name: 'app_noten')]
    public function index(Request $request, NoteRepository $noteRepository): JsonResponse
    {
        $noten = $noteRepository->findAll();

        return (new JsonResponse())->setContent(
            $this->serializer->serialize(
                $this->mapper->mapEntitiesToDTOs($noten), "json")
        );
    }



    #[Rest\Post('/noten', name: 'app_noten_post')]
    public function create(Request $request) : JsonResponse {

        $dto = $this->serializer->deserialize($request->getContent(), CreateUpdateNote::class, "json");


        $errorResponse = $this->validateDto($dto, ["create"]);

        if ($errorResponse) {
            return $errorResponse;
        }


        $fach = $this->faecherRepository->find($dto->fach);

        $entity = new Note();
        $entity->setNoteFach($fach);
        $entity->setNote($dto->note);


        $this->noteRepository->save($entity, true);

        return (new JsonResponse())->setContent(
            $this->serializer->serialize(
                $this->mapper->mapEntityToDTO($entity), "json")
        );
    }

    #[Rest\Delete('/noten/{id}', name: 'app_noten_delete')]
    public function delete(Request $request, $id) : JsonResponse
    {
        $entitynote = $this->noteRepository->find($id);
        if(!$entitynote) {
            return $this->json("Story with ID {$id} does not exist!", status: 403);
        }
        $this->noteRepository->remove($entitynote, true);
        return $this->json("Story with ID " . $id . " Succesfully Deleted");
    }

    #[Rest\Put('/noten/{id}', name: 'app_noten_put')]
    public function update(Request $request, $id) : JsonResponse
    {
        $dto = $this->serializer->deserialize($request->getContent(), CreateUpdateNote::class, "json");
        $entitynote = $this->noteRepository->find($id);

        if(!$entitynote) {
            return $this->json("note with ID " . $id . " does not exist! ", status: 403);
        }

        $entitynote->setNote($dto->note);


        $this->noteRepository->save($entitynote, true);
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
