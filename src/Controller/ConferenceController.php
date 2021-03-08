<?php

namespace App\Controller;

use Twig\Environment;
use App\Entity\Conference;
use App\Repository\CommentRepository;
use App\Repository\ConferenceRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConferenceController extends AbstractController
{
    /**
     * @var Environment
     */
    private $twig;
    /**
     * @var ConferenceRepository
     */
    private $conferenceRepository;

    public function __construct(Environment $twig, ConferenceRepository $conferenceRepository)
    {
        $this->twig = $twig;
        $this->conferenceRepository = $conferenceRepository;
    }

    /**
     * @Route("/", name="homepage")
     */
    public function index(): Response
    {
        return new Response($this->twig->render('conference/index.html.twig', [
            'conferences' => $this->conferenceRepository->findAll(),
        ]));
    }

    /**
     * @Route("/conference/{id}", name="conference.show")
     */
    public function show(Request $request, Conference $conference, CommentRepository $commentRepository): Response
    {
        $offset = $request->query->getInt('offset', 0);

        return new Response($this->twig->render('conference/show.html.twig', [
            'conference' => $conference,
            'comments' => ($paginator = $commentRepository->getCommentPaginator($conference, $offset)),
            'previous' => $offset - CommentRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $offset + CommentRepository::PAGINATOR_PER_PAGE)
        ]));
    }
}
