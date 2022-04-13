<?php

namespace App\Controller\Admin;

use App\Entity\Lesson;
use App\Entity\Section;
use App\Form\LessonType;
use Doctrine\ORM\QueryBuilder;
use App\Repository\CourseRepository;
use App\Repository\TeacherRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use Symfony\Component\String\Slugger\AsciiSlugger;
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

        $response->andWhere('entity.course in (:courses)')->setParameter('courses', $this->courses);
        return $response;
    }


    public static function getEntityFqcn(): string
    {
        
        return Section::class;
    }


    public function configureFields(string $pageName): iterable
    {
        $this->setTeacher();

        
        yield TextField::new('title');
        yield AssociationField::new('course', 'Formation')
            ->setSortable(true)
            ->setFormTypeOptions([
                'query_builder' => function (CourseRepository $course) {
                    return $course->createQueryBuilder('entity')
                    ->where('entity.teacher = :teacher')
                    ->setParameter('teacher', $this->teacher)
                    ;
                },
            ])
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

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        /** @var Section $section */
        $section = $entityInstance;
        // $section->setCreatedAt(new \DateTimeImmutable());
        $slug = (new AsciiSlugger())->slug($section->getTitle());
        $section->setSlug($slug);
        $section->setAddedAt(new \DateTimeImmutable()) ;
        // $section->setCourse($this->teacher);
        // $file = new File('images/courses/' . $course->getImage(), $course->getImage());
        // $course->setTeacher($this->teacher);
        // $course->setImageFile($file);
        // $course->setImageSize($file->getSize());
        // dd($file->getSize());
        // $course->setCreatedAt(new \DateTimeImmutable());
        // $course->setImageSize(0);
        // $entityManager->flush();
        // dd($course);
        parent::persistEntity($entityManager, $section);
    }
}
