<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserRegisterForm;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class RegistrationController extends AbstractController
{
    private UserPasswordHasherInterface $passwordEncoder;

    public function __construct(
        private Environment $twig,
        private EntityManagerInterface $em,
    ) {
    }

    /**
     * @Route("/registration", name="registration")
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     *
     * @return ResponseInterface|RedirectResponse
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function index(ServerRequestInterface $request, ResponseInterface $response): RedirectResponse | ResponseInterface
    {
        $user = new User();

        $form = $this->createForm(UserRegisterForm::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($this->passwordEncoder->hashPassword($user, $user->getPassword()));
            $user->setRoles(['ROLE_USER']);
            $this->em->persist($user);
            $this->em->flush();

            return $this->redirectToRoute('app_login');
        }

        $data = $this->twig->render('registration.html.twig', [
            'form' => $form->createView(),
        ]);

        $response->getBody()->write($data);

        return $response;
    }
}
