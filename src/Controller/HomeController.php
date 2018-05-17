<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Home Controller
 */
class HomeController extends Controller
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
                'about_url'       => 'http://www.jammary.com/',
                'about_version'   => 'Version 1.0.0'
            )
        );
    }

    /**
     * @Route("/home", name="home_index")
     */
    public function index()
    {
        return $this->render(
            'home/index.html.twig',
            array(
                'message_short' => 'Welcome to my simple web app',
                'message_long'  => 'This simple web app is made using PHP7, Symfony 4 and Twig 2.'
            )
        );
    }
}
