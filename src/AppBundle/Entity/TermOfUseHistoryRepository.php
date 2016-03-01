<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * TermOfUseRepository
 */
class TermOfUseHistoryRepository extends EntityRepository
{
    /**
     * Get the complete history for a term.
     * 
     * @param int $termId
     * @return TermOfUseHistory[]
     */
    public function getTermHistory($termId) {
        return $this->findBy(array(
            'termId' => $termId,
        ));
    }
}
