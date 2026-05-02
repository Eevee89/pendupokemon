<?php
// public/bot.php

use Discord\Interaction;

require_once __DIR__ . '/../vendor/autoload.php';

$discordPublicKey = $_ENV['DISCORD_PUBLIC_KEY'];

$signature = $_SERVER['HTTP_X_SIGNATURE_ED25519'] ?? '';
$timestamp = $_SERVER['HTTP_X_SIGNATURE_TIMESTAMP'] ?? '';
$body = file_get_contents('php://input');

if (!Interaction::verifyKey($body, $signature, $timestamp, $discordPublicKey)) {
    http_response_code(401);
    exit('Invalid signature');
}

$data = json_decode($body, true);

if ($data['type'] === 1) {
    header('Content-Type: application/json');
    echo json_encode(['type' => 1]);
    exit;
}

require_once __DIR__ . '/../config/bootstrap.php';
$kernel = new \App\Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$kernel->boot();

$container = $kernel->getContainer();
$botManager = $container->get(\App\Service\DiscordBotManager::class);

$discordId = $data['member']['user']['id'] ?? $data['user']['id'];
$command = $data['data']['name'];

$responsePayload = match ($command) {
    'pendu' => $botManager->handleStartGame($discordId),
    'deviner' => $botManager->handleGuess($discordId, $data['data']['options'][0]['value'] ?? ''),
    default => ['content' => "Commande inconnue."]
};

header('Content-Type: application/json');
echo json_encode(['type' => 4, 'data' => $responsePayload]);
