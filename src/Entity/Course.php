<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CourseRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Validator\Constraints\Range;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\UploadedFile;



#[ORM\Entity(repositoryClass: CourseRepository::class)]
#[Vich\Uploadable] 
// #[Valid]
class Course
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private $title;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private $slug;

    #[ORM\Column(type: 'text')]
    private $description;

    #[ORM\Column(type: 'datetime_immutable')]
    private $created_at;


    #[ORM\Column(type: 'boolean')]
    private $is_published;

    #[ORM\OneToMany(mappedBy: 'course', targetEntity: Section::class, orphanRemoval: true)]
    // #[ORM\OneToMany(mappedBy: 'course', targetEntity: Section::class)]
    private $sections;


    #[ORM\Column(type: 'string', length: 255)]
    private ?string $image=null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $updatedAt = null;


    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $published_at;


    public function __construct()
    {
        $this->sections = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->courseStudents = new ArrayCollection();
        
    }

    public function __toString()
    {
        return $this->title;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        $this->updatedAt = new \DateTimeImmutable();
        if(!$this->slug){
            $this->slug= (new AsciiSlugger())->slug($title);
        }
        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    
    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    

    public function getIsPublished(): ?bool
    {
        return $this->is_published;
    }

    public function setIsPublished(bool $is_published): self
    {
        $this->is_published = $is_published;
        if($this->is_published){
            $this->published_at = new \DateTimeImmutable();
        }
        return $this;
    }

    /**
     * @return Collection<int, Section>
     */
    public function getSections(): Collection
    {
        return $this->sections;
    }

    public function addSection(Section $section): self
    {
        if (!$this->sections->contains($section)) {
            $this->sections[] = $section;
            $section->setCourse($this);
        }

        return $this;
    }

    public function removeSection(Section $section): self
    {
        if ($this->sections->removeElement($section)) {
            // set the owning side to null (unless already changed)
            if ($section->getCourse() === $this) {
                $section->setCourse(null);
            }
        }

        return $this;
    }
    
    
        #[ORM\Column(type: 'integer')]
        #[Range(
            max: 1000000,
            notInRangeMessage: 'Max file size is 1M',
        )]
        private ?int $imageSize = null;
    
    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     */
    #[Vich\UploadableField(mapping: 'course_image', fileNameProperty: 'image', size: 'imageSize')]
    private ?File $imageFile = null;

    #[ORM\ManyToOne(targetEntity: Teacher::class, inversedBy: 'courses')]
    #[ORM\JoinColumn(nullable: true)]
    private $teacher;

    // #[ORM\OneToMany(mappedBy: 'course', targetEntity: CourseStudent::class, orphanRemoval: true,cascade:["persist", "remove"])]
    #[ORM\OneToMany(mappedBy: 'course', targetEntity: CourseStudent::class, orphanRemoval: true)]
    private $courseStudents;



    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|UploadedFile|null $imageFile
     */
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->setImageSize( $imageFile->getSize());
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImage(?string $image): void
    {
        $this->image = $image;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getImage(): ?string
    {
        return $this->image;
    }
    
    public function setImageSize(?int $imageSize): void
    {
        $this->updatedAt = new \DateTimeImmutable();
        $this->imageSize = $imageSize;
    }

    public function getImageSize(): ?int
    {
        return $this->imageSize;
    }

    public function getTeacher(): ?Teacher
    {
        return $this->teacher;
    }

    public function setTeacher(?Teacher $teacher): self
    {
        $this->teacher = $teacher;

        return $this;
    }

    /**
     * @return Collection<int, CourseStudent>
     */
    public function getCourseStudents(): Collection
    {
        return $this->courseStudents;
    }

    public function addCourseStudent(CourseStudent $courseStudent): self
    {
        if (!$this->courseStudents->contains($courseStudent)) {
            $this->courseStudents[] = $courseStudent;
            $courseStudent->setCourse($this);
        }

        return $this;
    }

    public function removeCourseStudent(CourseStudent $courseStudent): self
    {
        if ($this->courseStudents->removeElement($courseStudent)) {
            // set the owning side to null (unless already changed)
            if ($courseStudent->getCourse() === $this) {
                $courseStudent->setCourse(null);
            }
        }

        return $this;
    }

    public function getPublishedAt(): ?\DateTimeImmutable
    {
        return $this->published_at;
    }

    public function setPublishedAt(?\DateTimeImmutable $published_at): self
    {
        $this->published_at = $published_at;

        return $this;
    }

}
