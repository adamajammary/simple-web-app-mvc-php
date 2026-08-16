<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;

use Doctrine\Persistence\ManagerRegistry;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class TaskController extends AbstractController
{
    #[Route('/task/create', name: 'task_create')]
    public function create(Request $request, ManagerRegistry $doctrine)
    {
        $task = new Task();

        $form = $this->createForm(
            TaskType::class, $task,
            ['btn_label' => 'Create']
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->saveToDB($form->getData(), $doctrine);

            return $this->redirectToRoute('task');
        }

        return $this->render(
            'task/create.html.twig',
            ['form' => $form->createView()]
        );
    }

    #[Route('/task/delete/{id}', name: 'task_delete')]
    public function delete($id, Request $request, ManagerRegistry $doctrine)
    {
        $repository = $doctrine->getRepository(Task::class);
        $task       = $repository->find($id);

        $form = $this->createFormBuilder($task)
            ->add('delete', SubmitType::class, ['label' => 'Delete'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->removeFromDB($task, $doctrine);

            return $this->redirectToRoute('task');
        }

        return $this->render(
            'task/delete.html.twig',
            ['form' => $form->createView(), 'task' => $task]
        );
    }

    #[Route('/task/details/{id}', name: 'task_details')]
    public function details($id, ManagerRegistry $doctrine)
    {
        $repository = $doctrine->getRepository(Task::class);

        return $this->render(
            'task/details.html.twig',
            ['task' => $repository->find($id)]
        );
    }

    #[Route('/task/edit/{id}', name: 'task_edit')]
    public function edit($id, Request $request, ManagerRegistry $doctrine)
    {
        $repository = $doctrine->getRepository(Task::class);
        $task       = $repository->find($id);

        $form = $this->createForm(
            TaskType::class,
            $task, ['btn_label' => 'Save']
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->saveToDB($form->getData(), $doctrine);

            return $this->redirectToRoute('task');
        }

        return $this->render(
            'task/edit.html.twig',
	    ['form' => $form->createView()]
        );
    }

    #[Route('/task', name: 'task')]
    public function index(Request $request, ManagerRegistry $doctrine)
    {
        $sort = $request->query->get('sort');

        $sortTitle       = ($sort == 'Title'       ? 'Title_desc'       : 'Title');
        $sortDescription = ($sort == 'Description' ? 'Description_desc' : 'Description');
        $sortDate        = ($sort == 'Date'        ? 'Date_desc'        : 'Date');
        $sortStatus      = ($sort == 'Status'      ? 'Status_desc'      : 'Status');

        $tasks = $this->getSorted($sort, $doctrine);

        return $this->render(
            'task/index.html.twig',
            [
                'tasks'           => $tasks,
                'sortTitle'       => $sortTitle,
                'sortDescription' => $sortDescription,
                'sortDate'        => $sortDate,
                'sortStatus'      => $sortStatus,
                'sortJSON'        => $sort
            ]
        );
    }

    #[Route('/task/getJSON', name: 'task_getJSON')]
    public function getJSON(Request $request, ManagerRegistry $doctrine)
    {
        return $this->json($this->getSorted($request->query->get('sort'), $doctrine));
    }

    /**
     * Returns a list of tasks sorted by the specified sort column and order.
     * @param sort Sort column and order
     */
    private function getSorted($sort, ManagerRegistry $doctrine)
    {
        $repository = $doctrine->getRepository(Task::class);

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

        return $repository->findBy([], [$sortColumn => $sortOrder]);
    }

    /**
     * Removes the task from the database.
     */
    private function removeFromDB(Task $task, ManagerRegistry $doctrine)
    {
        $entityManager = $doctrine->getManager();

        $entityManager->remove($task);
        $entityManager->flush();
    }

    /**
     * Saves the task to the database.
     */
    private function saveToDB(Task $task, ManagerRegistry $doctrine)
    {
        $entityManager = $doctrine->getManager();

        $entityManager->persist($task);
        $entityManager->flush();
    }
}
