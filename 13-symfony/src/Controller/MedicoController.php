<?php

namespace App\Controller;

use App\Entity\Medico;
use App\Factory\MedicoFactory;
use App\Helper\UrlDataExtractor;
use App\Repository\MedicoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class MedicoController extends BaseController
{
    public function __construct(
        EntityManagerInterface $entityManager, 
        MedicoFactory $factory, 
        MedicoRepository $repository, 
        UrlDataExtractor $extractor, 
        CacheItemPoolInterface $cache,
        LoggerInterface $logger
    ) {
        parent::__construct($repository, $entityManager, $factory, $extractor, $cache, $logger);
    }

    public function indexByEspecialidede(int $especialidadeId): Response
    {
        // verificar se existe a especialidade
        
        $medicos = $this->repository->findBy([
            'especialidade' => $especialidadeId
        ]);

        return new JsonResponse($medicos, Response::HTTP_OK);
    }

    /**
     * @param Medico $currentEntity
     * @param Medico $newEntity
     */
    public function updateCurrentEntity($currentEntity, $newEntity)
    {
        $currentEntity
            ->setNome($newEntity->getNome())
            ->setCrm($newEntity->getCrm())
            ->setEspecialidade($newEntity->getEspecialidade());
    }

    /**
     * @param string $item_id
     */
    public function cacheItemId(string $item_id): string
    {
        return "medico_{$item_id}";
    }
}