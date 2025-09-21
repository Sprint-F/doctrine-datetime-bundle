<?php

namespace SprintF\Bundle\Datetime\Component\Form\Type;

use SprintF\Bundle\Datetime\Component\Form\Transformer\DateTransformer;
use Symfony\Component\Form\Extension\Core\Type\DateType as BaseType;
use Symfony\Component\Form\FormBuilderInterface;

class DateType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);
        $builder->addModelTransformer(new DateTransformer());
    }
}
