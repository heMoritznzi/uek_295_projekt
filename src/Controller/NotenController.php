<?php

namespace App\Controller;

use App\DTO\CreateUpdateNote;
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

#[Route("/api", name: "api_")]
class NotenController extends AbstractFOSRestController
{

    public function __construct(private SerializerInterface $serializer, private NoteRepository $noteRepository, private FaecherRepository $faecherRepository, private ShowNoteMapper $mapper){

    }

    #[Rest\Get('/noten', name: 'app_noten')]
    public function index(): JsonResponse
    {
        return $this->json(
            "Get funktioniert"
        );
    }

    #[Rest\Post('/noten', name: 'app_noten_post')]
    public function create(Request $request) : JsonResponse {

        $dto = $this->serializer->deserialize($request->getContent(), CreateUpdateNote::class, "json");

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

    #[Rest\Delete('/noten', name: 'app_noten_delete')]
    public function delete() : JsonResponse
    {
        return $this->json(
            "delete funktioniert"
        );
    }

    #[Rest\Put('/noten', name: 'app_noten_put')]
    public function update() : JsonResponse
    {
        return  $this->json(
            "update funktioniert"
        );
    }


}
