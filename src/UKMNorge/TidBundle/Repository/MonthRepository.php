<?php

namespace UKMNorge\TidBundle\Repository;

/**
 * MonthRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MonthRepository extends \Doctrine\ORM\EntityRepository
{
	public function getUserMonth($user, $month) {
		return $this->findOneBy(array('user' => $user, 'month' => $month));
	}

	public function getYearTotal($user, $max_month, $year) {
		#dump($user);
		#dump($max_month);
		#dump($year);

		$qry = $this->createQueryBuilder('m')
					->join('m.month', 'bm')
					->andWhere('bm.year = :year')
					->setParameter('year', $year)
					->andWhere('bm.month <= :month')
					->setParameter('month', $max_month)
					->andWhere('m.user = :user')
					->setParameter('user', $user->getId())
					->select('SUM(m.worked)')
					->getQuery();

		#dump($qry);
		#dump($qry->getSQL());
		#dump($qry->getParameters());
		return $qry->getSingleScalarResult();
	}
}
