<?php

namespace App\Service;

use App\Entity\Game;
use App\Repository\GameRepository;
use App\Repository\PokemonRepository;
use Doctrine\ORM\EntityManagerInterface;

class DiscordBotManager
{
    public function __construct(
        private PokemonRepository $pokemonRepo,
        private GameRepository $gameRepo,
        private EntityManagerInterface $em
    ) {}

    /**
     * Logique pour la commande /pendu
     */
    public function handleStartGame(string $discordId): array
    {
        $existingGame = $this->gameRepo->findOneBy(['discordId' => $discordId]);
        if ($existingGame) {
            $this->em->remove($existingGame);
        }

        $pokemon = $this->pokemonRepo->getRandomPokemon();

        if (null === $pokemon) {
            return [
                'content' => "Une erreur est survenue, réessaie plus tard."
            ];
        }

        $game = new Game();
        $game->setDiscordId($discordId);
        $game->setPokemon($pokemon);
        $game->setLetters("");

        $this->em->persist($game);
        $this->em->flush();

        $content = "🎮 **Nouveau Pendu lancé !**\n";
        $content .= "Pokémon de la génération " . $pokemon->getGeneration() . ".\n` ";
        $content .= $this->generateMask($pokemon->getName(), "") . " \n";
        $content .= "`\nUtilise `/deviner [lettre]` !";

        return [
            'content' => $content
        ];
    }

    /**
     * Logique pour la commande /deviner
     */
    public function handleGuess(string $discordId, string $letter): array
    {
        $game = $this->gameRepo->findOneBy(['discordId' => $discordId]);
        if (!$game) {
            return ['content' => "Tu n'as pas de partie en cours. Tape `/pendu` !"];
        }

        $letter = strtoupper($letter);
        $currentLetters = strtoupper($game->getLetters());

        if (str_contains($currentLetters, $letter)) {
            return ['content' => "Tu as déjà proposé la lettre **$letter** !"];
        }

        $newLetters = $currentLetters . $letter;
        $game->setLetters($newLetters);

        $nomPokemon = strtoupper($game->getPokemon()->getName());
        $mask = $this->generateMask($nomPokemon, $newLetters);

        if (!str_contains($mask, '_')) {
            $this->em->remove($game);
            $this->em->flush();
            return ['content' => "✨ GAGNÉ ! C'était bien **$nomPokemon** !"];
        }

        $this->em->flush();
        return [
            'content' => "Lettre : **$letter**\nMot : ` $mask `\nLettres jouées : $newLetters"
        ];
    }

    /**
     * Génère l'affichage avec underscores
     */
    private function generateMask(string $nom, string $letters): string
    {
        $nom = strtoupper($nom);
        $lettersArray = str_split(strtoupper($letters));
        $result = "";

        foreach (str_split($nom) as $char) {
            if (in_array($char, $lettersArray) || $char === '-' || $char === ' ') {
                $result .= $char . " ";
            } else {
                $result .= "_ ";
            }
        }

        return trim($result);
    }
}
