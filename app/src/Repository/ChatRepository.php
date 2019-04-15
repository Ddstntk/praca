<?php
/**
 * PHP Version 5.6
 * Chat repository.
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
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ChatRepository
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
class ChatRepository
{
    /**
     * Number of items per page.
     *
     * Const int NUM_ITEMS
     */
    const NUM_ITEMS = 50;

    /**
     * Doctrine DBAL connection.
     *
     * @var \Doctrine\DBAL\Connection $db Database connection
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
     * @param User $userId Id
     * @param Chat $id     Id
     *
     * @return array
     */
    public function findAll($userId, $id)
    {
        $queryBuilder = $this->queryAll($userId, $id);

        return $queryBuilder->execute()->fetchAll();
    }

    /**
     * Find all chats
     *
     * @param User $userId user
     *
     * @return mixed
     */
    public function findAllChats($userId)
    {
        $queryBuilder = $this->db->createQueryBuilder();
        $usersList = [];
        $queryBuilder->select('p.FK_idConversations')
            ->from('participants', 'p')
            ->innerJoin('p', 'users', 'u', 'p.FK_idUsers = u.PK_idUsers')
            ->where(
                'p.FK_idUsers = :userId'
            )
            ->setParameters(array(':userId' => $userId));

        $convList = $queryBuilder->execute()->fetchAll();

        foreach ($convList as $key => $value) {
            unset($queryBuilder);
            $queryBuilder = $this->db->createQueryBuilder();

            $convId = $value['FK_idConversations'];
            $queryBuilder
                ->select('u.PK_idUsers', 'u.name', 'u.surname', 'p.FK_idConversations')
                ->from('users', 'u')
                ->innerJoin(
                    'u',
                    'participants',
                    'p',
                    'u.PK_idUsers = p.FK_idUsers'
                )
                ->where(
                    'p.FK_idConversations = :id'
                )
                ->setParameters(array(':id' => $convId));

            $usersList[$key] = $queryBuilder->execute()->fetchAll();
        }

        return $usersList;
    }

    /**
     * Get last chat id
     *
     * @param User $userId Id
     *
     * @return array
     */
    public function findLastChat($userId)
    {
        $queryBuilder = $this->db->createQueryBuilder();
        $usersList = [];
        $queryBuilder->select('m.FK_idConversations')
            ->from('messages', 'm')
            ->where(
                'm.FK_idUsers = :userId'
            )
            ->setParameters(array(':userId' => $userId))
            ->orderBy('m.PK_time', 'DESC')
            ->setMaxResults(1);

        $chatId = $queryBuilder->execute()->fetchAll();


        return $chatId;
    }

    /**
     * Find for uniqueness
     *
     * @param User $email email
     *
     * @return array
     */
    public function findForUniqueness($email)
    {
        $queryBuilder = $this->queryAll();
        $queryBuilder->where('u.email = :email')
            ->setParameter(':email', $email, \PDO::PARAM_STR);


        return $queryBuilder->execute()->fetchAll();
    }

    /**
     * Find for existence
     *
     * @param Chat $id Id
     *
     * @return array
     */
    public function findForExistence($id)
    {

        $queryBuilder = $this->db->createQueryBuilder();

        $queryBuilder->select('c.PK_idConversations')
            ->from('conversations', 'c')
            ->where('c.PK_idConversations = :id')
            ->setParameter(':id', $id);


        return $queryBuilder->execute()->fetchAll();
    }
    /**
     * Find all paginated
     *
     * @param User $userId Id
     * @param Chat $id     Id
     * @param int  $page   Page
     *
     * @return array
     */
    public function findAllPaginated($userId, $id, $page = 1)
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
     * Save record
     *
     * @param Message $message object
     * @param User    $userId  Id
     * @param Chat    $id      Id
     *
     * @throws DBALException
     * @throws \Doctrine\DBAL\ConnectionException
     *
     * @return nothing
     */
    public function save($message, $userId, $id)
    {
        $this->db->beginTransaction();
        $queryBuilder = $this->db->createQueryBuilder();
        $verifyUser = $queryBuilder->select('p.FK_idUsers')
            ->from('participants', 'p')
            ->innerJoin(
                'p',
                'messages',
                'm',
                'p.FK_idConversations = m.FK_idConversations'
            )
            ->where(
                'p.FK_idUsers = :userId',
                'm.FK_idConversations = :id'
            )
            ->orderBy('m.PK_time', 'DESC')
            ->setParameters(array(':userId' => $userId, ':id' => $id));

        try {
            if ($this->findForExistence($id)) {
                $currentDateTime = new \DateTime();
                unset($message['messages']);

                // add new record
                $message['PK_time'] = $currentDateTime->format('Y-m-d H:i:s');
                $message['FK_idUsers'] = $userId;
                $message['FK_idConversations'] = $id;
                $this->db->insert('messages', $message);

                $this->db->commit();
            } else {
                echo "Błędne ID konwersacji. Spróbuj wybrać ją ponownie z listy";
            }
        } catch (DBALException $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Add new chat
     *
     * @param Participants $participants Object
     * @param User         $userId       Id
     *
     * @throws DBALException
     * @throws \Doctrine\DBAL\
     *
     * @return nothing
     */
    public function addChat($participants, $userId)
    {
        //        $conn = $this->getDoctrine()->getConnection();
        $this->db->beginTransaction();
        $conversation = [];
        try {
            unset($conversation['conversations']);

            // add new record
            $conversation['FK_idUsers'] = $userId;
            $this->db->insert('conversations', $conversation);
            $lastInsert = $this->db->lastInsertId();
            $this->db->commit();
        } catch (DBALException $e) {
            $this->db->rollBack();
            throw $e;
        }

        $data['FK_idConversations'] = $lastInsert;

        $data['FK_idUsers'] = $userId;
        $this->db->insert('participants', $data);
        foreach ($participants['selectUsers'] as $p) {
            //            $this->db->beginTransaction();

            $data['FK_idUsers'] = $p;
            $this->db->insert('participants', $data);
            //            $this->db->commit();
        }
    }

    /**
     * Count all pages
     *
     * @param User $userId Id
     * @param Chat $id     Id
     *
     * @return float|int
     */
    protected function countAllPages($userId, $id)
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
     * Query all records
     *
     * @param User $userId Id
     * @param Chat $id     Id
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    protected function queryAll($userId, $id)
    {
        $queryBuilder = $this->db->createQueryBuilder();

        return $queryBuilder->select(
            'm.PK_time',
            'm.content',
            'u.PK_idUsers',
            'u.name',
            'u.surname'
        )
            ->from('messages', 'm')
            ->innerJoin(
                'm',
                'participants',
                'p',
                'p.FK_idConversations = m.FK_idConversations'
            )
            ->innerJoin(
                'm',
                'users',
                'u',
                'u.PK_idUsers = m.FK_idUsers'
            )
            ->where(
                'p.FK_idUsers = :userId',
                'm.FK_idConversations = :id'
            )
            ->orderBy('m.PK_time', 'DESC')
            ->setParameters(array(':userId' => $userId, ':id' => $id));
    }
}
