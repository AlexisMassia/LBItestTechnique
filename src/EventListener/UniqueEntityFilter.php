<?php

namespace App\EventListener;

use App\Entity\Type;
use Doctrine\ORM\Events;
use App\Entity\MovieHasPeople;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UniqueEntityFilter implements EventSubscriber
{

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function getSubscribedEvents()
    {
        return [Events::onFlush];
    }

    public function onFlush(OnFlushEventArgs $args)
    {
        $unitOfWork = $this->entityManager->getUnitOfWork();
        foreach ($unitOfWork->getScheduledEntityInsertions() as $entity) {
            if ($entity instanceof Type) {
                $existingEntity = $this->entityManager->getRepository(Type::class)->findOneByName($entity->getName());
                if ($existingEntity) {
                    throw new HttpException(Response::HTTP_UNPROCESSABLE_ENTITY, 'Duplicate Type entity found.');
                }
            }
            elseif ($entity instanceof MovieHasPeople) {
                $existingEntity = $this->entityManager->getRepository(MovieHasPeople::class)->findOneBy([
                    "movie" => $entity->getMovie(),
                    "people" => $entity->getPeople(),
                ]);
                if ($existingEntity) {
                    throw new HttpException(Response::HTTP_UNPROCESSABLE_ENTITY, 'Duplicate Type entity found.');
                }
            }
        }

        foreach ($unitOfWork->getScheduledEntityUpdates() as $entity) {
            if ($entity instanceof Type) {
                $existingEntity = $this->entityManager->getRepository(Type::class)->findOneByName($entity->getName());
                if ($existingEntity && $existingEntity->getId() !== $entity->getId()) {
                    throw new HttpException(Response::HTTP_UNPROCESSABLE_ENTITY, 'Duplicate MovieHasPeople entity found.');
                }
            }
            elseif ($entity instanceof MovieHasPeople) {
                $existingEntity = $this->entityManager->getRepository(MovieHasPeople::class)->findOneBy([
                    "movie" => $entity->getMovie(),
                    "people" => $entity->getPeople()
                ]);
                if ($existingEntity && $existingEntity->getId() !== $entity->getId()) {
                    throw new HttpException(Response::HTTP_UNPROCESSABLE_ENTITY, 'Duplicate MovieHasPeople entity found.');
                }
            }
        }
    }
}
