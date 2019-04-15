<?php
/**
 * PHP Version 5.6
 * Posts repository.
 *
 * @category  Social_Network
 *
 * @author    Konrad Szewczuk <konrad3szewczuk@gmail.com>
 *
 * @copyright 2018 Konrad Szewczuk
 *
 * @license   https://opensource.org/licenses/MIT MIT license
 *
 * @link      cis.wzks.uj.edu.pl/~16_szewczuk
 */
namespace Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Utils\Paginator;
use Repository\FriendsRepository;

/**
 * Class PostsRepository
 *
 * @category  Social_Network
 *
 * @author    Konrad Szewczuk <konrad3szewczuk@gmail.com>
 *
 * @copyright 2018 Konrad Szewczuk
 *
 * @license   https://opensource.org/licenses/MIT MIT license
 *
 * @link      cis.wzks.uj.edu.pl/~16_szewczuk
 */
class PostsRepository
{
    /**
     * Number of items per page.
     *
     * Const int NUM_ITEMS
     */
    const NUM_ITEMS = 5;

    /**
     * Doctrine DBAL connection.
     *
     * @var \Doctrine\DBAL\Connection $db
     */
    protected $db;

    /**
     * PostsRepository constructor.
     *
     * @param \Doctrine\DBAL\Connection $db Database connection
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * Fetch all records
     *
     * @return array
     */
    public function findAll()
    {
        $queryBuilder = $this->queryAll();

        return $queryBuilder->execute()->fetchAll();
    }

    /**
     * Find all records paginated
     *
     * @param User $userId user
     * @param int  $page   Page
     *
     * @return array
     */
    public function findAllPaginated($userId, $page = 1)
    {
        $queryBuilder = $this
            ->queryAll()
            ->leftJoin('p', 'friends', 'f', 'f.FK_idUserA = p.FK_idUsers')
            ->where('p.visibility = 1')
            ->orWhere('f.FK_idUserB = :userId')
            ->orWhere('p.FK_idUsers = :userId')
            ->setParameter(':userId', $userId);
        $queryBuilder->setFirstResult(($page - 1) * static::NUM_ITEMS)
            ->setMaxResults(static::NUM_ITEMS);

        //        $friendsRepository->
        $pagesNumber = $this->countAllPages();

        $paginator = [
            'page' => ($page < 1 || $page > $pagesNumber) ? 1 : $page,
            'max_results' => static::NUM_ITEMS,
            'pages_number' => $pagesNumber,
            'data' => $queryBuilder->execute()->fetchAll(),
        ];

        return $paginator;
    }

    /**
     * Find one post by id
     *
     * @param Post $id Id
     *
     * @return array|mixed
     */
    public function findOneById($id)
    {
        $queryBuilder = $this->queryAll();
        $queryBuilder->where('p.PK_idPosts = :id')
            ->setParameter(':id', $id, \PDO::PARAM_INT);
        $result = $queryBuilder->execute()->fetch();

        return !$result ? [] : $result;
    }


    /**
     * Save record
     *
     * @param Post $post   Id
     * @param User $userId Id
     *
     * @throws DBALException
     * @throws \Doctrine\DBAL\ConnectionException
     *
     * @return nothing
     */
    public function save($post, $userId)
    {
        $this->db->beginTransaction();

        try {
            $currentDateTime = new \DateTime();
            $post['modified_at'] = $currentDateTime->format('Y-m-d H:i:s');
            unset($post['posts']);

            if (isset($post['id']) && ctype_digit((string) $post['id'])) {
                // update record
                $postId = $post['id'];
                unset($post['id']);
                $this->db->update('posts', $post, ['id' => $postId]);
            } else {
                // add new record
                $post['created_at'] = $currentDateTime->format('Y-m-d H:i:s');
                $post['FK_idUsers'] = $userId;
                $this->db->insert('posts', $post);
            }
            $this->db->commit();
        } catch (DBALException $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Delete record
     *
     * @param Post $id Id
     *
     * @throws DBALException
     * @throws \Doctrine\DBAL\ConnectionException
     *
     * @return nothing
     */
    public function delete($id)
    {

        try {
            $this->db->beginTransaction();
            $queryBuilder = $this->db->createQueryBuilder();

            $queryBuilder -> delete('posts')
                ->where('PK_idPosts = '.$id)
                ->execute();
            $this->db->commit();

            $this->db->beginTransaction();
            $queryBuilder = $this->db->createQueryBuilder();

            $queryBuilder -> delete('comments')
                ->where('FK_idPosts = '.$id)
                ->execute();
            $this->db->commit();
        } catch (DBALException $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Count all pages
     *
     * @return float|int
     */
    protected function countAllPages()
    {
        $pagesNumber = 1;

        $queryBuilder = $this->queryAll();
        $queryBuilder->select('COUNT(DISTINCT p.PK_idPosts) AS total_results')
            ->setMaxResults(1);

        $result = $queryBuilder->execute()->fetch();

        if ($result) {
            $pagesNumber =  ceil($result['total_results'] / static::NUM_ITEMS);
        } else {
            $pagesNumber = 1;
        }

        return $pagesNumber;
    }

    /**
     * Query all records
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    protected function queryAll()
    {
        $queryBuilder = $this->db->createQueryBuilder();

        return $queryBuilder->select(
            'DISTINCT p.PK_idPosts',
            'u.name',
            'u.surname',
            'p.FK_idUsers',
            'p.visibility',
            'p.content',
            'p.idMedia',
            'p.created_at',
            'p.modified_at'
        )->from('posts', 'p')
            ->innerJoin('p', 'users', 'u', 'p.FK_idUsers = u.PK_idUsers')
            ->orderBy('p.created_at', 'DESC');
    }
}
