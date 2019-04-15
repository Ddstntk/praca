<?php
/**
 * Friends repository.
 */
namespace Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Utils\Paginator;

/**
 * Class ChayRepository.
 */
class FriendsRepository
{
    /**
     * Number of items per page.
     *
     * const int NUM_ITEMS
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
    public function findAll()
    {
        $queryBuilder = $this->queryAll();

        return $queryBuilder->execute()->fetchAll();
    }


    /**
     *  Add record
     *
     * @param int                          $userId current user id
     *
     * @param int friendId added friend id
     */
    public function addFriend($userId, $friendId)
    {

        try {
            $relation = [];
            unset($relation['FK_idUsersA']);

            $this->db->beginTransaction();

            // add new record
            $relation['FK_idUserA'] = $userId;
            $relation['FK_idUserB'] = $friendId;

            $this->db->insert('friends', $relation);

            $this->db->commit();

            $this->db->beginTransaction();

            $relation['FK_idUserB'] = $userId;
            $relation['FK_idUserA'] = $friendId;

            $this->db->insert('friends', $relation);

            $this->db->commit();
        } catch (DBALException $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * @param $userId
     * @param $friendId
     * @return int
     * @throws \Doctrine\DBAL\Exception\InvalidArgumentException
     */


    public function deleteFriend($userId, $friendId)
    {

        return $this->db->delete('si_tags', ['id' => $friend['id']]);
    }

    //    /**
    //     * Get records paginated.
    //     *
    //     * @param int $page Current page number
    //     *
    //     * @return array Result
    //     */
    //    public function findAllPaginated($page, $userId, $id)
    //    {
    //        $countQueryBuilder = $this->queryAll($page)
    //            ->select('COUNT(DISTINCT m.PK_time) AS total_results')
    //            ->where('m.FK_idConversations = 1')
    //            ->setMaxResults(100);
    //
    //        $queryBuilder = $this->db->createQueryBuilder();
    //        $result =
    //            $queryBuilder->select('m.PK_time', 'm.content', 'u.name', 'u.surname')
    //                ->from('messages', 'm')
    //                ->innerJoin('m', 'participants', 'p', 'p.FK_idConversations = m.FK_idConversations')
    //                ->innerJoin('m', 'users', 'u', 'u.PK_idUsers = m.FK_idUsers')
    //                ->where('p.FK_idUsers = :userId',
    //                    'm.FK_idConversations = :id')
    //                ->orderBy('m.PK_time', 'DESC')
    //                ->setParameters(array(':userId'=> $userId, ':id' => $id));
    //
    //
    //        $paginator = new Paginator($result, $countQueryBuilder);
    //        $paginator->setCurrentPage($page);
    //        $paginator->setMaxPerPage(static::NUM_ITEMS);
    //
    //        return $paginator->getCurrentPageResults();
    //    }

    //
    //    /**
    //     * Save record.
    //     *
    //     * @param array $post Post
    //     *
    //     * @throws \Doctrine\DBAL\DBALException
    //     */
    //    public function save($message, $userId, $id = 1)
    //    {
    //        $this->db->beginTransaction();
    //
    //
    //        $queryBuilder = $this->db->createQueryBuilder();
    //        $verifyUser =
    //            $queryBuilder->select('p.FK_idUsers')
    //                ->from('participants', 'p')
    //                ->innerJoin('p', 'messages', 'm', 'p.FK_idConversations = m.FK_idConversations')
    //                ->where('p.FK_idUsers = :userId',
    //                    'm.FK_idConversations = :id')
    //                ->orderBy('m.PK_time', 'DESC')
    //                ->setParameters(array(':userId'=> $userId, ':id' => $id));
    //
    //        try {
    //            $currentDateTime = new \DateTime();
    //            unset($message['messages']);
    //
    //            // add new record
    //            $message['PK_time'] = $currentDateTime->format('Y-m-d H:i:s');
    //            $message['FK_idUsers'] = $userId;
    //            $message['FK_idConversations'] = 1 ;
    //            $this->db->insert('messages', $message);
    //
    //            $this->db->commit();
    //        } catch (DBALException $e) {
    //            $this->db->rollBack();
    //            throw $e;
    //        }
    //    }

    //    /**
    //     * Remove record.
    //     *
    //     * @param array $post Post
    //     *
    //     * @throws \Doctrine\DBAL\DBALException
    //     *
    //     * @return boolean Result
    //     */
    //    public function delete($post)
    //    {
    //        $this->db->beginTransaction();
    //
    //        try {
    //            $this->removeLinkedTags($post['id']);
    //            $this->db->delete('posts', ['id' => $post['id']]);
    //            $this->db->commit();
    //        } catch (DBALException $e) {
    //            $this->db->rollBack();
    //            throw $e;
    //        }
    //    }

    /**
     * Query all records.
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder Result
     */
    protected function queryAll()
    {
        $queryBuilder = $this->db->createQueryBuilder();

        return $queryBuilder->select(
            'm.PK_time',
            'm.FK_idConversations',
            'm.FK_idUsers',
            'm.content'
        )->from('messages', 'm');
    }
}
