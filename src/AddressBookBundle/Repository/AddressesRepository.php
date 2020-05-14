<?php declare(strict_types=1);

namespace AddressBookBundle\Repository;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;

class AddressesRepository extends \Doctrine\ORM\EntityRepository
{
    const ITEMS_PER_PAGE = 20;

    public function getContactListPageCount()
    {
        $em = $this->getEntityManager();
        $builder = $em->createQueryBuilder();
        $builder->select('count(ad.*) as cnt')
            ->from('AddressBookBundle:Addresses', 'ad');
        $data = $builder->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);

        return $pages = ceil($data['cnt'] / self::ITEMS_PER_PAGE);
    }

    public function getContactListQuery($page)
    {
        $multiplier = $page > 1 ? $page - 1 : 1;
        $em = $this->getEntityManager();
        $builder = $em->createQueryBuilder();
        $builder->select('ad.*')
            ->from('AddressBookBundle:Addresses', 'ad')
            ->setFirstResult(self::ITEMS_PER_PAGE * $multiplier)
            ->setMaxResults(self::ITEMS_PER_PAGE);

        return $builder->getQuery();
    }
}
