<?php

namespace App\Controller\Admin;

use App\Entity\Lesson;
use App\Entity\Section;
use App\Form\LessonType;
use Doctrine\ORM\QueryBuilder;
use App\Repository\CourseRepository;
use App\Repository\TeacherRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use Symfony\Component\String\Slugger\AsciiSlugger;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class SectionCrudController extends AbstractCrudController
{
    private $teacher;
    private $courses;
    public function __construct(private TeacherRepository $teacherRepository, private EntityRepository $entityRepo)
    {
        
    }

    public function configureActions(Actions $actions): Actions
{
    return $actions
        // ...
        // this will forbid to create or delete entities in the backend
        ->disable(Action::NEW, Action::DELETE)
    ;
}

    public function setTeacher()
    {
        $this->teacher = $this->teacherRepository->findOneBy(["user" => $this->getUser()]);
        return $this;
    }
    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $this->setTeacher();
        $userId = $this->teacher;
        $this->courses = $this->teacher->getCourses();
        $response = $this->entityRepo->createQueryBuilder($searchDto, $entityDto, $fields, $filters);

        // $response->andWhere('entity.course in (:courses)')->setParameter('courses', $this->courses);
        $response->andWhere('entity.teacher = :teacher')->setParameter('teacher', $this->teacher);
        return $response;
    }


    public static function getEntityFqcn(): string
    {
        
        return Section::class;
    }
    
    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('course')
            ->add('title')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        $this->setTeacher();
        // dd($this->teacher);
        
        yield TextField::new('title',label:"Titre");
        yield AssociationField::new('course', 'Formation')
            ->setSortable(true)
            ->setFormTypeOptions([
                'query_builder' => function (CourseRepository $course) {
                    
                     $course->createQueryBuilder('entity')
                     ->where('entity.teacher = :teacher')
                     ->setParameter('teacher', $this->teacher)
                    ;
                },
            ])
            ->onlyOnDetail()
            ->onlyOnIndex()
            ;
        yield CollectionField::new('lessons', 'Leçons')
        ->setEntryType(LessonType::class)
    
        // ->setFormType(LessonType::class)
        ;
        yield AssociationField::new('quiz', 'Quiz');
        yield DateField::new('addedAt', 'Date d\'ajout')
        ->onlyOnIndex();
            // yield AssociationField::new('course', 'Formation');
            // yield BooleanField::new('is_published', label: "Publier la section ?");
            // yield CollectionField::new("lessons","Leçons")->setEntryType(LessonType::class)


        ;
            // yield TextEditorField::new('description');
        
    }

    private function entityCommon(EntityManagerInterface $entityManager,Section $section):Section
    {
        /** @var Section $section */
        $slug = (new AsciiSlugger())->slug( strtolower($section->getTitle()) );
        $section->setSlug($slug);
        
        foreach ($section->getLessons() as $lesson) {
            $lesson->setTeacher($this->teacher);
            $entityManager->persist($lesson);
        }
        // dd($section);
        return $section;
    }


    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        /** @var Section $section */
        $section = $this->entityCommon($entityManager,$entityInstance);
        $section->setAddedAt(new \DateTimeImmutable()) ;
        $section->setTeacher($this->teacher);
        parent::persistEntity($entityManager, $section);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        /** @var Section $section */
        $section = $this->entityCommon($entityManager,$entityInstance);
        parent::persistEntity($entityManager, $section);
    }

}
