<?php

namespace App\Controller;

use App\Factory\EntityFactory;
use App\Factory\ResponseFactory;
use App\Helper\UrlDataExtractor;
use Doctrine\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseController extends AbstractController
{
    /**
     * @var ObjectRepository
     */
    protected $repository;
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;
    /**
     * @var EntityFactory
     */
    protected $factory;
    /**
     * @var UrlDataExtractor
     */
    protected $extractor;
    
    public function __construct(ObjectRepository $repository, EntityManagerInterface $entityManager, EntityFactory $factory, UrlDataExtractor $extractor)
    {
        $this->repository    = $repository;
        $this->entityManager = $entityManager;
        $this->factory       = $factory;
        $this->extractor     = $extractor;
    }
    
    public function index(Request $request): Response
    {
        $currentPage  = $this->extractor->getCurrentPage($request);
        $itensPerPage = $this->extractor->getItensPerPage($request); 
        $offsetSearch = $this->extractor->getOffsetSearch($request); 

        $entityList = $this->repository->findBy(
            $this->extractor->getFilterParams($request), 
            $this->extractor->getSortParams($request), 
            $itensPerPage, 
            $offsetSearch
        );

        $totalItens = count($this->repository->findAll()); // trocar isso

        $response = new ResponseFactory($entityList, $currentPage, $itensPerPage, $totalItens);

        return $response->getResponse();
    }

    public function store(Request $request): Response
    {
        $entity = $this->factory->createEntity($request->getContent());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return new JsonResponse($entity, Response::HTTP_CREATED);
    }
    
    public function show(int $id): Response
    {
        $entity = $this->repository->find($id);
        $codigoRetorno = is_null($entity) ? Response::HTTP_NOT_FOUND : Response::HTTP_OK;
        
        return new JsonResponse($entity, $codigoRetorno);
    }

    public function update(int $id, Request $request): Response
    {
        $entity = $this->repository->find($id);
        if ( is_null($entity) ) {
            return new Response('', Response::HTTP_NOT_FOUND);
        }
        
        $newEntity = $this->factory->createEntity($request->getContent());
        
        $this->updateCurrentEntity($entity, $newEntity);
        $this->entityManager->flush();

        return new JsonResponse($entity, Response::HTTP_OK);
    }

    public function destroy(int $id): Response
    {
        $entity = $this->repository->find($id);
        if ( is_null($entity) ) {
            return new Response('', Response::HTTP_NOT_FOUND);
        }
        
        $this->entityManager->remove($entity);
        $this->entityManager->flush();

        return new JsonResponse('', Response::HTTP_NO_CONTENT);
    }

    abstract public function updateCurrentEntity($currentEntity, $newEntity);
}