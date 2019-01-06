<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Product::class);
    }
    
	public function deleteByIdCode($id_code=0)
    {
		return $this->createQueryBuilder('p')
			->delete()
            ->Where('p.id_code = :id_code')
            ->setParameter('id_code', $id_code)
            ->getQuery()
            ->execute()
        ;
    }
    
    //Simple search
	public function findProductsSimpleSearch($searchstring='')
    {
        $r = $this->createQueryBuilder('p')
            ->Where('p.name LIKE :searchstring')   
            ->orWhere('p.description LIKE :searchstring')
            ->orWhere('p.url LIKE :searchstring')
            ->orWhere('p.url_canonical LIKE :searchstring')
            ->setParameter('searchstring', '%'.$searchstring.'%')
            ->orderBy('p.price', 'ASC')
            ->setMaxResults(100)
            ->getQuery()
            ->getResult()
        ;
        /*print_r(array(
        'sql'        => $r->getSQL(),
        'parameters' => $r->getParameters(),
        ));
        die;*/
        return $r;
        //->orWhere('p.description LIKE :searchstring')
    }
    
    // Using the fulltext index
    public function findProductFullTextSearch($searchstring='')
    {
		
		/*$r = $this->createQueryBuilder('p')
            ->Where('MATCH(p.data) AGAINST (:searchstring) ')   
            ->setParameter('searchstring', $searchstring )
            ->orderBy('p.price', 'ASC')
            ->setMaxResults(100)
            ->getQuery()
            ->getResult()
        ;
        return $r; */
        
        
        
        $rsm = new ResultSetMapping;
		$rsm->addEntityResult('App\Entity\Product', 'p');
		$rsm->addFieldResult('p', 'id', 'id');
		$rsm->addFieldResult('p', 'id_code', 'id_code');
		$rsm->addFieldResult('p', 'price', 'price');
		$rsm->addFieldResult('p', 'name', 'name');
		$rsm->addFieldResult('p', 'description', 'description');
		$rsm->addFieldResult('p', 'url', 'url');
		$rsm->addFieldResult('p', 'url_image', 'url_image');
		$rsm->addFieldResult('p', 'date_last_updated', 'date_last_updated');
		$rsm->addFieldResult('p', 'source', 'source_id');
		
		$q = $this->getEntityManager()->createNativeQuery('SELECT * FROM product p WHERE MATCH(p.name,p.data) AGAINST (:searchstring) ORDER BY p.price ASC LIMIT 100', $rsm)
			->setParameter('searchstring', $searchstring );
		return $q->getResult();
        
        
	}

    // /**
    //  * @return Product[] Returns an array of Product objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Product
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
