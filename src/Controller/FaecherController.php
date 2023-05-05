<?php

namespace App\Controller;

use App\DTO\CreateUpdateFaecher;
use App\DTO\FilterFaecher;
use App\DTO\Mapper\ShowFaecherMapper;
use App\Entity\Faecher;
use App\Repository\FaecherRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;


#[Route("/api", name: "api_")]
class FaecherController extends AbstractController
{

    public function __construct(private SerializerInterface $serializer, private FaecherRepository $faecherRepository, private ShowFaecherMapper $mapper, private ValidatorInterface $validator){

    }

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
    public function delete() : JsonResponse
    {
        return $this->json(
            "delete funktioniert"
        );
    }

    #[Rest\Put('/faecher', name: 'app_faecher_put')]
    public function update() : JsonResponse
    {
        return  $this->json(
            "update funktioniert"
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

