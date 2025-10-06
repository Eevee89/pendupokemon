<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Service\UserManager;
use App\Service\PokemonManager;

class AppController extends AbstractController
{
    public function __construct(
        private UserManager $userManager, 
        private PokemonManager $pokemonManager
    ) { }


    #[Route('/', name: 'app_main')]
    public function home(SessionInterface $session)
    {
        $user = $this->getUser();
        $pokemon = $this->pokemonManager->getRandomPokemon();

        if (null === $pokemon) {
            return new JsonResponse(["Error while randomizing pokemon"], 500);
        }

        $session->set("pokemon", $pokemon);
        $nb = strlen($pokemon->getName());

        $params = [
            "connected" => false,
            "username" => "",
            "letters" => $nb
        ];
        if ($user) {
            $params = [
                "connected" => true,
                "username" => $user->getUsername(),
                "letters" => $nb
            ];
        }

        $guessed = "";
        for($i = 0; $i < $nb; $i++) {
            $guessed .= "_";
        }

        $session->set("guessed", $guessed);
        $session->set("hint", false);
        $session->set("errors", 0);
        $session->set("score", 0);

        return $this->render("game.html.twig", $params);
    }


    #[Route('/scores', name: 'app_scores')]
    public function scores(): JsonResponse
    {
        $scores = $this->userManager->findAll();

        return new JsonResponse([
            "data" => $scores
        ]);
    }

    #[Route('/hint', name: 'app_hint')]
    public function hint(SessionInterface $session): JsonResponse
    {
        if ($session->get("hint")) {
            return new JsonResponse(["message" => "Hint already used"], 400);
        }
        
        $pokemon = $session->get("pokemon");
        if (null === $pokemon) {
            return new JsonResponse(["No pokemon in game"], 500);
        }

        $hints = [
            "type1" => "append" . $pokemon->getType1() . "Type"
        ];

        if (null !== $pokemon->getType2()) {
            $hints["type2"] = "append" . $pokemon->getType2() . "Type";
        }

        $session->set("hint", true);

        return new JsonResponse($hints);
    }

    #[Route('/guess/{char}', name: 'app_guess')]
    public function guess(string $char, SessionInterface $session): JsonResponse
    {
        $guessed = $session->get("guessed");
        if (null === $guessed) {
            return new JsonResponse(["message" => ""], 500);
        }
        
        $pokemon = $session->get("pokemon");
        if (null === $pokemon) {
            return new JsonResponse(["No pokemon in game"], 500);
        }

        $name = strtoupper($pokemon->getName());

        if (false === strpos($name, $char)) {
            $errors = $session->get("errors") + 1;
            $session->set("errors", $errors);
            return new JsonResponse([
                "errors" => $errors,
                "answer" => $errors === 10 ? $name : "", 
                "pokedex" => $errors === 10 ? $pokemon->getPokedex() : 0
            ], 400);
        }

        $newguess = "";
        for ($i = 0; $i < strlen($guessed); $i++) {
            if ($guessed[$i] !== "_" || $name[$i] === $char) {
                $newguess .= $name[$i];
            } else {
                $newguess .= "_";
            }
        }

        $session->set("guessed", $newguess);
        $hint = $session->get("hint") ? 1 : 0;

        if (false === strpos($newguess, "_")) {
            $session->set("score", $session->get('score') + max(10 - $session->get("errors") - $hint, 0));

            $user = $this->getUser();
            if (null !== $user) {
                $this->userManager->updateScore($user, $session->get('score'));
            }
        }

        return new JsonResponse([
            "guess" => $newguess,
            "pokedex" => $pokemon->getPokedex(),
            "score" => $session->get('score')
        ]);
    }

    #[Route('/replay', name: 'app_replay')]
    public function replay(Request $request, SessionInterface $session)
    {
        $generations = $request->query->get("generations");
        $pokemon = $this->pokemonManager->getRandomPokemon($generations);

        if (null === $pokemon) {
            return new JsonResponse(["Error while randomizing pokemon"], 500);
        }

        $session->set("pokemon", $pokemon);
        $nb = strlen($pokemon->getName());

        $guessed = "";
        for($i = 0; $i < $nb; $i++) {
            $guessed .= "_";
        }

        $session->set("guessed", $guessed);
        $session->set("hint", false);
        $session->set("errors", 0);

        return new JsonResponse(["letters" => $nb]);
    }

}