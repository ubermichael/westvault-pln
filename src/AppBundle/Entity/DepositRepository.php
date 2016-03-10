<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * DepositRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DepositRepository extends EntityRepository
{
    /**
     * Find deposits by state.
     * 
     * @param string $state
     * 
     * @return Deposit[]
     */
    public function findByState($state) {
        return $this->findBy(array(
            'state' => $state,
        ));
    }
	
    /**
     * Summarize deposits by counting them by state.
     * 
     * @return array
     */
	public function stateSummary() {
		$qb = $this->createQueryBuilder('e');
		$qb->select('e.state, count(e) as ct')
				->groupBy('e.state')
				->orderBy('e.state');
		return $qb->getQuery()->getResult();
	}
	
    /**
     * Search for deposits by UUID or part of a UUID.
     * 
     * @param string $q
     * @return Deposit[]
     */
	public function search($q) {
		$qb = $this->createQueryBuilder('d');
		$qb->where('d.depositUuid LIKE :q');
		$qb->setParameter('q', '%' . strtoupper($q) . '%');
		return $qb->getQuery()->getResult();
	}
}
