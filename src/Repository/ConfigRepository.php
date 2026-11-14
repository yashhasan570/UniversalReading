<?php

namespace Universal Reading\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Universal Reading\Entity\Config;
use Universal Reading\Entity\User;

/**
 * @method Config|null findOneByUser(User $user)
 */
class ConfigRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Config::class);
    }
}
