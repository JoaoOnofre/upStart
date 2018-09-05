<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\PostFormType;
use App\Entity\Post;
use App\Entity\Document;
use App\Form\PostSearchFormType;
use App\DTO\PostSearch;
use App\Entity\Comment;
use App\Form\CommentFormType;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Entity\User;

class PostController extends Controller
{
    /**
     * @Route("/post", name="post")
     */
            // post creation
    public function postList(Request $request)
    {
        $manager = $this->getDoctrine()->getManager();
        $post = new Post();
        $postForm = $this->createForm(
            PostFormType::class,
            $post,
            ['standalone' => true]
        );
        $postForm->handleRequest($request);
        if ($postForm->isSubmitted() && $postForm->isValid()) {
            /**
             * @var UploadFile $file
             * 
             */

            $file = $post->getPicture();
            if ($file) {

                $document = new Document();
                $document->setPath($this->getParameter('upload_dir'))
                    ->setMimeType($file->getMimeType())
                    ->setName($file->getFileName());

                $file->move($this->getParameter('upload_dir'));
                $post->setPicture($document);
                $manager->persist($document);
            }

            $post->setUser($this->getUser());
            $manager->persist($post);
            $manager->flush();


            return $this->redirectToRoute('post');

        }
        
        //this is search function
        $dto = new PostSearch();
        $searchForm = $this->createForm(PostSearchFormType::class, $dto, ['standalone' => true]);

        $searchForm->handleRequest($request);

        return $this->render(
            'post/index.html.twig',
            [
                'posts' => $manager->getRepository(Post::class)->findPaginates($request, $this->get('knp_paginator'), $dto),
                'users' => $manager->getRepository(User::class)->findAll(),
                'postForm' => $postForm->createView(),
                'searchForm' => $searchForm->createView(),
            ]
        );
    }


                // Delete The post
    public function deletePost(Request $request, Post $post)
    {
        $idUser = $this->getUser();
        $deletionError = false;

        if (($idUser == $post->getUser())) {
            $manager = $this->getDoctrine()->getManager();
            $manager->remove($post);
            $manager->flush();
        } else {
            $deletionError = true;
        }
        return $this->redirectToRoute('post');
    }

        // Edit the post 
    public function editPost(Post $post, Request $request)
    {
        if ($post->getPicture()) {
            $file = new File(
                $post->getPicture()->getPath() . '/' . $post->getPicture()->getName()
            );
            $post->setPicture($file);
        }
        $editForm = $this->createForm(PostFormType::class, $post, ['standalone' => true]);
        $editForm->handleRequest($request);

        $editError = false;
        $idUser = $this->getUser();
        /**
         * @var Document $file
         * 
         */

        $file = $post->getPicture();



        if ($editForm->isSubmitted() && $editForm->isValid() && $idUser == $post->getUser()) {

            if ($file) {

                $document = new Document();
                $document->setPath($this->getParameter('upload_dir'))
                    ->setMimeType($file->getMimeType())
                    ->setName($file->getFileName());

                $file->move($this->getParameter('upload_dir'));
                $this->getDoctrine()->getManager()->persist($document);
                $post->setPicture($document);
            }
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('post');

        }

        return $this->render(
            'post/editPost.html.twig',
            [
                'post' => $post,
                'editError' => $editError,
                'editForm' => $editForm->createView()
            ]
        );
    }

//this is just test as comment to push the stuff
    public function commentDetail(post $post, Request $request)
    {
        // for comment 
        $manager = $this->getDoctrine()->getManager();
        $comment = new Comment();
        $commentForm = $this->createForm(
            CommentFormType::class,
            $comment,
            ['standalone' => true]
        );
        $commentForm->handleRequest($request);
        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            /**
             * @var UploadFile $file
             * 
             */

            $file = $comment->getPicture();
            if ($file) {

                $document = new Document();
                $document->setPath($this->getParameter('upload_dir'))
                    ->setMimeType($file->getMimeType())
                    ->setName($file->getFileName());

                $file->move($this->getParameter('upload_dir'));
                $comment->setPicture($document);
                $manager->persist($document);
            }

            $comment->setPost($post);
            $comment->setUser($this->getUser());
            $manager->persist($comment);
            $manager->flush();


            return $this->redirectToRoute('post_detail', array("post" => $post->getId()));


        }

        return $this->render(
            'post/detail.html.twig',
            [
                'post' => $post,
                'commentForm' => $commentForm->createView(),
                'comments' => $post->getComments(),

            ]
        );
    }

    
    // Edit the Comments

    public function editComment(Post $post,  Comment $comment, Request $request)
    {
        $manager = $this->getDoctrine()->getManager();
        if ($comment->getPicture()) {
            $file = new File(
                $comment->getPicture()->getPath() . '/' . $comment->getPicture()->getName()
            );
            $comment->setPicture($file);
        }
        $editForm = $this->createForm(CommentFormType::class, $comment, ['standalone' => true]);
        $editForm->handleRequest($request);

        $editError = false;
        $idUser = $this->getUser();
        /**
         * @var Document $file
         * 
         */

        $file = $comment->getPicture();

        if ($editForm->isSubmitted() && $editForm->isValid() && $idUser == $comment->getUser()) {
            if ($file) {

                $document = new Document();
                $document->setPath($this->getParameter('upload_dir'))
                    ->setMimeType($file->getMimeType())
                    ->setName($file->getFileName());

                $file->move($this->getParameter('upload_dir'));
                $manager->persist($document);
                $comment->setPicture($document);
            }
            $comment->setUser($this->getUser());
            $manager->persist($comment);
            $manager->flush();;

            

            return $this->redirectToRoute('post_detail',array('post'=>$post->getId()));


        }

        return $this->render(
            'post/editComment.html.twig',
            [
                'comment' => $comment,
                'editError' => $editError,
                'edit_form' => $editForm->createView()
            ]
        );
    }
    public function deleteComment(Post $post, Request $request, Comment $comment)
    {
        $idUser = $this->getUser();
        $deletionError = false;

        if (($idUser == $comment->getUser())) {
            $manager = $this->getDoctrine()->getManager();
            $manager->remove($comment);
            $manager->flush();
        } else {
            $deletionError = true;
        }

        return $this->redirectToRoute('post_detail',array('post'=>$post->getId()));
    }
}
    


