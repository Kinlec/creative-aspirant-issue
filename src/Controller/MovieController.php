<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\Movie;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\RouteCollectorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class MovieController extends AbstractController
{
    public function __construct(
        private RouteCollectorInterface $routeCollector,
        private Environment $twig,
        private EntityManagerInterface $em
    ) {}

    /**
     * @Route("/movie/{id}")
     */
    public function view(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $id = $request->getAttribute('id');

        try {
            $movie = $this->em->getRepository(Movie::class)->find($id);
            $data = $this->twig->render('movie.html.twig', [
                'movie' => $movie,
            ]);
        } catch (\Exception $e) {
            throw $this->createNotFoundException('There are no movies with the following id: ' . $id);
        }

        $response->getBody()->write($data);

        return $response;
    }
}