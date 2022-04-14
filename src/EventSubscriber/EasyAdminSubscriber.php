<?php 
// namespace App\EventSubscriber;
namespace App\EventSubscriber;

use App\Entity\Course;
use App\Entity\Lesson;
use App\Entity\Section;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeCrudActionEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;

class EasyAdminSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        
        return [
            
            BeforeEntityPersistedEvent::class => ['setEntitySlug'],
            BeforeEntityUpdatedEvent::class => ['setEntitySlug'],
        ];
    }



    public function setEntitySlug(BeforeEntityPersistedEvent|BeforeEntityUpdatedEvent $event)
    {
        $entity = $event->getEntityInstance();
        if (!($entity instanceof Course || $entity instanceof Section || $entity instanceof Lesson )) {
            return;
        }
        $slug = (new AsciiSlugger())->slug($entity->getTitle());
        $entity->setSlug( strtolower($slug)   );
    }
}