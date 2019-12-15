<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Home Controller
 */
class HomeController extends AbstractController
{
    /**
     * @Route("/home/about", name="home_about")
     */
    public function about()
    {
        return $this->render(
            'home/about.html.twig',
            array(
                'about_copyright' => '2018 Adam A. Jammary',
                'about_url'       => 'https://github.com/adamajammary/simple-web-app-mvc-php',
                'about_version'   => 'Version 1.0.0'
            )
        );
    }

    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        return $this->render(
            'home/index.html.twig',
            array(
                'message_short' => 'Welcome to my simple web app',
                'message_long'  => 'This simple web app is made using PHP7, Symfony 4, Twig 2 and Doctrine ORM.'
            )
        );
    }
}
