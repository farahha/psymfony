<?php

namespace Tests\PlatformBundle\Repository;

use Doctrine\ORM\QueryBuilder;
use Tests\PlatformBundle\Entity\Advert;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * AdvertRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AdvertRepository extends \Doctrine\ORM\EntityRepository
{
    const CONDITION_EQUAL_TO = 0x1;
    const CONDITION_GREATER_THAN = 0x2;
    const CONDITION_LOWER_THAN = 0x4;
    const CONDITION_IS_NULL = 0x8;
    const CONDITION_NOT = 0x10;

    /**
     * Récupère toutes les annonces par date la plus recente
     * @return Array of Advert (Object)
     */
    public function getAdverts($page, $nbAdvertPerPage, $sets = null)
    {
        $qb = $this->createQueryBuilder('advert')
            ->orderBy('advert.date', 'DESC');

        if (empty($sets)) {
            $qb->leftJoin('advert.applications', 'app')
                    ->addSelect('app')
                    ->leftJoin('advert.image', 'img')
                    ->addSelect('img')
                    ->leftJoin('advert.categories', 'cat')
                    ->addSelect('cat')
                    ->leftJoin('advert.skills', 'skill')
                    ->addSelect('skill');
        } else {
            if (!empty($sets['__embedded']['applications'])) {
                $qb->leftJoin('advert.applications', 'app')
                    ->addSelect('app');
            }

            if (!empty($sets['__embedded']['skills'])) {
                $qb->leftJoin('advert.skills', 'skill')
                    ->addSelect('skill');
            }

            if (!empty($sets['__embedded']['image'])) {
                $qb->leftJoin('advert.image', 'img')
                    ->addSelect('img');
            }

            if (!empty($sets['__embedded']['categories'])) {
                $qb->leftJoin('advert.categories', 'cat')
                    ->addSelect('cat');
            }
        }

        $query = $qb->getQuery();

        // On défini le debut et la fin de l'intervalle de récupération
        $query->setFirstResult(($page - 1) * $nbAdvertPerPage)
        ->setMaxResults($nbAdvertPerPage);

        return new Paginator($query, true);
    }

    /**
     * Récupère toutes les annonces
     * @return Array of Advert (Array)
     */
    public function getArrayAdverts($sets = null)
    {
        $qb = $this->createQueryBuilder('advert')
        ->orderBy('advert.date', 'DESC');

        if (empty($sets)) {
            $qb->leftJoin('advert.applications', 'app')
            ->addSelect('app')
            ->leftJoin('advert.image', 'img')
            ->addSelect('img')
            ->leftJoin('advert.categories', 'cat')
            ->addSelect('cat')
            ->leftJoin('advert.skills', 'skill')
            ->addSelect('skill');
        } else {
            if (!empty($sets['__embedded']['applications'])) {
                $qb->leftJoin('advert.applications', 'app')
                ->addSelect('app');
            }

            if (!empty($sets['__embedded']['skills'])) {
                $qb->leftJoin('advert.skills', 'skill')
                ->addSelect('skill');
            }

            if (!empty($sets['__embedded']['image'])) {
                $qb->leftJoin('advert.image', 'img')
                ->addSelect('img');
            }

            if (!empty($sets['__embedded']['categories'])) {
                $qb->leftJoin('advert.categories', 'cat')
                ->addSelect('cat');
            }
        }

        return $qb->getQuery()->getArrayResult();
    }

    /**
     * Retourne le nombre d'Advert qu'il y a en BDD
     * @return mixed|\Doctrine\DBAL\Driver\Statement|NULL
     */
    public function getNbAdverts()
    {
        $qb = $this->createQueryBuilder('advert')->select('count(advert)');

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Retourne l'ensemble des annonces (Advert) qui sont dans l'une des catégories de $categoryNames
     * @param array $categoryNames
     * @return array Advert (Object)
     */
    public function getAdvertWithCategories(array $categoryNames)
    {
        $queryBuilder = $this->createQueryBuilder('a')
            ->innerJoin('a.categories', 'cat')
            ->addSelect('cat');

        $queryBuilder->where($queryBuilder->expr()->in('c.name', $categoryNames));

        return $queryBuilder
            ->getQuery()
            ->getResult();
    }

    public function getAdvertsWithoutApplications($days)
    {
        $qb = $this->createQueryBuilder('advert')
                ->leftJoin('advert.applications', 'app');

        $this->whereNoApplications($qb);

        $date = new \DateTime();
        $date->sub(new \DateInterval('P'.(int)$days.'D'));

        // c'est normalement ce qui est demandé, mais c'est pas logique car les annonces qui n'ont
        // jamais été modifiées, ne seront jamais supprimées
        // Je choisi de me baser sur la date de création sinon, il faut que je modifie mon entité Advert
        // Pour seter la date mise à jour en même temps que la date de création :/

        // $this->whereUpdated($qb, $date, self::CONDITION_GREATER_THAN);

        $this->whereDate($qb, $date, self::CONDITION_GREATER_THAN);

        return $qb->getQuery()->getResult();
    }

    public function whereNoApplications(QueryBuilder $qb)
    {
        $qb->andWhere('app.id IS NULL');
        // $qb->andWhere('app.id IS EMPTY'); // => Ne marche pas
        // $qb->andWhere('app.id = :empty')->setParameter('empty', serialize([])); // => Ne marche pas
    }

    public function whereDate(QueryBuilder $qb, $date, $condition = self::CONDITION_EQUAL_TO)
    {
        switch ($condition) {
            case self::CONDITION_EQUAL_TO:
                $qb->andWhere('advert.date = :date');
                break;
            case self::CONDITION_GREATER_THAN:
                $qb->andWhere('advert.date > :date');
                break;
            case self::CONDITION_LOWER_THAN:
                $qb->andWhere('advert.date < :date');
                break;
            case self::CONDITION_GREATER_THAN | self::CONDITION_EQUAL_TO:
                $qb->andWhere('advert.date >= :date');
                break;
            case self::CONDITION_LOWER_THAN | self::CONDITION_EQUAL_TO:
                $qb->andWhere('advert.date <= :date');
                break;
            default:
                $qb->andWhere('advert.date = :date');
        }

        $qb->setParameter('date', $date);
    }

    public function whereUpdated(QueryBuilder $qb, $date, $condition = self::CONDITION_EQUAL_TO)
    {
        switch ($condition) {
            case self::CONDITION_EQUAL_TO:
                $qb->andWhere('advert.updated = :updated');
                break;
            case self::CONDITION_GREATER_THAN:
                $qb->andWhere('advert.updated > :updated');
                break;
            case self::CONDITION_LOWER_THAN:
                $qb->andWhere('advert.updated < :updated');
                break;
            case self::CONDITION_GREATER_THAN | self::CONDITION_EQUAL_TO:
                $qb->andWhere('advert.updated >= :updated');
                break;
            case self::CONDITION_LOWER_THAN | self::CONDITION_EQUAL_TO:
                $qb->andWhere('advert.updated <= :updated');
                break;
            default:
                $qb->andWhere('advert.updated = :updated');
        }

        $qb->setParameter('updated', $date);
    }

    public function myFindAll()
    {

        // le 'advert' peut être remplacé par n'importe quelle autre chaine de caractères, c'est juste un alias
        // déjà, il est conseillé de mettre 'a' au lieu de 'advert', première lettre de l'entity
        $queryBuilder = $this->_em->createQueryBuilder()
            ->select('advert')
            ->from($this->_entityName, 'advert');

        // on peut aussi construire le queryBuilder comme ceci
        // $queryBuilder = $this->createQueryBuilder('advert');

        // on récupère la requete
        $query = $queryBuilder->getQuery();

        // On execute la requete pour recuperer le resultat
        $result = $query->getResult();
        // Ou bien
        //$result = $query->execute(null, $query::HYDRATE_OBJECT);

        return $result;

        // En very short requete :)
        // return $this->createQueryBuilder('advert')->getQuery()->getResult();
    }

    public function myFindOne($id)
    {
        $queryBuilder = $this->createQueryBuilder('advert');
        $queryBuilder->where('advert.id = :id')
        ->setParameter('id', (int) $id);

        return $queryBuilder->getQuery()->getResult();
    }

    public function myFindDQL($id)
    {
        $query = $this->_em->createQuery("SELECT a FROM TestsPlatfromBundle:Advert a WHERE id = :id");
        $query->setParameter('id', (int) $id);
        return $query->getSingleResult();
    }

    public function findByAuthorAndDate($author, $date)
    {
        $queryBuilder = $this->createQueryBuilder('a')
                        ->where('a.author = :author')
                        ->andWhere('a.date < :year');

        $queryBuilder->setParameter('author', $author);
        $queryBuilder->setParameter('date', $date);

        $queryBuilder->orderBy('a.date', 'DESC');

        return $queryBuilder->getQuery()->getResult();
    }

    public function whereCurrentYear(QueryBuilder $qb)
    {
        $qb->andWhere('a.date BETWEEN :start AND :end')
        ->setParameter('start', new \DateTime(date('Y').'-01-01'))
        ->setParameter('end', new \DateTime(date('Y').'-12-31'));
    }

    public function myFind($author)
    {
        $queryBuilder = $this->createQueryBuilder('a');
        $queryBuilder->where('a.author = :author')->setParameter('author', $author);
        $this->whereCurrentYear($queryBuilder);
        $queryBuilder->orderBy('a.date', 'DESC');
        return $queryBuilder->getQuery()->getResult();
    }
}
