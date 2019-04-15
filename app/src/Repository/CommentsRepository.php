<?php
/**
 * PHP Version 5.6
 * Comments repository.
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

/**
 * Class CommentsRepository
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
class CommentsRepository
{
    /**
     * Number of items per page.
     *
     * Const int NUM_ITEMS
     */
    const NUM_ITEMS = 100;

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
     * Find all paginated
     *
     * @param Post $postId Id
     * @param int  $page   Page
     *
     * @return array
     */
    public function findAllPaginated($postId, $page = 1)
    {
        $queryBuilder = $this->queryAll()
            ->innerJoin('c', 'users', 'u', 'c.FK_idUsers = u.PK_idUsers')
            ->where("c.FK_idPosts = :postId")
            ->setParameter(':postId', $postId);

        $queryBuilder->setFirstResult(($page - 1) * static::NUM_ITEMS)
            ->setMaxResults(static::NUM_ITEMS);

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
     * Save record
     *
     * @param Comment $comment object
     * @param Post    $id      Id
     * @param User    $userId  Id
     *
     * @throws DBALException
     * @throws \Doctrine\DBAL\ConnectionException
     *
     * @return nothing
     */
    public function save($comment, $id, $userId)
    {
        $this->db->beginTransaction();

        try {
            $currentDateTime = new \DateTime();
            unset($comment['comments']);


                // add new record
            $comment['created_at'] = $currentDateTime->format('Y-m-d H:i:s');
            $comment['FK_idPosts'] = $id;
            $comment['FK_idUsers'] = $userId;
            $this->db->insert('comments', $comment);

            $this->db->commit();
        } catch (DBALException $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Remove record
     *
     * @param Comment $id Id
     *
     * @throws DBALException
     * @throws \Doctrine\DBAL\ConnectionException
     *
     * @return nothing
     */
    public function delete($id)
    {
        $this->db->beginTransaction();

        try {
            $this->db->delete('comments', ['PK_idComments' => $id]);
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
        $queryBuilder->select('COUNT(DISTINCT c.PK_idComments) AS total_results')
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
     * Query all records.
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder Result
     */
    protected function queryAll()
    {
        $queryBuilder = $this->db->createQueryBuilder();

        return $queryBuilder->select(
            'u.name',
            'u.surname',
            'c.PK_idComments',
            'c.FK_idPosts',
            'c.FK_idUsers',
            'c.content',
            'c.created_at'
        )->from('comments', 'c')
            ->orderBy('c.created_at', 'DESC');
    }
}
