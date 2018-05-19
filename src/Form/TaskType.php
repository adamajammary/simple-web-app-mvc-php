<?php

namespace App\Form;

use App\Entity\Task;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskType extends AbstractType
{
    private $statusCodes = array(
        'N/A'         => 'N/A',
        'Not Started' => 'Not Started',
        'Started'     => 'Started',
        'In Progress' => 'In Progress',
        'Almost Done' => 'Almost Done',
        'Completed'   => 'Completed'
    );

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title',       TextType::class,   array('attr'     => array('minlength' => 3)))
            ->add('description', TextType::class,   array('required' => false))
            ->add('date',        DateType::class)
            ->add('status',      ChoiceType::class, array('choices'  => $this->statusCodes, 'expanded' => false, 'multiple' => false))
            ->add('save',        SubmitType::class, array('label'    => $options['btn_label']));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => Task::class,
                'btn_label'  => ''
            )
        );
    }
}

?>
