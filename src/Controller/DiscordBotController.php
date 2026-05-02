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
        string $discordPublicKey,
        string $databaseUrl
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

            $url = "https://discord.com/api/v10/webhooks/{$appId}/{$token}/messages/@original";

            try {
                $config = parse_url($databaseUrl);

                if ($config === false || !isset($config['host'])) {
                    throw new \Exception("Impossible de parser la DATABASE_URL");
                }

                $user     = isset($config['user']) ? rawurldecode($config['user']) : '';
                $password = isset($config['pass']) ? rawurldecode($config['pass']) : '';
                $host     = $config['host'];
                $port     = $config['port'] ?? 3306;
                $dbName   = isset($config['path']) ? ltrim($config['path'], '/') : '';

                $dsn = sprintf(
                    "mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4",
                    $host,
                    $port,
                    $dbName
                );

                $pdo = new \PDO($dsn, $user, $password, [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                    \PDO::ATTR_TIMEOUT => 2
                ]);

                if ($command === 'pendu') {
                    $stmt = $pdo->query("SELECT name FROM pokemon ORDER BY RAND() LIMIT 1");
                    $pokemon = $stmt->fetch(\PDO::FETCH_ASSOC);

                    $content = ['content' => "Jeu lancé ! Le pokémon est : " . $pokemon['name']];
                } else {
                    $content = ['content' => "Autre commande..."];
                }
            } catch (\Exception $e) {
                $content = ['content' => "Erreur SQL Directe : " . $e->getMessage()];
            }

            $httpClient->request('PATCH', $url, ['json' => $content]);
            exit;
        }

        return new JsonResponse();
    }
}
