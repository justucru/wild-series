<?php
// src/Controller/WildController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/wild")
 */

Class WildController extends AbstractController
{
    /**
     * @Route("/", name="wild_index")
     */
    public function index() : Response
    {
        return $this->render('wild/index.html.twig', [
            'website' => 'Wild Series',
        ]);
    }

    /**
     * @Route("/show/{slug}",  name="wild_show", requirements={"slug"="[0-9-a-z]+$"})
     */
    public function show(string $slug = ""): Response
    {
        $title = self::toReadableTitle($slug);

        return $this->render('wild/show.html.twig', ['slug' => $slug, 'title' => $title]);
    }

    public static function toReadableTitle($str) {
        $strex = explode('-', $str);
        for ($i = 0; $i < count($strex); $i++) {
            $strex[$i] = ucfirst($strex[$i]);
        }
        $strim = implode(' ', $strex);
        return $strim;
    }
}