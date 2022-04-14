<?php

namespace App\Controller\Admin;

use App\Entity\Course;
use DateTimeImmutable;
use App\Entity\Section;
use App\Form\SectionType;
use Doctrine\ORM\QueryBuilder;
use App\Admin\Field\VichImageField;
use App\Repository\TeacherRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\HttpFoundation\File\File;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Vich\UploaderBundle\Form\Type\VichImageType;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use Symfony\Component\String\Slugger\AsciiSlugger;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class CourseCrudController extends AbstractCrudController
{
    private $teacher;

    public function __construct(private TeacherRepository $teacherRepository, private EntityRepository $entityRepo)
    {
    }

    public function setTeacher()
    {
        $this->teacher = $this->teacherRepository->findOneBy(["user" => $this->getUser()]);

        return $this;
    }
    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $userId = $this->teacher;
        $response = $this->entityRepo->createQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $response->andWhere('entity.teacher = :userId')->setParameter('userId', $userId);
        return $response;
    }


    public static function getEntityFqcn(): string
    {
        return Course::class;
    }


    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setEntityLabelInPlural("Formations")
            ->setEntityLabelInSingular("Formation")
            ->setEntityPermission('ROLE_TEACHER')
            ->addFormTheme('@FOSCKEditor/Form/ckeditor_widget.html.twig')
        ;
    }

    // public function createEntity(string $entityFqcn) {
    //     $course = new Course();
    //     $course->setCreatedAt(new \DateTimeImmutable());
    //     $slugger = new AsciiSlugger();

    //     $course->setSlug($slugger->slug($course->getTitle()));
    //     dd($course);
    //     // $entity->setWhatEverBooleanField(true);
    //     return $course;
    // }

    public function configureFields(string $pageName): iterable
    {
        $this->setTeacher();
        yield TextField::new('title', label: "Intitulé de la formation");
        yield TextEditorField::new('description')->setFormType(CKEditorType::class)
        
        ;
        // yield SlugField::new('slug')
        // ->setTargetFieldName('title');
        yield BooleanField::new('is_published', label: "Publier la formation ?");
        // yield CollectionField::new('sections', label: "Sections")
        //     ->setEntryType(SectionType::class);

        yield ImageField::new('image', 'Image')
            ->onlyOnIndex()
            ->setBasePath('/images/courses')
            // ->setUploadDir('public/images/courses/')
            ->setUploadedFileNamePattern('[name]-[randomhash].[extension]');

        $imageField =  TextField::new("imageFile", "Image")
            ->onlyOnForms()
            ->setFormType(VichImageType::class)
            ->setCustomOption('image_uri', 'images/courses/')
            ->setCustomOption('download_uri', 'public/images/courses/');
        if (Crud::PAGE_EDIT == $pageName) {
            $imageField->setRequired(false);
        }
        // yield AssociationField::new('sections','Sections')
        // ->setSortable(true)
        // ;
        yield $imageField;
    }


    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        /** @var Course $course */
        $course = $entityInstance;
        $course->setCreatedAt(new \DateTimeImmutable());
        $slug = (new AsciiSlugger())->slug($course->getTitle());
        $course->setSlug($slug);
        $course->setTeacher($this->teacher);
        if($course->getIsPublished()){
            $course->setPublishedAt(new \DateTimeImmutable());
        }
        else{
            $course->setPublishedAt(null);
        }
        parent::persistEntity($entityManager, $course);
    }
}
