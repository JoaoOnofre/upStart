<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Form\ChatFormType;
use App\Entity\Message;
use Symfony\Component\HttpFoundation\Request;

class ChatController extends Controller
{
    public function showChat(Request $request)
    {
        $manager = $this->getDoctrine()->getManager();
        $message = new Message();
        $chatForm = $this->createForm(ChatFormType::class, $message);

        $chatForm->handleRequest($request);
        if ($chatForm->isSubmitted() && $chatForm->isValid()){
            $message->setUser($this->getUser());
            $manager->persist($message);
            $manager->flush();
            return $this->redirectToRoute('Chat');
        }
        
        return $this->render(
            'Default/Chat.html.twig',
            [
                'messages' => $manager->getRepository(Message::class)->findAll(),
                'chatForm' => $chatForm->createView()
            ]
            );
    }
}