<?php

namespace App\Controller\Admin;

use App\Entity\Lesson;
use Doctrine\ORM\QueryBuilder;
use App\Repository\SectionRepository;
use App\Repository\TeacherRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Doctrine\Common\Collections\ArrayCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;

use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class LessonCrudController extends AbstractCrudController
{
    private $teacher;
    private $courses;
    private $sections;
    public function __construct(private TeacherRepository $teacherRepository, private EntityRepository $entityRepo)
    {
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            // ...
            // this will forbid to create or delete entities in the backend
            ->disable(Action::NEW, Action::DELETE);
    }
    public function setTeacher()
    {
        $this->teacher = $this->teacherRepository->findOneBy(["user" => $this->getUser()]);
        $this->courses = $this->teacher->getCourses();
        $this->sections = new ArrayCollection();
        foreach ($this->courses as $course) {
            foreach ($course->getSections() as $section) {
                $this->sections->add($section);
            }
        }
        return $this;
    }
    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $this->setTeacher();
        $userId = $this->teacher;
        $response = $this->entityRepo->createQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $response->andWhere('entity.teacher =:teacher')->setParameter('teacher', $this->teacher);
        return $response;
    }



    public static function getEntityFqcn(): string
    {
        return Lesson::class;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('section')
            // ->add('section.course')
            ->add('is_published')
            ->add('title');
    }


    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setEntityLabelInPlural("Leçons")
            ->setEntityLabelInSingular("Leçon")
            // ->setDefaultSort(["user.id" => "desc", "isApproved" => "asc",])
            ->setEntityPermission('ROLE_TEACHER')
            ->addFormTheme('@FOSCKEditor/Form/ckeditor_widget.html.twig');
    }


    public function configureFields(string $pageName): iterable
    {
        $this->setTeacher();
        yield TextField::new("title", "Titre de la leçon");
        yield TextEditorField::new('content', "Contenu")
            ->setFormType(CKEditorType::class);
        yield UrlField::new('media', "Video");

        yield TextField::new("section.course", "Formation")
        ->onlyOnIndex();
        yield AssociationField::new("section", "Section")
            ->setFormTypeOptions([
                'query_builder' => function (SectionRepository $section) {
                    return $section->createQueryBuilder('entity')
                        ->where('entity in (:sections)')
                        ->setParameter('sections', $this->sections);
                },
            ])
            ->onlyOnIndex()
        ;

        // yield SlugField::new("slug", "Texte pour l'url")
        //     ->setTargetFieldName("title");
        yield BooleanField::new("is_published", "Publier la leçon ?");
    }


    public function persistEntity(EntityManagerInterface $entityManager, $lesson): void
    {
        /** @var Lesson $lesson */

        $lesson->setTeacher($this->teacher);
        // dd($lesson);
        parent::persistEntity($entityManager, $lesson);
    }
}
