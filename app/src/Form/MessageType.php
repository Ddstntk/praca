<?php
/**
 * PHP Version 5.6
 * Message type.
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
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Validator\Constraints as CustomAssert;

/**
 * Class MessageType
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
class MessageType extends AbstractType
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
            TextareaType::class,
            [
                'label' => false,
                'attr' => [
                    'style' => 'height:55%; width:100%; resize: none',
                    'rows' => 3,
                ],
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(
                        ['groups' => ['message-default']]
                    ),
                    new Assert\Length(
                        [
                            'groups' => ['message-default'],
                            'min' => 1,
                            'max' => 1000,
                        ]
                    ),
                    new CustomAssert\UniqueEmail(
                        ['groups' => ['message-default'],
                            'repository' =>
                                isset($options['chat_repository']) ?
                                    $options['chat_repository'] :
                                    null,
                            'email' =>
                                isset($options['data']['id']) ?
                                    $options['data']['id'] :
                                    null, ]
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
     * @return none
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'validation_groups' => 'message-default',
                'user_repository' => null,
            ]
        );
    }

    /**
     * Get block prefix
     *
     * @return null|string
     */
    public function getBlockPrefix()
    {
        return 'message_type';
    }
}
