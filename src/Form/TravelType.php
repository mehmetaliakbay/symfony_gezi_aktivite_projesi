<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Travel;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use FOS\CKEditorBundle\Form\Type\CKEditorType;

class TravelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'title',
            ])
            ->add('title')
            ->add('keywords')
            ->add('description')
            ->add('email')
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'True' => 'True',
                    'False' => 'False'
                ]
            ])
            ->add('star', ChoiceType::class, [
                'choices' => [
                    '1 Yıldız' => '1',
                    '2 Yıldız' => '2',
                    '3 Yıldız' => '3',
                    '4 Yıldız' => '4',
                    '5 Yıldız' => '5',

                ]
            ])
            ->add('address')
            ->add('image', FileType::class, [
                'label' => 'Travel Main Image',

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,
                // make it optional so you don't have to re-upload the PDF file
                // everytime you edit the Product details
                'required' => false,
                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/*',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid Image document',
                    ])
                ],
            ])
            ->add('city', ChoiceType::class, [
                'choices' => [
                    'ADANA' => '1',
                    'ADIYAMAN' => '2',
                    'AFYONKARAHİSAR' => '3',
                    'AĞRI' => '4',
                    'AMASYA' => '5',
                    'ANKARA' => '6',
                    'ANTALYA' => '7',
                    'ARTVİN' => '8',
                    'AYDIN' => '9',
                    'BALIKESİR' => '10',
                    'BİLECİKK' => '11',
                    'BİNGÖL' => '12',
                    'BİTLİS' => '13',
                    'BOLU' => '14',
                    'BURDUR' => '15',
                    'BURSA' => '16',
                    'ÇANAKKALE' => '17',
                    'ÇANKIRI' => '18',
                    'ÇORUM' => '19',
                    'DENİZLİ' => '20',
                    'DİYARBAKIR' => '21',
                    'EDİRNE' => '22',
                    'ELAZIĞ' => '23',
                    'ERZİNCAN' => '24',
                    'ERZURUM' => '25',
                    'ESKİŞEHİR' => '26',
                    'GAZİANTEP' => '27',
                    'GİRESUN' => '28',
                    'GÜMÜŞHANE' => '29',
                    'HAKKARİ' => '30',
                    'HATAY' => '31',
                    'ISPARTA' => '32',
                    'MERSİN' => '33',
                    'İSTANBUL' => '34',
                    'İZMİR' => '35',
                    'KARS' => '36',
                    'KASTAMONU' => '37',
                    'KAYSERİ' => '38',
                    'KIRKLARELİ' => '39',
                    'KIRŞEHİR' => '40',
                    'KOCAELİ' => '41',
                    'KONYA' => '42',
                    'KÜTAHYA' => '43',
                    'MALATYA' => '44',
                    'MANİSA' => '45',
                    'KAHRAMANMARAŞ' => '46',
                    'MARDİN' => '47',
                    'MUĞLA' => '48',
                    'MUŞ' => '49',
                    'NEVŞEHİR' => '50',
                    'NİĞDE' => '51',
                    'ORDU' => '52',
                    'RİZE' => '53',
                    'SAKARYA' => '54',
                    'SAMSUN' => '55',
                    'SİİRT' => '56',
                    'SİNOP' => '57',
                    'SİVAS' => '58',
                    'TEKİRDAĞ' => '59',
                    'TOKAT' => '60',
                    'TRABZON' => '61',
                    'TUNCELİ' => '62',
                    'ŞANLIURFA' => '63',
                    'UŞAK' => '64',
                    'VAN' => '65',
                    'YOZGAT' => '66',
                    'ZONGULDAK' => '67',
                    'AKSARAY' => '68',
                    'BAYBURT' => '69',
                    'KARAMAN' => '70',
                    'KIRIKKALE' => '71',
                    'BATMAN' => '72',
                    'ŞIRNAK' => '73',
                    'BARTIN' => '74',
                    'ARDAHAN' => '75',
                    'IĞDIR' => '76',
                    'YALOVA' => '77',
                    'KARABüK' => '78',
                    'KİLİS' => '79',
                    'OSMANİYE' => '80',
                    'DÜZCE' => '81'
                ]
            ])
            ->add('country')
            ->add('location')
            ->add('detail', CKEditorType::class, array(
                'config' => array(
                    'uiColor' => '#ffffff',
                ),
            ))
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Travel::class,
        ]);
    }
}
