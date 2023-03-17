<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController
{
    public function index()
    {
        return $this->render('index.html.twig', [
            'categories' => $this->categories,
        ]);
    }

    public function show()
    {
        return $this->render('show.html.twig', [
            'categories' => $this->categories,
        ]);
    }
}
