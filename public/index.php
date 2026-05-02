<?php

use App\Kernel;
use Discord\Interaction;

//#region --- INTERCEPT DISCORD PING ---
if (isset($_SERVER['HTTP_X_SIGNATURE_ED25519'])) {
    if (file_exists(dirname(__DIR__) . '/.env.local')) {
        (new \Symfony\Component\Dotenv\Dotenv())->bootEnv(dirname(__DIR__) . '/.env.local');
    }

    $discordPublicKey = $_ENV['DISCORD_PUBLIC_KEY'] ?? '';
    $signature = $_SERVER['HTTP_X_SIGNATURE_ED25519'] ?? '';
    $timestamp = $_SERVER['HTTP_X_SIGNATURE_TIMESTAMP'] ?? '';
    $body = file_get_contents('php://input');

    require_once dirname(__DIR__) . '/vendor/autoload.php';

    if (!Interaction::verifyKey($body, $signature, $timestamp, $discordPublicKey)) {
        header('HTTP/1.1 401 Unauthorized');
        exit('Invalid signature');
    }

    $data = json_decode($body, true);

    if ($data && isset($data['type']) && $data['type'] === 1) {
        header('Content-Type: application/json');
        echo json_encode(['type' => 1]);
        exit;
    }
}
//#endregion --- INTERCEPT DISCORD PING ---

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
