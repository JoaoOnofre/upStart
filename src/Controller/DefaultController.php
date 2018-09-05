<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Entity\Document;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Entity\Post;
use App\Form\UserFormType;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Form\ProfileEditFormType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class DefaultController extends Controller
{
    public function homepage()
    {
        return $this->render('default/homepage.html.twig');
    }
    

    public function profile()
    {
        $manager = $this->getDoctrine()->getManager();

        return $this->render(
            'profile/profile.html.twig',
            [
                'user' =>$this->getUser(),
                ]
            );          
    }

    public function profileUser(User $id)
    {
        $manager = $this->getDoctrine()->getManager();

        return $this->render(
            'profile/profile.html.twig',
            [
                'user' =>$id,
                ]
            );          
    }

    public function aboutUs()
    {
        return $this->render('default/about.html.twig'); 
    }

    public function termsOfUse()
    {
        return $this->render('default/terms.html.twig'); 
    }



    public function downloadDocument(Document $document)
    {
        $fileName = sprintf(
            '%s/%s',
            $document->getPath(),
            $document->getName()
        );
        return new BinaryFileResponse($fileName);
    }

     public function about()
    {
        return $this->render('Default/about.html.twig'); 
    }

    public function terms()
    {
        return $this->render('Default/terms.html.twig'); 

    }

    public function privacy()
    {
        return $this->render('Default/privacy.html.twig'); 
    }

    public function tamara()
    {
        return $this->render('members/tamara.html.twig'); 
    }

    public function miro()
    {
        return $this->render('members/miro.html.twig'); 
    }

    public function joao()
    {
        return $this->render('members/joao.html.twig'); 
    }

    public function bahaa()
    {
        return $this->render('members/bahaa.html.twig'); 
    }

    public function ibrahem()
    {
        return $this->render('members/ibrahem.html.twig'); 
    }

    public function contact()
    {
        return $this->render('Default/contact.html.twig'); 
    }

    // Edit Profile /*********************** */
    public function profileEdit(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $manager = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $picture = $user->getPicture();
        if($picture)
        {
            $file = new File($picture->getPath() . '/' . $picture->getName());
            $user->setPicture($file);
        }

        $profileForm = $this->createForm(ProfileEditFormType::class, $user, ['standalone' => true]);
        $profileForm->handleRequest($request);
        
        if ($profileForm->isSubmitted() && $profileForm->isValid()) {
            
            $file = $user->getPicture();
            if($file){
                $document = new Document();
                $document->setPath($this->getParameter('upload_dir'))
                    ->setMimeType($file->getMimeType())
                    ->setName($file->getFilename());
                $file->move($this->getParameter('upload_dir'));
                
                $user->setPicture($document);
                
                $manager->persist($document);

            } else
            {
                $user->setPicture($picture);
            }
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);

            $manager->flush();
            
            return $this->redirectToRoute('profile');
        }

        $user->setPicture($picture);
        
        return $this->render(
            'profile/profileEdit.html.twig',
            [
                'user'=>$user,
                'profileForm' => $profileForm->createView()
            ]
        );
    }
    public function deleteUser(Request $request, User $user, TokenStorageInterface $tokenStorage)
    {
        if(($user == $this->getUser()))
        {
            $manager = $this->getDoctrine()->getManager();
            $manager->remove($user);
            $manager->flush();

            $tokenStorage->setToken(null);

            return $this->redirectToRoute('homepage');
        } 
        else
        {
            $deletionError = true;  
        }
       
    }
 
}