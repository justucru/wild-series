<?php
// src/Controller/WildController.php
namespace App\Controller;

use App\Repository\ProgramRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Program;
use App\Entity\Category;

/**
 * @Route("/wild")
 */

Class WildController extends AbstractController
{
    /**
     * SHow all rows from Program's entity
     *
     * @Route("/", name="wild_index")
     * @return Response A response instance
     */
    public function index() : Response
    {
        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findAll();

        if (!$programs) {
            throw $this->createNotFoundException(
                'No program found in program\'s table.'
            );
        }

        return $this->render(
            'wild/index.html.twig',
            ['programs' => $programs]
        );
    }

    /**
     * Getting a program with a formatted slug for title
     *
     * @param string $slug The slugger
     * @Route("/show/{slug<^[a-z0-9-]+$>}", defaults={"slug" = null}, name="wild_show")
     * @return Response
     */
    public function show(?string $slug): Response
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

        return $this->render('wild/show.html.twig', [
            'slug' => $slug,
            'program' => $program]);
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
    /*
    public static function toReadableTitle($str) {
        $strex = explode('-', $str);
        for ($i = 0; $i < count($strex); $i++) {
            $strex[$i] = ucfirst($strex[$i]);
        }
        $strim = implode(' ', $strex);
        return $strim;
    }
    */
}