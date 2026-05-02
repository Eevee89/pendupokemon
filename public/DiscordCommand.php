<?php

// --- CONFIGURATION ---
$botToken = 'TON_BOT_TOKEN';
$applicationId = 'TON_APPLICATION_ID';
$guildId = null; // Laisse null pour des commandes globales (peuvent mettre 1h à apparaître)
// OU mets l'ID de ton serveur pour une apparition INSTANTANÉE.

// --- DÉFINITION DES COMMANDES ---
$commands = [
    [
        "name" => "pendu",
        "description" => "Démarrer une nouvelle partie de pendu Pokémon"
    ],
    [
        "name" => "deviner",
        "description" => "Proposer une lettre pour le pendu en cours",
        "options" => [
            [
                "name" => "lettre",
                "description" => "La lettre à tester",
                "type" => 3, // 3 = STRING
                "required" => true,
                "max_length" => 1
            ]
        ]
    ]
];

// --- APPEL API ---
$url = $guildId
    ? "https://discord.com/api/v10/applications/$applicationId/guilds/$guildId/commands"
    : "https://discord.com/api/v10/applications/$applicationId/commands";

foreach ($commands as $command) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bot $botToken",
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($command));

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    echo "Commande '" . $command['name'] . "' : HTTP $httpCode - $response <br>";
}

echo "<br><b>Opération terminée. SUPPRIME CE FICHIER DE TON SERVEUR MAINTENANT !</b>";
