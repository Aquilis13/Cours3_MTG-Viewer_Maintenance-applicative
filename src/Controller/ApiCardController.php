<?php

namespace App\Controller;

use App\Entity\Artist;
use App\Entity\Card;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes as OA;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/card', name: 'api_card_')]
#[OA\Tag(name: 'Card', description: 'Routes for all about cards')]
class ApiCardController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerInterface $logger
    ) {
    }
    #[Route('/all', name: 'List all cards', methods: ['GET'])]
    #[OA\Put(description: 'Return all cards in the database')]
    #[OA\Response(response: 200, description: 'List all cards')]
    public function cardAll(): Response
    {
        // récupère toutes les cartes
        // $cards = $this->entityManager->getRepository(Card::class)->findAll();

        // Récupère uniquement les 20 premières cartes 
        $maxResult = 20;
        $offset = 0;

        $cardRepository = $this->entityManager->getRepository(Card::class);
        $cards = $cardRepository->findBy([], null, $maxResult, $offset);

        return $this->json($cards);
    }

    #[Route('/{uuid}', name: 'Show card', methods: ['GET'])]
    #[OA\Parameter(name: 'uuid', description: 'UUID of the card', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Put(description: 'Get a card by UUID')]
    #[OA\Response(response: 200, description: 'Show card')]
    #[OA\Response(response: 404, description: 'Card not found')]
    public function cardShow(string $uuid): Response
    {
        $card = $this->entityManager->getRepository(Card::class)->findOneBy(['uuid' => $uuid]);
        if (!$card) {
            return $this->json(['error' => 'Card not found'], 404);
        }
        return $this->json($card);
    }

    #[Route('/search/{search}', name: 'List card with search', methods: ['GET'])]
    #[OA\Parameter(name: 'search', description: 'search of the card on name or uuid', in: 'path', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Put(description: 'Get a card by search')]
    #[OA\Response(response: 200, description: 'Show card')]
    #[OA\Response(response: 404, description: 'Card not found')]
    public function cardSearch($search): Response
    {
        // Récupère uniquement les 20 premières cartes 
        $maxResult = 20;

        $cardRepository = $this->entityManager->getRepository(Card::class);
        $query = $cardRepository->createQueryBuilder('c')
            ->where('c.name LIKE :searchName')
            ->orWhere('c.uuid = :searchUuid')
            ->setParameter('searchName', '%' . $search . '%')
            ->setParameter('searchUuid', $search)
            ->setMaxResults($maxResult)
            ->getQuery();

        $cards = $query->getResult();

        return $this->json($cards);
    }
}
