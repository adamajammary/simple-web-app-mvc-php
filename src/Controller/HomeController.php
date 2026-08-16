<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Home Controller
 */
class HomeController extends AbstractController
{
    #[Route('/about', name: 'about')]
    public function about(): Response
    {
        return $this->render(
            'home/about.html.twig',
            [
            	'about_copyright' => '2018 Adam A. Jammary',
                'about_url'       => 'https://www.jammary.com/',
                'about_version'   => 'Version 1.0.4'
            ]
        );
    }

    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render(
            'home/index.html.twig',
	    [
                'message_short' => 'Welcome to my simple web app',
                'message_long'  => 'This simple web app is made using PHP 8, Symfony 7, Twig and Doctrine ORM 3.'
            ]
        );
    }
}
