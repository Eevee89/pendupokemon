<?php

namespace App\Service;

class DiscordBotManager
{
    private \PDO $pdo;

    public function __construct()
    {
        $config = parse_url($_ENV['DATABASE_URL'] ?? '');
        $user = isset($config['user']) ? rawurldecode($config['user']) : '';
        $password = isset($config['pass']) ? rawurldecode($config['pass']) : '';
        $host = $config['host'];
        $port = $config['port'] ?? 3306;
        $dbName = isset($config['path']) ? ltrim($config['path'], '/') : '';

        $dsn = sprintf("mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4", $host, $port, $dbName);

        $this->pdo = new \PDO($dsn, $user, $password, [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        ]);
    }

    public function handleStartGame(string $discordId): array
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM hanging_game WHERE discord_id = ?");
            $stmt->execute([$discordId]);

            $stmt = $this->pdo->query("SELECT id, name, generation FROM pokemon ORDER BY RAND() LIMIT 1");
            $pokemon = $stmt->fetch();

            if (!$pokemon) {
                return ['content' => "Erreur : aucun Pokémon trouvé dans la base."];
            }

            $stmt = $this->pdo->prepare("INSERT INTO hanging_game (discord_id, pokemon_id, letters) VALUES (?, ?, ?)");
            $stmt->execute([$discordId, $pokemon['id'], ""]);

            $content = "🎮 **Nouveau Pendu lancé !**\n";
            $content .= "Pokémon de la génération " . $pokemon['generation'] . ".\n` ";
            $content .= $this->generateMask($pokemon['name'], "") . " \n";
            $content .= "`\nUtilise `/deviner [lettre]` !";

            return ['content' => $content];
        } catch (\Throwable $e) {
            return ['content' => "Erreur (Start): " . $e->getMessage()];
        }
    }

    public function handleGuess(string $discordId, string $letter): array
    {
        try {
            $sql = "SELECT g.*, p.name as pokemon_name, p.pokedex
                    FROM hanging_game g 
                    JOIN pokemon p ON g.pokemon_id = p.id 
                    WHERE g.discord_id = ?";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$discordId]);
            $game = $stmt->fetch();

            if (!$game) {
                return ['content' => "Tu n'as pas de partie en cours. Tape `/pendu` !"];
            }

            $letter = strtoupper(trim($letter));
            if (strlen($letter) !== 1) return ['content' => "Envoie une seule lettre !"];

            $currentLetters = strtoupper($game['letters'] ?? '');

            if (str_contains($currentLetters, $letter)) {
                return ['content' => "Tu as déjà proposé la lettre **$letter** !"];
            }

            $newLetters = $currentLetters . $letter;

            $stmt = $this->pdo->prepare("UPDATE hanging_game SET letters = ? WHERE discord_id = ?");
            $stmt->execute([$newLetters, $discordId]);

            $name = strtoupper($game['pokemon_name']);
            $mask = $this->generateMask($name, $newLetters);

            if (!str_contains($mask, '_')) {
                $stmt = $this->pdo->prepare("DELETE FROM hanging_game WHERE discord_id = ?");
                $stmt->execute([$discordId]);
                $url = "https://www.pokebip.com/pokedex-images/300/" . $game['pokedex'] . ".png?v=ev-blueberry";
                return ['content' => "✨ GAGNÉ ! C'était bien **[$name]($url)**"];
            }

            return [
                'content' => "Lettre : **$letter**\nMot : ` $mask `\nLettres jouées : $newLetters"
            ];
        } catch (\Throwable $e) {
            return ['content' => "Erreur (Guess): " . $e->getMessage()];
        }
    }

    private function generateMask(string $name, string $letters): string
    {
        $name = strtoupper($name);
        $lettersArray = str_split(strtoupper($letters));
        $result = "";

        foreach (str_split($name) as $char) {
            if (in_array($char, $lettersArray) || $char === '-' || $char === ' ') {
                $result .= $char . " ";
            } else {
                $result .= "_ ";
            }
        }
        return trim($result);
    }
}
