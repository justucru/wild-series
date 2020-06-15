<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Season;
use App\Form\SeasonType;
use App\Repository\SeasonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/comment")
 */
class CommentController extends AbstractController
{

    /**
     * @Route("/{id}", name="comment_delete")
     * @IsGranted("ROLE_SUBSCRIBER")
     */
    public function delete(Request $request, Comment $comment): Response
    {
        $episode = $comment->getEpisode();

        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($comment);
            $entityManager->flush();
        }

        return $this->render('episode/show.html.twig', [
            'episode' => $episode,]);
    }
}
