<?php
/**
 * Chat repository.
 */
namespace Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Utils\Paginator;

/**
 * Class PostsRepository.
 */
class ChatRepository
{
    /**
     * Number of items per page.
     *
     * const int NUM_ITEMS
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
     * @param \Doctrine\DBAL\Connection $db
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }
    /**
     * Fetch all records.
     *
     * @return array Result
     */
    public function findAll($userId, $id)
    {
        $queryBuilder = $this->queryAll($userId, $id);

        return $queryBuilder->execute()->fetchAll();
    }

    /**
     * Get records paginated.
     *
     * @param int $page Current page number
     *
     * @return array Result
     */
    public function findAllPaginated($page = 1, $userId, $id)
    {
        $queryBuilder = $this->queryAll($userId, $id);
        $queryBuilder->setFirstResult(($page - 1) * static::NUM_ITEMS)
            ->setMaxResults(static::NUM_ITEMS);

        $pagesNumber = $this->countAllPages($userId, $id);


        $paginator = [
            'page' => ($page < 1 || $page > $pagesNumber) ? 1 : $page,
            'max_results' => static::NUM_ITEMS,
            'pages_number' => $pagesNumber,
            'data' => $queryBuilder->execute()->fetchAll(),
        ];

        return $paginator;
    }

    /**
     * Count all pages.
     *
     * @return int Result
     */
    protected function countAllPages( $userId, $id)
    {
        $pagesNumber = 1;

        $queryBuilder = $this->queryAll($userId, $id);
        $queryBuilder->select('COUNT(DISTINCT m.PK_time) AS total_results')
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
     * Save record.
     *
     * @param array $post Post
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function save($message, $userId, $id = 1)
    {
        $this->db->beginTransaction();


        $queryBuilder = $this->db->createQueryBuilder();
        $verifyUser =
            $queryBuilder->select('p.FK_idUsers')
                ->from('participants', 'p')
                ->innerJoin('p', 'messages', 'm', 'p.FK_idConversations = m.FK_idConversations')
                ->where(
                    'p.FK_idUsers = :userId',
                    'm.FK_idConversations = :id'
                )
                ->orderBy('m.PK_time', 'DESC')
                ->setParameters(array(':userId'=> $userId, ':id' => $id));

        try {
            $currentDateTime = new \DateTime();
            unset($message['messages']);

                // add new record
                $message['PK_time'] = $currentDateTime->format('Y-m-d H:i:s');
                $message['FK_idUsers'] = $userId;
                $message['FK_idConversations'] = 1 ;
                $this->db->insert('messages', $message);

            $this->db->commit();
        } catch (DBALException $e) {
            $this->db->rollBack();
            throw $e;
        }
    }


    /**
     * Query all records.
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder Result
     */
    protected function queryAll($userId, $id)
    {
        $queryBuilder = $this->db->createQueryBuilder();
        return $queryBuilder->select('m.PK_time', 'm.content', 'u.PK_idUsers', 'u.name', 'u.surname')
            ->from('messages', 'm')
            ->innerJoin('m', 'participants', 'p', 'p.FK_idConversations = m.FK_idConversations')
            ->innerJoin('m', 'users', 'u', 'u.PK_idUsers = m.FK_idUsers')
            ->where(
                'p.FK_idUsers = :userId',
                'm.FK_idConversations = :id'
            )
            ->orderBy('m.PK_time', 'DESC')
            ->setParameters(array(':userId'=> $userId, ':id' => $id));
    }
}
