<?php

namespace App\Controller\Admin;

use App\Entity\Teacher;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Vich\UploaderBundle\Form\Type\VichImageType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CodeEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\FileUploadType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class TeacherCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Teacher::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setEntityLabelInPlural("Formateurs")
            ->setEntityLabelInSingular("Formateur")
            ->setDefaultSort(["user.id" => "desc", "isApproved" => "asc",])
            ->setEntityPermission('ROLE_ADMIN')
            // ->addFormTheme('@FOSCKEditor/Form/ckeditor_widget.html.twig')
            
            ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            // ->remove(Crud::PAGE_INDEX, Action::EDIT)
            
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('firstname', "Prénom")->onlyOnIndex();
        yield TextField::new('lastname', "Nom")->onlyOnIndex();
        yield EmailField::new('user.email', "E-Mail")->onlyOnIndex();
        yield BooleanField::new('isApproved', "approuvé");
        yield DateField::new('user.createdAt', "Date d'inscription")->onlyOnIndex();
        yield DateField::new('approvedAt', "Date d'acceptation")->onlyOnIndex();
        yield TextEditorField::new('description', "Description")->onlyOnIndex();
        // ->setFormType(CKEditorType::class);
        yield ImageField::new('image', 'Image')
            ->onlyOnIndex()
            ->setBasePath('/images/teachers')
            ->setUploadDir('public/images/teachers/');
            // yield ImageField::new('imageFile', 'Image')
            //     ->onlyOnForms()
            //     ->hide
            //     ->setFormType(VichImageType::class);


            // yield ImageField::new('image',"Photo")->onlyOnIndex()
            // ->setBasePath('teachers/')
            // ->setUploadDir('public/images/teachers/')
            // ->onlyOnForms()
            // ->setFormType(ImageField::class)
            // ->setFormType(VichImageType::class)
            // ->setUploadedFileNamePattern('[randomhash].[extension]')
            // ->setRequired(false)
        ;
    }
}
