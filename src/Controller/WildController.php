<?php
// src/Controller/WildController.php
namespace App\Controller;

use App\Repository\ProgramRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Program;
use App\Entity\Category;
use App\Entity\Season;
use App\Entity\Episode;
use App\Form\ProgramSearchType;

/**
 * @Route("/wild")
 */

Class WildController extends AbstractController
{
    /**
     * SHow all rows from Program's entity
     *
     * @Route("/", name="wild_index")
     * @param Request $request
     * @return Response A response instance
     */
    public function index(Request $request) : Response
    {
        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findAll();

        if (!$programs) {
            throw $this->createNotFoundException(
                'No program found in program\'s table.'
            );
        }

        $form = $this->createForm(ProgramSearchType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data = $form->getData();
            // TODO: Faire une recherche dans la BDD avec les infos de $data
        }

        return $this->render('wild/index.html.twig', [
             'programs' => $programs,
             'form' => $form->createView()
            ]
        );
    }

    /**
     * Getting the list of programs by category
     *
     * @param string $categoryName
     * @Route("/category/{categoryName}", name="show_category")
     * @return Response
     */
    public function showByCategory(string $categoryName)
    {
        if (!$categoryName) {
            throw $this
                ->createNotFoundException('No category has been sent to find a category in category\'s table.');
        }

        $categoryId = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findBy(['name' => $categoryName]);

        $programList = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findBy(
                ['category' => $categoryId],
                ['id' => 'DESC'],
                3
            );


        if (!$programList) {
            throw $this->createNotFoundException(
                'No program with ' . $categoryName . ' category found in program\'s table .'
            );
        }

        return $this->render('wild/category.html.twig', [
            'categoryName' => $categoryName,
            'programList' => $programList]);
    }

    /**
     * Getting a program with a formatted slug for title
     *
     * @param string $slug The slugger
     * @Route("/program/{slug<^[a-z0-9-]+$>}", defaults={"slug" = null}, name="show_program")
     * @return Response
     */
    public function showByProgram(?string $slug): Response
    {
        if (!$slug) {
            throw $this
                ->createNotFoundException('No slug has been sent to find a program in program\'s table.');
        }
        $slug = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($slug)), "-")
        );
        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findOneBy(['title' => mb_strtolower($slug)]);

        if (!$program) {
            throw $this->createNotFoundException(
                'No program with' . $slug . ' title found in program\'s table .'
            );
        }

        $seasons = $this->getDoctrine()
            ->getRepository(Season::class)
            ->findSeasonsInProgram($program->getId());

        return $this->render('wild/program.html.twig', [
                'slug' => $slug,
                'program' => $program,
                'seasons' => $seasons]
        );
    }

    /**
     * Getting the list of seasons by program
     *
     * @param string $seasonId
     * @Route("/seasons/{seasonId}", name="show_season")
     * @return Response
     */
    public function showBySeason(string $seasonId)
    {
        $season = $this->getDoctrine()
            ->getRepository(Season::class)
            ->findOneBy(['id' => $seasonId]);

        if (!$season) {
            throw $this->createNotFoundException(
                'No season with' . $seasonId . ' id found in season\'s table .'
            );
        }

        $program = $season->getProgram();

        $episodes = $season->getEpisodes();

        return $this->render('wild/season.html.twig', [
                'season' => $season,
                'program' => $program,
                'episodes' => $episodes
        ]);
    }
    /**
     * @Route("/episode/{id}", name="show_episode")
     * @return Response
     */
    public function showByEpisode(Episode $episode)
    {
        $season = $episode->getSeason();
        $program = $season->getProgram();

        return $this->render('wild/episode.html.twig', [
            'episode' => $episode,
            'season' => $season,
            'program' => $program,
        ]);
    }
}