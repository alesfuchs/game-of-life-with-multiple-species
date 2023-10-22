<?php declare(strict_types = 1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

class GameAssignmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('initialData', TextareaType::class, ['required' => true])
            ->add('pauseAfterIterationsCount', IntegerType::class, ['required' => true])
            ->add('currentIterationNumber', HiddenType::class)
            ->add('currentIterationData', HiddenType::class)
            ->add('submit', SubmitType::class);
    }
}
