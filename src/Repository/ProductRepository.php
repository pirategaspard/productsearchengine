<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
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
            ->execute();
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
            ->getResult();
        /*print_r(array(
        'sql'        => $r->getSQL(),
        'parameters' => $r->getParameters(),
        ));
        die;*/
        return $r;
    }
    
    // Using the fulltext index
    public function findProductFullTextSearch($searchstring='')
    {
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
	
	public function findNext($offset=0,$limit=8)
	{		
		$dql = "SELECT p FROM App\Entity\Product p ORDER BY p.date_last_updated ASC";
		$q = $this->getEntityManager()->createQuery($dql);
		$p = new Paginator($q,$offset,$limit);
		$p->getQuery()
			->setFirstResult($offset)
			->setMaxResults($limit);
		//echo count($p); die;
		return $p;
	}
	
	// when you want to clear all your data and start over
	public function removeAllProducts(): self
    {	
		// "Nuke it from orbit. Its the only way to be sure"		
		$sql = "TRUNCATE TABLE product";
		$conn = $this->getEntityManager()->getConnection();
		$q = $conn->prepare($sql);
		$q->execute();
		return $this;
	}
	
}
