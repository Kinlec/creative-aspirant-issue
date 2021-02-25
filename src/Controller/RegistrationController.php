<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserRegisterForm;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Response;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Component\Form\FormFactoryBuilder;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\PasswordHasher\Hasher\MessageDigestPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class RegistrationController
{
    /**
     * RegistrationController constructor.
     * @param Environment $twig
     * @param EntityManagerInterface $em
     */
    public function __construct(
        private Environment $twig,
        private EntityManagerInterface $em
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
    public function index(ResponseInterface $response): RedirectResponse | ResponseInterface
    {
        $user = new User();

        $defaultEncoder = new MessageDigestPasswordHasher('sha512', true, 5000);
        $encoders = [User::class => $defaultEncoder];
        $encoderFactory = new PasswordHasherFactory($encoders);
        $passwordEncoder = $encoderFactory->getPasswordHasher($user);

        $formFactoryBuilder = new FormFactoryBuilder();
        $formFactory = $formFactoryBuilder->getFormFactory();

        $form = $formFactory->create(UserRegisterForm::class, $user);

        $this->twig->addExtension(new FormExtension());

        $form->handleRequest();

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($passwordEncoder->hash($user->getPassword()));
            $user->setRoles(['ROLE_USER']);
            $this->em->persist($user);
            $this->em->flush();

            return new Response(200, null, null);
        }

        $data = $this->twig->render('registration.html.twig', [
            'form' => $form->createView(),
        ]);

        $response->getBody()->write($data);

        return $response;
    }
}
