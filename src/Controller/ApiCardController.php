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
use Symfony\Component\HttpFoundation\Request;

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
    #[OA\Parameter(name: 'set_code', description: 'set code of the card', in: 'query', required: false, schema: new OA\Schema(type: 'string'))]
    #[OA\Put(description: 'Return all cards in the database')]
    #[OA\Response(response: 200, description: 'List all cards')]
    public function cardAll(Request $request): Response
    {
        $setCode = $request->query->get('set_code');
        $page = $request->query->get('page');

        $maxResult = 100;
        $offset = $maxResult * $page - 100;

        $cardRepository = $this->entityManager->getRepository(Card::class);

        $nbCards = $cardRepository->countCards($setCode);  
        $cards = $cardRepository->createQueryBuilder('c');

        if ($setCode !== null) {
            $cards->where('c.setCode = :setCode')
                ->setParameter('setCode', $setCode);
        }
        
        $cards = $cards
            ->setMaxResults($maxResult)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult()
        ;

        $response = [
            "nb_result" => $nbCards,
            "data" => $cards
        ];
        return $this->json($response);
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
    #[OA\Parameter(name: 'set_code', description: 'set code of the card', in: 'query', required: false, schema: new OA\Schema(type: 'string'))]
    #[OA\Put(description: 'Get a card by search')]
    #[OA\Response(response: 200, description: 'Show card')]
    #[OA\Response(response: 404, description: 'Card not found')]
    public function cardSearch(Request $request, $search): Response
    {
        $setCode = $request->query->get('set_code');

        // Récupère uniquement les 20 premières cartes 
        $maxResult = 20;

        $cardRepository = $this->entityManager->getRepository(Card::class);
        $query = $cardRepository->createQueryBuilder('c')
            ->where('c.name LIKE :searchName')
            ->orWhere('c.uuid = :searchUuid')
            ->setParameter('searchName', '%' . $search . '%')
            ->setParameter('searchUuid', $search)
            ->setMaxResults($maxResult);

        if ($setCode !== null) {
            $query->andWhere('c.setCode = :setCode')
                ->setParameter('setCode', $setCode);
        }
        $query = $query->getQuery();

        $cards = $query->getResult();

        return $this->json($cards);
    }

    #[Route('/setcode/all', name: 'List all setCode for cards', methods: ['GET'])]
    #[OA\Put(description: 'Get all setCode')]
    #[OA\Response(response: 200, description: 'Show setCode')]
    public function allSetCode(): Response
    {
        $cardRepository = $this->entityManager->getRepository(Card::class);
        $query = $cardRepository->findAllSetCodes();

        return $this->json($query);
    }
}
