<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Repository\CampusRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use League\Csv\Reader;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserController extends AbstractCrudController
{
    public function __construct(private UserPasswordHasherInterface $userPasswordHasher)
    {
    }
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_EDIT, Action::INDEX)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_EDIT, Action::DETAIL)
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        $fields = [
            IdField::new('id')->hideOnForm(),
            EmailField::new('email'),
            TextField::new('login'),

        ];
        $roles = [
            'Utilisateur' => 'ROLE_USER',
            'Admin' => 'ROLE_ADMIN',
        ];
        $fields[] = ChoiceField::new('roles')
            ->setChoices($roles)
            ->allowMultipleChoices()
            ->setFormType(ChoiceType::class)
            ->onlyOnForms();


        $password = TextField::new('password')
            ->setFormType(RepeatedType::class)
            ->setFormTypeOptions([
                'type' => PasswordType::class,
                'first_options' => ['label' => 'Password'],
                'second_options' => ['label' => '(Repeat)'],
                'mapped' => false,
            ])
            ->setRequired($pageName === Crud::PAGE_NEW)
            ->onlyOnForms()
        ;
        $fields[] = $password;

        return $fields;
    }

    public function createNewFormBuilder(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormBuilderInterface
    {
        $formBuilder = parent::createNewFormBuilder($entityDto, $formOptions, $context);
        return $this->addPasswordEventListener($formBuilder);
    }

    public function createEditFormBuilder(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormBuilderInterface
    {
        $formBuilder = parent::createEditFormBuilder($entityDto, $formOptions, $context);
        return $this->addPasswordEventListener($formBuilder);
    }

    private function addPasswordEventListener(FormBuilderInterface $formBuilder): FormBuilderInterface
    {
        return $formBuilder->addEventListener(FormEvents::POST_SUBMIT, $this->hashPassword());
    }

    private function hashPassword() {
        return function($event) {
            $form = $event->getForm();
            if (!$form->isValid()) {
                return;
            }
            $password = $form->get('password')->getData();
            if ($password === null) {
                return;
            }

            $hash = $this->userPasswordHasher->hashPassword($this->getUser(), $password);
            $form->getData()->setPassword($hash);
        };
    }
    #[IsGranted('ROLE_ADMIN')]
    #[Route("/admin/upload-csv", name:"admin_upload_csv")]
    public function uploadCsv(Request $request, EntityManagerInterface $em, CampusRepository $campusRepo): Response
    {
        $csvFile = $request->files->get('csv_file');
        if (!$csvFile || $csvFile->getClientOriginalExtension() !== 'csv') {
           $this->addFlash('danger', 'Veuillez sélectionner un fichier CSV');
        }

        // Lire le contenu du fichier CSV
        $csvReader = Reader::createFromPath($csvFile->getPathname());
        $csvReader->setHeaderOffset(0);
        $csvRecords = $csvReader->getRecords();

        foreach ($csvRecords as $record) {
            $user = new User();
            $user->setLogin(htmlspecialchars($record['login']));
            $user->setEmail(htmlspecialchars($record['email']));
            $user->setPassword($this->userPasswordHasher->hashPassword($user, $record['password']));
            $user->setRoles([$record['role']]);
            $user->setActif(true);
            $user->setCampus($campusRepo->findOneBy(['name' => htmlspecialchars($record['campus'])]));

            $em->persist($user);
            $em->flush();
        }

        $this->addFlash('success', 'Les utilisateurs ont été importés avec succès');
        return $this->redirectToRoute('admin');
    }
}
