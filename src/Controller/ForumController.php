<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ForumTopicRepository;
use App\Entity\ForumTopic;
use App\Form\ForumTopicType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;



class ForumController extends AbstractController{

    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'home')]
    public function home(): Response
    {
        return $this->render('forum/home.html.twig');
    }
    #[Route('/forum', name: 'forum_index')]
    public function index(ForumTopicRepository $topicRepository): Response
    {
        $topics = $topicRepository->findAll();
        return $this->render('forum/index.html.twig', [
            'topics' => $topics,
        ]);
    }

#[Route('/forum/new', name: 'forum_new')]
public function new(Request $request): Response
{
    $topic = new ForumTopic();
    $form = $this->createForm(ForumTopicType::class, $topic);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $topic->setAuthor($this->getUser());
        $topic->setCreatedAt(new \DateTimeImmutable());
        $this->entityManager->persist($topic);
        $this->entityManager->flush();

        return $this->redirectToRoute('forum_index');
    }

    return $this->render('forum/new.html.twig', [
        'form' => $form->createView(),
    ]);
}
}