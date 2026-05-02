<?php

namespace App\Controller;

use App\Service\DiscordBotManager;
use Discord\Interaction;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DiscordBotController extends AbstractController
{
    #[Route('/discord/interactions', name: 'discord_interactions', methods: ['POST'])]
    public function handle(Request $request, DiscordBotManager $botManager, string $discordPublicKey): JsonResponse
    {
        $signature = $request->headers->get('X-Signature-Ed25519');
        $timestamp = $request->headers->get('X-Signature-Timestamp');
        $body = $request->getContent();

        if (!Interaction::verifyKey($body, $signature, $timestamp, $discordPublicKey)) {
            return new JsonResponse(['error' => 'Invalid signature'], 401);
        }

        $data = json_decode($body, true);
        if ($data['type'] === 1) {
            return new JsonResponse(['type' => 1]);
        }

        if ($data['type'] === 2) {
            return new JsonResponse([
                'type' => 4,
                'data' => ['content' => "Test de rapidité"]
            ]);

            $command = $data['data']['name'];
            $discordId = $data['member']['user']['id'] ?? $data['user']['id'];

            return new JsonResponse([
                'type' => 4,
                'data' => match ($command) {
                    'pendu' => $botManager->handleStartGame($discordId),
                    'deviner' => $botManager->handleGuess($discordId, $data['data']['options'][0]['value']),
                    default => ['content' => "Commande inconnue."]
                }
            ]);
        }

        return new JsonResponse(['type' => 1]);
    }
}
