<?php

namespace App\Repository;

use App\Entity\ExchangeRate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @method ExchangeRate|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExchangeRate|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExchangeRate[]    findAll()
 * @method ExchangeRate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExchangeRateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExchangeRate::class);
    }

    /**
     * @return QueryBuilder Returns a QueryBuilder for ExchangeRate objects of Currency object $currency ordered by $order
     */
    public function findAll2OneCurrencyPage($currency, $order): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            ->where('p.id_currency = :id')
            ->setParameter('id', $currency->getId())
            ->orderBy('p.date', $order);
    }

    /**
     * @return QueryBuilder Returns a QueryBuilder for ExchangeRate objects of Currency object $currency ordered by $order
     */
    public function findAllOneCurrencyPage($currency, $order): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            ->where('p.id_currency = :id')
            ->setParameter('id', $currency->getId())
            ->orderBy('p.date', $order);
    }

    /**
     * @return QueryBuilder Returns a QueryBuilder for all ExchangeRate objects ordered by $order
     */
    public function findAllCurrenciesPage($order): QueryBuilder
    {
        return$this->createQueryBuilder('p')
            ->orderBy('p.date', $order);
    }

    /**
     * @return QueryBuilder Returns a QueryBuilder for ExchangeRate objects of Currency object $currency in range($date_start, $date_end) ordered by $order
     */
    public function findBetweenDatesOneCurrencyPage($currency, $date_start, $date_end, $order): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            ->where('p.id_currency = :id')
            ->andwhere('p.date >= :date_start')
            ->andWhere('p.date <= :date_end')
            ->setParameter('id', $currency->getId())
            ->setParameter('date_start', $date_start->format('Y-m-d'))
            ->setParameter('date_end', $date_end->format('Y-m-d'))
            ->orderBy('p.date', $order);
    }

    /**
     * @return QueryBuilder Returns a QueryBuilder for ExchangeRate objects in range($date_start, $date_end) ordered by $order
     */
    public function findBetweenDatesAllCurrenciesPage($date_start, $date_end, $order): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            ->where('p.date >= :date_start')
            ->andWhere('p.date <= :date_end')
            ->setParameter('date_start', $date_start->format('Y-m-d'))
            ->setParameter('date_end', $date_end->format('Y-m-d'))
            ->orderBy('p.date', $order);
    }

}
