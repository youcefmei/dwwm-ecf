<?php 
// namespace App\EventSubscriber;
namespace App\EventSubscriber;

use App\Entity\Course;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;

class EasyAdminSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return [
            // BeforeEntityPersistedEvent::class => ['setCourseSlug'],
        ];
    }

    public function setCourseSlug(BeforeEntityPersistedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof Course)) {
            return;
        }
        $slug = (new AsciiSlugger())->slug($entity->getTitle());
        $entity->setSlug($slug);
        $entity->setImageFile($entity->getImage());
        // $entity->setCreatedAt(new \DateTimeImmutable());
        // $entity->setImageSize(0);
        // dd($event);
        dd($entity);
        // dd($this);
    }
}