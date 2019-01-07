<?php

namespace App\Repository;

use App\Entity\Source;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Source|null find($id, $lockMode = null, $lockVersion = null)
 * @method Source|null findOneBy(array $criteria, array $orderBy = null)
 * @method Source[]    findAll()
 * @method Source[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SourceRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Source::class);
    }
    
	public function removeAllProducts($source_id): self
    {	
		$sql = "DELETE FROM product
				WHERE source_id = :source_id";		
		$conn = $this->getEntityManager()->getConnection();
		$q = $conn->prepare($sql);
		$q->bindValue(':source_id',$source_id);
		$q->execute();
		return $this;
	}
	
	// when you want to clear all your data and start over
	public function removeAllSources(): self
    {	
		// "Nuke it from orbit. Its the only way to be sure"		
		$sql = "DELETE FROM source"; // TRUNCATE TABLE throws an error sometimes related to the product FK even when all the products are deleted. not sure why
		$conn = $this->getEntityManager()->getConnection();
		$q = $conn->prepare($sql);
		$q->execute();
		return $this;
	}
    
	public function getNamedSources($offset=0,$limit=5)
    {
		// Get the Sources that were added manually	via admin area	
		$q = $this->createQueryBuilder('s')
		->select()
		->Where('s.title IS NOT NULL')
		->orderBy('s.date_last_updated', 'ASC')
		->getQuery();
		$p = new Paginator($q,$offset,$limit);
		$p->getQuery()
			->setFirstResult($offset)
			->setMaxResults($limit);
		return $p;
	}
    
    public function deleteByIdCode($id_code=0)
    {
		return $this->createQueryBuilder('s')
			->delete()
            ->Where('s.id_code = :id_code')
            ->setParameter('id_code', $id_code)
            ->getQuery()
            ->execute()
        ;
    }
        
    public function getNextOldest($limit=5)
    {
		// Get the oldest ones first		
		return $this->createQueryBuilder('s')
		->select()
		->orderBy('s.date_last_updated', 'ASC')
		->setMaxResults($limit) 
		->getQuery()->getResult();
	}
	
	public function findNext($offset=0,$limit=20)
	{		
		$dql = "SELECT s FROM App\Entity\Source s";
		$q = $this->getEntityManager()->createQuery($dql);
		$p = new Paginator($q,$offset,$limit);
		$p->getQuery()
			->setFirstResult($offset)
			->setMaxResults($limit);
		return $p;
	}

    // /**
    //  * @return Source[] Returns an array of Source objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Source
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
