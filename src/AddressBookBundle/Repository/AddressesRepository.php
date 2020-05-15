<?php declare(strict_types=1);

namespace AddressBookBundle\Repository;

use AddressBookBundle\Entity\Addresses;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;

class AddressesRepository extends \Doctrine\ORM\EntityRepository
{
    const ITEMS_PER_PAGE = 8;

    public function getContactListPageCount()
    {
        $em = $this->getEntityManager();
        $builder = $em->createQueryBuilder();
        $builder->select('count(ad.id) as cnt')
            ->from('AddressBookBundle:Addresses', 'ad');
        $data = $builder->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);

        return $pages = ceil($data[0]['cnt'] / self::ITEMS_PER_PAGE);
    }

    public function getContactListQuery($page)
    {
        $multiplier = $page > 1 ? $page - 1 : 0;
        $em = $this->getEntityManager();
        $builder = $em->createQueryBuilder();
        $builder->select([
            'ad.id',
            'ad.firstname',
            'ad.lastname',
            'ad.streetNo',
            'ad.zip',
            'ad.city',
            'ad.countryIsoAlpha2',
            'ad.phone',
            'ad.birthday',
            'ad.email',
            'ad.pictureUrl']
        )->from('AddressBookBundle:Addresses', 'ad')
            ->setFirstResult(self::ITEMS_PER_PAGE * $multiplier)
            ->setMaxResults(self::ITEMS_PER_PAGE);

        return $builder->getQuery();
    }

    public function deleteContact($id)
    {
        $em = $this->getEntityManager();
        $em->getConnection()->delete('addresses', ['id' => $id], ['id' => \PDO::PARAM_INT]);
    }
}
