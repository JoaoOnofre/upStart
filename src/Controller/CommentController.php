<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\File;

class CommentController extends AbstractController
{
    /**
     * @Route("/comment", name="comment")
     */
    public function post_detail()
    {
        return $this->render('post/detail.html.twig', [
            'controller_name' => 'CommentController',
        ]);
    }
}
