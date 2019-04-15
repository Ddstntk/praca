<?php
/**
 * PHP Version 5.6
 * Comment type.
 *
 * @category  Social_Network
 *
 * @author    Konrad Szewczuk <konrad3szewczuk@gmail.com>
 *
 * @copyright 2018 Konrad Szewczuk
 *
 * @license   https://opensource.org/licenses/MIT MIT license
 *
 * @link      cis.wzks.uj.edu.pl/~16_szewczuk
 */
namespace Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Validator\Constraints as CustomAssert;

/**
 * Class CommentType
 *
 * @category  Social_Network
 *
 * @author    Konrad Szewczuk <konrad3szewczuk@gmail.com>
 *
 * @copyright 2018 Konrad Szewczuk
 *
 * @license   https://opensource.org/licenses/MIT MIT license
 *
 * @link      cis.wzks.uj.edu.pl/~16_szewczuk
 */
class CommentType extends AbstractType
{
    /**
     * Form builder
     *
     * @param FormBuilderInterface $builder Form builder
     * @param array                $options Form options
     *
     * @return none
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'content',
            TextType::class,
            [
                'label' => 'label.content',
                'required' => true,
                'attr' => [
                    'max_length' => 1000,
                ],
                'constraints' => [
                    new Assert\NotBlank(
                        ['groups' => ['comment-default']]
                    ),
                    new Assert\Length(
                        [
                            'groups' => ['comment-default'],
                            'min' => 1,
                            'max' => 1000,
                        ]
                    ),
                ],
            ]
        );
    }

    /**
     * Options configuration
     *
     * @param OptionsResolver $resolver Options Resolver
     *
     * @return nothing
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'validation_groups' => 'comment-default',
            ]
        );
    }

    /**
     * Get prefix
     *
     * @return null|string
     */
    public function getBlockPrefix()
    {
        return 'comment_type';
    }
}
