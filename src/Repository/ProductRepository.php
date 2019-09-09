<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

    /**
     * @param Category $category
     * @param array $filter['attr1913' => ['1.1'], 'attr23' => ['5']]
     *
     * @return Product[]
     */
    public function findByFilter(Category $category, array $filter)
    {
        $queryBilder = $this->createQueryBuilder('p');
        $queryBilder
            ->join('p.categories', 'c')
            ->andWhere('c.id = :category')
            ->setParameter('category', $category);

        foreach ($filter as $key => $values) {
            if(!$values){
                continue;
            }
        $attributeId =substr($key, 4);

        $queryBilder
            ->join('p.attributeValues', $key)
            ->andWhere('IDENTITY('. $key . '.attribute) = :' .$key)
            ->setParameter($key, $attributeId)
            ->andWhere($queryBilder->expr()->in($key . '.value', $values));

        }

        return $queryBilder->getQuery()->execute();
    }



}
