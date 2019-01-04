<?php

namespace App\Repository;

use App\Entity\Source;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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
