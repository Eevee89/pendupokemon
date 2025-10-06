<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Bundle\SecurityBundle\Security;
use App\Service\UserManager;

class SecurityController extends AbstractController
{
    private UserManager $manager;

    public function __construct(UserManager $manager) 
    {
        $this->manager = $manager;
    }

    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, Request $request)
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        
        // Vérifie si la requête a été faite via AJAX
        if ($request->isXmlHttpRequest()) {
            return $this->render('security/modal.html.twig', [
                'last_username' => $lastUsername,
                'error'         => $error,
            ]);
        }

        return $this->render('security/index.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    #[Route('/register', name: 'app_register', methods: ['POST'])]
    public function register(Request $request, Security $security): JsonResponse
    {
        $params = $request->request->all();

        if (!isset($params["username"]) || !isset($params["password"])) {
            return new JsonResponse([
                "message" => "missingParams"
            ], 400);
        }

        if ($this->manager->findByUsername($params["username"])) {
            return new JsonResponse([
                "message" => "usernameTaken"
            ], 400);
        }

        try {
            $user = $this->manager->create($params["username"], $params["password"]);
            $security->login($user);

            return new JsonResponse();
        } catch (\Throwable $e) {
            return new JsonResponse([
                "message" => "serverError"
            ], 500);
        }
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {

    }
}