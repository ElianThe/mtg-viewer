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
        $cards = $this->entityManager->getRepository(Card::class)->findAll();
        $this->logger->info('Une nouvelle requête sur la route /all a été éxécuté');
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
        $this->logger->info('Une nouvelle requête sur la route /{uuid} a été éxécuté');
        if (!$card) {
            return $this->json(['error' => 'Card not found'], 404);
        }
        return $this->json($card);
    }

    #[Route('/search/{name}', name:'Search card', methods: ['GET'])]
    #[OA\Parameter(name: 'name', description: 'name of the card', in: 'path', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Put(description: 'Get cards by UUID')]
    #[OA\Response(response: 200, description: 'Show card')]
    #[OA\Response(response: 404, description: 'Card not found')]
    public function cardBySearch(string $name) : Response
    {
        if (strlen($name) < 3) {
            return $this->json(['error' => "Le mot n'est pas assez long"], 404);
        }
        $card = $this->entityManager->getRepository(Card::class)->getNameBySearch($name);
        $this->logger->info('Une nouvelle requête sur la route /search/{uuid} a été éxécuté');
        if (!$card) {
            return $this->json(['error' => 'Card not found'], 404);
        }
        return $this->json($card);
    }

    /**
     * @Route("/api/cards", methods={"GET"})
     * @OA\Get(
     *     path="/api/cards",
     *     summary="List all cards with optional setCode filter",
     *     @OA\Parameter(
     *         name="setCode",
     *         in="query",
     *         description="The setCode to filter cards by",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Returns a list of cards",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Card"))
     *     )
     * )
     */
    public function listCards(Request $request): \Symfony\Component\HttpFoundation\JsonResponse
    {
        $setCode = $request->query->get('setCode');

        $query = $this->getDoctrine()->getRepository(Card::class)->createQueryBuilder('c');

        if ($setCode) {
            $query->where('c.setCode = :setCode')->setParameter('setCode', $setCode);
        }

        $cards = $query->getQuery()->getResult();

        return $this->json($cards);
    }

    /**
     * @Route("/api/set-codes", methods={"GET"})
     * @OA\Get(
     *     path="/api/set-codes",
     *     summary="List all available setCodes",
     *     @OA\Response(
     *         response=200,
     *         description="Returns a list of setCodes",
     *         @OA\JsonContent(type="array", @OA\Items(type="string"))
     *     )
     * )
     */
    public function listSetCodes(): JsonResponse
    {
        $setCodes = $this->getDoctrine()->getRepository(Card::class)->createQueryBuilder('c')
            ->select('DISTINCT c.setCode')
            ->getQuery()
            ->getResult();

        return $this->json($setCodes);
    }
}
