<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventFormType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EventController extends AbstractController
{
    /**
     * @Route("/admin/events", name="admin_events")
     */
    public function eventList(Request $request, EntityManagerInterface $manager, \Swift_Mailer $mailer): Response
    {
        $events = $this->getDoctrine()->getRepository(Event::class)->findAll();

        $event = new Event();
        $form = $this->createForm(EventFormType::class, $event);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $file = $form->get("image")->getData();
            $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();
            $file->move(
                $this->getParameter('uploads'),
                $fileName
            );
            $event->setImage($fileName);
            $manager->persist($event);
            $manager->flush();
            $message = (new \Swift_Message('New Event'))
                ->setFrom('kais.fellah@esprit.tn')
                ->setTo('kais255722@gmail.com')
                ->setBody(
                    $this->renderView(
                    // templates/emails/registration.html.twig
                        'emails/event_email.html.twig'
                    ),
                    'text/html'
                );
            $mailer->send($message);

            return $this->redirectToRoute('admin_events');
        }
        return $this->render('event/liste_event.html.twig', ["form"=>$form->createView(), "events"=>$events]);
    }

    /**
     * @Route("admin/tri-event", name="tri_event")
     */
    public function triEvent(Request $request, EventRepository $manager, \Swift_Mailer $mailer): Response
    {
        $events = $manager->trier();
        $event = new Event();
        $form = $this->createForm(EventFormType::class, $event);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $file = $form->get("image")->getData();
            $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();
            $file->move(
                $this->getParameter('uploads'),
                $fileName
            );
            $event->setImage($fileName);
            $manager->persist($event);
            $manager->flush();
            $message = (new \Swift_Message('New Event'))
                ->setFrom('kais.fellah@esprit.tn')
                ->setTo('kais255722@gmail.com')
                ->setBody(
                    $this->renderView(
                    // templates/emails/registration.html.twig
                        'emails/event_email.html.twig'
                    ),
                    'text/html'
                );
            $mailer->send($message);

            return $this->redirectToRoute('admin_events');
        }
        return $this->render('event/liste_event.html.twig', ["form"=>$form->createView(), "events"=>$events]);
    }

    /**
     * @Route("admin/search", name="search")
     */
    public function search(Request $request, EventRepository $manager, \Swift_Mailer $mailer): Response
    {
        $events = $manager->search($request->request->get('search'));
        $event = new Event();
        $form = $this->createForm(EventFormType::class, $event);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $file = $form->get("image")->getData();
            $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();
            $file->move(
                $this->getParameter('uploads'),
                $fileName
            );
            $event->setImage($fileName);
            $manager->persist($event);
            $manager->flush();
            $message = (new \Swift_Message('New Event'))
                ->setFrom('kais.fellah@esprit.tn')
                ->setTo('kais255722@gmail.com')
                ->setBody(
                    $this->renderView(
                    // templates/emails/registration.html.twig
                        'emails/event_email.html.twig'
                    ),
                    'text/html'
                );
            $mailer->send($message);

            return $this->redirectToRoute('admin_events');
        }
        return $this->render('event/liste_event.html.twig', ["form"=>$form->createView(), "events"=>$events]);
    }

    /**
     * @Route("admin/update-event/{id}", name="update_event")
     */
    public function update(Request $request, EntityManagerInterface $manager, $id): Response
    {
        if ($request->isMethod('post')) {
            // your code
            $event = $this->getDoctrine()->getRepository(Event::class)->find($id);
            $event->setTitre($request->request->get('title'));
            $event->setDescription($request->request->get('description'));
            $event->setLocation($request->request->get('location'));
            $event->setDateDebut(\DateTime::createFromFormat('Y-m-d', $request->request->get('DateDebut')));
            $event->setDateFin(\DateTime::createFromFormat('Y-m-d', $request->request->get('DateFin')));

            $file = $request->files->get('image');
            if (!empty($file)){
                $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();
                $file->move(
                    $this->getParameter('uploads'),
                    $fileName
                );
                $event->setimage($fileName);
            }
            $manager->flush();
            return $this->redirectToRoute('admin_events');
        }

    }

    /**
     * @Route("/admin/delete-event/{id}", name="delete_event")
     */
    public function delete($id, EntityManagerInterface $manager): Response
    {
        $event = $this->getDoctrine()->getRepository(Event::class)->find($id);
        $manager->remove($event);
        $manager->flush();
        return $this->redirectToRoute('admin_events');
    }


    /**
     * @Route("/front/liste-event-front", name="liste_event_front")
     */
    public function listeEventFront(EntityManagerInterface $manager): Response
    {
        $events = $this->getDoctrine()->getRepository(Event::class)->findAll();
        return $this->render('event/liste_event_front.html.twig', ["events"=>$events]);
    }

    /**
     * @Route("/event", name="get_event")
     */
    public function getEvent(): Response
    {
        #$event = $this->getDoctrine()->getRepository(Event::class)->find($id);
        return $this->render('event/event.html.twig');
    }


    /**
 * @return string
 */
private function generateUniqueFileName()
{
    return md5(uniqid());
}
}
