<?php

namespace Universal Reading\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Universal Reading\Entity\IgnoreOriginUserRule;

class IgnoreOriginUserRuleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IgnoreOriginUserRule::class);
    }
}
