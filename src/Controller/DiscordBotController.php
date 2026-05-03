<?php

namespace App\Controller;

use App\Service\DiscordBotManager;
use Discord\Interaction;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/discord', name: 'discord_', methods: ['POST'])]
class DiscordBotController extends AbstractController
{
    #[Route('/bot', name: 'bot', methods: ['GET'])]
    public function install(string $discordApplicationId): RedirectResponse 
    {
        return $this->redirect("https://discord.com/oauth2/authorize?client_id=$discordApplicationId");
    }

    #[Route('/interactions', name: 'interactions', methods: ['POST'])]
    public function handle(
        Request $request, 
        HttpClientInterface $httpClient,
        string $discordPublicKey
    ): JsonResponse {
        $signature = $request->headers->get('X-Signature-Ed25519');
        $timestamp = $request->headers->get('X-Signature-Timestamp');
        $body = $request->getContent();

        if (empty($signature) || empty($timestamp) || empty($body)) {
            return new JsonResponse(['error' => 'Invalid signature'], 401);
        }

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

            $botManager = new DiscordBotManager();

            $content = match ($command) {
                'pendu' => $botManager->handleStartGame($discordId),
                'deviner' => $botManager->handleGuess($discordId, $data['data']['options'][0]['value'] ?? ''),
                default => ['content' => "Commande inconnue."]
            };

            $url = "https://discord.com/api/v10/webhooks/{$appId}/{$token}/messages/@original";
            $httpClient->request('PATCH', $url, ['json' => $content]);
            exit;
        }

        return new JsonResponse();
    }
}
