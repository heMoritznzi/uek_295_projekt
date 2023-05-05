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
use OpenApi\Attributes\Get;
use OpenApi\Attributes\Items;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Post;
use OpenApi\Attributes\RequestBody;
use phpDocumentor\Reflection\DocBlock\Tags\Reference\Reference;
use phpDocumentor\Reflection\Types\Context;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;


#[Route("/api", name: "api_")]
class FaecherController extends AbstractController
{

    public function __construct(private SerializerInterface $serializer, private FaecherRepository $faecherRepository, private ShowFaecherMapper $mapper, private ValidatorInterface $validator){

    }

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
        $dtoFilter = $this->serializer->deserialize(
            $request->getContent(),
            FilterFaecher::class,
            "json"
        );

        $fach = $this->faecherRepository->filterAll($dtoFilter) ?? [];

        return (new JsonResponse())->setContent(
            $this->serializer->serialize(
                $this->mapper->mapEntitiesToDTOs($fach), "json")
        );
    }



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

        $dtoFach = $this->serializer->deserialize($request->getContent(), CreateUpdateFaecher::class, "json");

        $errorResponse = $this->validateDto($dtoFach, ["create"]);

        if ($errorResponse) {
            return $errorResponse;
        }

        $entity = new Faecher();
        $entity->setFach($dtoFach->fach);


        $this->faecherRepository->save($entity, true);

        return (new JsonResponse())->setContent(
            $this->serializer->serialize(
                $this->mapper->mapEntityToDTO($entity), "json")
        );
    }

    #[Rest\Delete('/faecher', name: 'app_faecher_delete')]
    public function delete($id) : JsonResponse
    {
        $entityFach = $this->faecherRepository->find($id);
        if(!$entityFach) {
            return $this->json("Story with ID {$id} does not exist!", status: 403);
        }
        $this->faecherRepository->remove($entityFach, true);
        return $this->json("Story with ID " . $id . " Succesfully Deleted");
    }

    #[Rest\Put('/faecher', name: 'app_faecher_put')]
    public function update(Request $request, $id) : JsonResponse
    {
        $dto = $this->serializer->deserialize($request->getContent(), CreateUpdateFaecher::class, "json");
        $entityfach = $this->faecherRepository->find($id);

        if(!$entityfach) {
            return $this->json("Fach with ID " . $id . " does not exist! ", status: 403);
        }

        $entityfach->setFach($dto->fach);


        $this->faecherRepository->save($entityfach, true);
        return $this->json("Fach with ID " . $id . " Succesfully Changed");
    }

    #[Rest\Get('/faecher/{id}/notenschnitt', name: 'app_faecher_notenschnitt')]
    public function averageGradeBySubject(Request $request, FaecherRepository $faecherRepository, ?int $id): JsonResponse
    {
        $fach = $faecherRepository->find($id);

        if (!$fach) {
            return (new JsonResponse())->setStatusCode(Response::HTTP_NOT_FOUND);
        }

        $notes = $fach->getFaecherNote();

        $count = count($notes);
        $sum = 0;
        foreach ($notes as $note) {
            $sum += $note->getNote();
        }
        $average = $count > 0 ? $sum / $count : 0;

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

            return $this->json($errorStringArray, 400);
        }


    }


}

