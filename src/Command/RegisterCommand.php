<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:discord:register-commands',
    description: 'Enregistre les slash commandes auprès de Discord',
)]
class RegisterCommand extends Command
{
    public function __construct(
        private HttpClientInterface $client, 
        private string $discordApplicationId, 
        private string $discordBotToken
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if (!$this->discordApplicationId || !$this->discordBotToken) {
            $io->error('Les variables DISCORD_APPLICATION_ID ou DISCORD_BOT_TOKEN sont manquantes dans le .env');
            return Command::FAILURE;
        }

        $url = "https://discord.com/api/v10/applications/{$this->discordApplicationId}/commands";

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
                        "type" => 3, // STRING
                        "required" => true,
                        "max_length" => 1
                    ]
                ]
            ]
        ];

        $io->info("Enregistrement des commandes auprès de Discord...");

        try {
            $response = $this->client->request('PUT', $url, [
                'headers' => [
                    'Authorization' => "Bot {$this->discordBotToken}",
                    'Content-Type' => 'application/json',
                ],
                'json' => $commands
            ]);

            $statusCode = $response->getStatusCode();

            if ($statusCode >= 200 && $statusCode < 300) {
                $io->success("Les commandes ont été enregistrées avec succès (HTTP $statusCode) !");
                return Command::SUCCESS;
            } else {
                $io->error("Erreur lors de l'enregistrement : " . $response->getContent(false));
                return Command::FAILURE;
            }
        } catch (\Exception $e) {
            $io->error("Erreur : " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
