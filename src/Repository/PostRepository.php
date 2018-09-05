<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\ORM\EntityRepository;
use App\DTO\PostSearch;
use App\DTO\PostSearchFormType;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Entity\Comment;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\Paginator;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends EntityRepository
{


//    /**
//     * @return Post[] Returns an array of Post objects
//     */


    public function findByPostSearch(PostSearch $dto)
    {
        $queryBuilder = $this->createQueryBuilder('ta');
        
        if (!empty($dto->search)) {

            $queryBuilder->andWhere('ta.title like :search');

            $queryBuilder->setParameter('search', '%' . $dto->search . '%');
        }
        
        return $queryBuilder->getQuery()->execute();

    }
    public function listComment(Comment $comment)
    {
      
    }
    
    public function findPaginates(Request $request, Paginator $paginator, PostSearch $dto)
    {
        $limit = 8;
        $query = $this->createQueryBuilder('p');
        $query->orderBy('p.creationDate', 'DESC'); 
        
        if (!empty($dto->search)) {
            $query->andWhere('p.title like :search');
            $query->setParameter('search', '%' . $dto->search . '%');
            
            $limit = 100;
        }
        
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $limit
        );
        
        return $pagination;
    }
  
}

