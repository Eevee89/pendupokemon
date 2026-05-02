<?php

namespace App\Controller;

use App\Service\DiscordBotManager;
use Discord\Interaction;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class DiscordBotController extends AbstractController
{
    #[Route('/discord/interactions', name: 'discord_interactions', methods: ['POST'])]
    public function handle(
        Request $request, 
        HttpClientInterface $httpClient,
        string $discordPublicKey
    ): JsonResponse {
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
            $response = new JsonResponse(['type' => 5]);
            $response->send();
            if (function_exists('fastcgi_finish_request')) {
                fastcgi_finish_request();
            }

            $token = $data['token'];
            $appId = $data['application_id'];
            $command = $data['data']['name'];
            $discordId = $data['member']['user']['id'] ?? $data['user']['id'];

            set_time_limit(60);
            ini_set('memory_limit', '256M');

            $botManager = $this->container->get(DiscordBotManager::class);
            $content = match ($command) {
                'pendu' => $botManager->handleStartGame($discordId),
                'deviner' => $botManager->handleGuess($discordId, $data['data']['options'][0]['value']),
                default => ['content' => "Commande inconnue."]
            };

            $url = "https://discord.com/api/v10/webhooks/{$appId}/{$token}/messages/@original";
            $httpClient->request('PATCH', $url, [
                'json' => $content
            ]);
        }

        return new JsonResponse();
    }
}
