<?php

namespace SprintF\Bundle\Datetime\Component\Form\Type;

use SprintF\ValueObjects\Type\Date;
use SprintF\ValueObjects\Type\DateRange;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function Symfony\Component\Translation\t;

class DateRangeType extends AbstractType implements DataMapperInterface
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'begin_label' => null,
            'end_label' => null,
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('beginDate', DateType::class, ['required' => false, 'label' => $options['begin_label'] ?? t('form.type.daterange.begin.label', domain: 'SprintFDatetimeBundle')])
            ->add('endDate', DateType::class, ['required' => false, 'label' => $options['end_label'] ?? t('form.type.daterange.end.label', domain: 'SprintFDatetimeBundle')])
        ;
        $builder->setDataMapper($this);
    }

    public function mapDataToForms(mixed $viewData, \Traversable $forms): void
    {
        if (null === $viewData) {
            return;
        }

        if (!$viewData instanceof DateRange) {
            throw new UnexpectedTypeException($viewData, DateRange::class);
        }

        /** @var FormInterface[] $forms */
        $forms = iterator_to_array($forms);

        $forms['beginDate']->setData($viewData->getBeginDate());
        $forms['endDate']->setData($viewData->getEndDate());
    }

    public function mapFormsToData(\Traversable $forms, mixed &$viewData): void
    {
        /** @var FormInterface[] $forms */
        $forms = iterator_to_array($forms);

        $viewData = new DateRange(
            $forms['beginDate']->getData() ? Date::createFromInterface($forms['beginDate']->getData()) : null,
            $forms['endDate']->getData() ? Date::createFromInterface($forms['endDate']->getData()) : null,
        );
    }
}
