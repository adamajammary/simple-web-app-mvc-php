<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    /**
     * @Route("/task/create", name="task_create")
     */
    public function create(Request $request)
    {
        $task = new Task();

        $form = $this->createForm(
            TaskType::class, $task, array('btn_label' => 'Create')
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->saveToDB($form->getData());
            return $this->redirectToRoute('task');
        }

        return $this->render(
            'task/create.html.twig', array('form' => $form->createView())
        );        
    }

    /**
     * @Route("/task/delete/{id}", name="task_delete")
     */
    public function delete($id, Request $request)
    {
        $repository = $this->getDoctrine()->getRepository(Task::class);
        $task       = $repository->find($id);

        $form = $this->createFormBuilder($task)
            ->add('delete', SubmitType::class, array('label' => 'Delete'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->removeFromDB($task);
            return $this->redirectToRoute('task');
        }

        return $this->render(
            'task/delete.html.twig',
            array('form' => $form->createView(), 'task' => $task)
        );        
    }

    /**
     * @Route("/task/details/{id}", name="task_details")
     */
    public function details($id)
    {
        $repository = $this->getDoctrine()->getRepository(Task::class);

        return $this->render(
            'task/details.html.twig', array('task' => $repository->find($id))
        );
    }

    /**
     * @Route("/task/edit/{id}", name="task_edit")
     */
    public function edit($id, Request $request)
    {
        $repository = $this->getDoctrine()->getRepository(Task::class);
        $task       = $repository->find($id);

        $form = $this->createForm(
            TaskType::class, $task, array('btn_label' => 'Save')
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->saveToDB($form->getData());
            return $this->redirectToRoute('task');
        }

        return $this->render(
            'task/edit.html.twig', array('form' => $form->createView())
        );        
    }

    /**
     * @Route("/task", name="task")
     */
    public function index(Request $request)
    {
        $sort            = $request->query->get('sort');
        $sortTitle       = ($sort == 'Title'       ? 'Title_desc'       : 'Title');
        $sortDescription = ($sort == 'Description' ? 'Description_desc' : 'Description');
        $sortDate        = ($sort == 'Date'        ? 'Date_desc'        : 'Date');
        $sortStatus      = ($sort == 'Status'      ? 'Status_desc'      : 'Status');
        $tasks           = $this->getSorted($sort);

        return $this->render(
            'task/index.html.twig',
            array(
                'tasks'           => $tasks,
                'sortTitle'       => $sortTitle,
                'sortDescription' => $sortDescription,
                'sortDate'        => $sortDate,
                'sortStatus'      => $sortStatus,
                'sortJSON'        => $sort
            )
        );
    }

    /**
     * @Route("/task/getJSON", name="task_getJSON")
     */
    public function getJSON(Request $request)
    {
        return $this->json($this->getSorted($request->query->get('sort')));
    }

    /**
     * Returns a list of tasks sorted by the specified sort column and order.
     * @param sort Sort column and order
     */
    private function getSorted($sort)
    {
        $repository = $this->getDoctrine()->getRepository(Task::class);

        if (!empty($sort))
        {
            $sort       = strtolower($sort);
            $sortOrder  = substr($sort, -5);
            $sortColumn = ($sortOrder == '_desc' ? substr($sort, 0, strpos($sort, '_desc')) : $sort);
            $sortOrder  = ($sortOrder == '_desc' ? 'DESC' : 'ASC');
        }
        else
        {
            $sortColumn = 'title';
            $sortOrder  = 'ASC';
        }

        return $repository->findBy(array(), array($sortColumn => $sortOrder));
    }

    /**
     * Removes the task from the database.
     */
    private function removeFromDB(Task $task)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($task);
        $entityManager->flush();
    }

    /**
     * Saves the task to the database.
     */
    private function saveToDB(Task $task)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($task);
        $entityManager->flush();
    }
}
