<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\Movie;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Interfaces\RouteCollectorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class MovieController
{
    public function __construct(
        private RouteCollectorInterface $routeCollector,
        private Environment $twig,
        private EntityManagerInterface $em
    ) {
    }

    /**
     * @Route("/movie/{id}")
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     *
     * @return ResponseInterface
     *
     * @throws HttpBadRequestException
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
            throw new HttpBadRequestException($request, $e->getMessage(), $e);
        }

        $response->getBody()->write($data);

        return $response;
    }
}
