<?php
/**
 * PHP Version 5.6
 * User repository
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
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Utils\Paginator;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * Class UserRepository
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
class UserRepository
{
    /**
     * Doctrine DBAL connection.
     *
     * @var \Doctrine\DBAL\Connection $db
     */
    protected $db;

    /**
     * UserRepository constructor.
     *
     * @param Connection $db Database connection
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * Get user by email
     *
     * @param Email $email email
     *
     * @return mixed
     */
    public function getIdByEmail($email)
    {
        $queryBuilder = $this->db->createQueryBuilder();
        $id = $queryBuilder ->select('u.PK_idUsers')
            ->from('users', 'u')
            ->where('u.email = :email')
            ->setParameter(':email', $email)
            ->execute()->fetch();

        return $id;
    }

    /**
     * Get user by id
     *
     * @param User $id Id
     *
     * @return array|mixed
     */
    public function getUserById($id)
    {
        $queryBuilder = $this->queryAll();
        $queryBuilder->where('u.PK_idUsers = :id')
            ->setParameter(':id', $id, \PDO::PARAM_INT);
        $result = $queryBuilder->execute()->fetch();

        return !$result ? [] : $result;
    }

    //    public function getUserByEmail($email)
    //    {
    //        $queryBuilder = $this->queryAll();
    //        $queryBuilder->where('u.email = :email')
    //            ->setParameter(':email', $email, \PDO::PARAM_INT);
    //        $result = $queryBuilder->execute()->fetch();
    //
    //        return !$result ? [] : $result;
    //    }

    /**
     * Load user by email
     *
     * @param User $email email
     *
     * @return array
     */
    public function loadUserByEmail($email)
    {
        try {
            $user = $this->getUserByEmail($email);

            if (!$user || !count($user)) {
                throw new UsernameNotFoundException(
                    sprintf('Username "%s" does not exist.', $email)
                );
            }

            $roles = $this->getUserRoles($user['PK_idUsers']);

            if (!$roles || !count($roles)) {
                throw new UsernameNotFoundException(
                    sprintf('Username "%s" does not exist.', $email)
                );
            }

            return [
                'id' => $user['PK_idUsers'],
                'email' => $user['email'],
                'password' => $user['password'],
                'roles' => $roles,
            ];
        } catch (DBALException $exception) {
            throw new UsernameNotFoundException(
                sprintf('Username "%s" does not exist.', $email)
            );
        } catch (UsernameNotFoundException $exception) {
            throw $exception;
        }
    }

    /**
     * Get user by Email
     *
     * @param User $email email
     *
     * @return array|mixed
     */
    public function getUserByEmail($email)
    {
        try {
            $queryBuilder = $this->db->createQueryBuilder();
            $queryBuilder->select('u.PK_idUsers', 'u.email', 'u.password')
                ->from('users', 'u')
                ->where('u.email = :email')
                ->setParameter(':email', $email, \PDO::PARAM_STR);

            return $queryBuilder->execute()->fetch();
        } catch (DBALException $exception) {
            return [];
        }
    }


    /**
     * Get user roles by userId
     *
     * @param User $id Id
     *
     * @return array
     */
    public function getUserRoles($id)
    {
        $roles = [];

        try {
            $queryBuilder = $this->db->createQueryBuilder();
            $queryBuilder->select('r.name')
                ->from('users', 'u')
                ->innerJoin('u', 'roles', 'r', 'u.role_id = r.id')
                ->where('u.PK_idUsers = :id')
                ->setParameter(':id', $id, \PDO::PARAM_INT);
            $result = $queryBuilder->execute()->fetchAll();

            if ($result) {
                $roles = array_column($result, 'name');
            }

            return $roles;
        } catch (DBALException $exception) {
            return $roles;
        }
    }

    /**
     * Save record
     *
     * @param User $user object
     *
     * @throws DBALException
     * @throws \Doctrine\DBAL\ConnectionException
     *
     * @return nothing
     */
    public function save($user)
    {
        $this->db->beginTransaction();

        try {
            if (isset($user['PK_idUsers'])
                && ctype_digit((string) $user['PK_idUsers'])
            ) {
                // update record
                $userId = $user['PK_idUsers'];
                unset($user['PK_idUsers']);
                $this->db
                    ->update('users', $user, ['PK_idUsers' => $userId]);
                $this->db
                    ->commit();
            } else {
                // add new user
                $user['birthDate'] = $user['birthDate'] ->format('Y-m-d');
                $this->db->insert('users', $user);
                $this->db->commit();
            }
        } catch (DBALException $e) {
            $this->db->rollBack();
            throw $e;
        }
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
     * Find all users paginated
     *
     * @param Repository $friendsRepository Friends
     * @param User       $userId            Id
     * @param int        $page              Page
     *
     * @return array
     */
    public function findAllPaginated($friendsRepository, $userId, $page = 1)
    {
        $countQueryBuilder = $this->findStrangers($friendsRepository, $userId)
            ->select('COUNT(DISTINCT k.PK_idUsers) AS total_results')
            ->setMaxResults(1);


        $paginator = new Paginator(
            $this
            ->findStrangers($friendsRepository, $userId),
            $countQueryBuilder
        );
        $paginator->setCurrentPage($page);
        $paginator->setMaxPerPage(100);

        return $paginator->getCurrentPageResults();
    }

    /**
     * Find all users
     *
     * @return array
     */
    public function findAll()
    {
        $queryBuilder = $this->queryAll();

        return $queryBuilder->execute()->fetchAll();
    }

    /**
     * Delete record
     *
     * @param User $id Id
     *
     * @throws \Doctrine\DBAL\ConnectionException
     *
     * @return nothing
     */
    public function delete($id)
    {

        $this->db->beginTransaction();
        $queryBuilder = $this->db->createQueryBuilder();

        $queryBuilder -> delete('friends')
            ->where('FK_idUserA = '.$id)
            ->orWhere('FK_idUserB = '.$id)
            ->execute();
        $this->db->commit();

        $this->db->beginTransaction();
        $queryBuilder = $this->db->createQueryBuilder();

        $queryBuilder -> delete('invitations')
            ->where('FK_idUserA = '.$id)
            ->orWhere('FK_idUserB = '.$id)
            ->execute();
        $this->db->commit();

        $this->db->beginTransaction();
        $queryBuilder = $this->db->createQueryBuilder();

        $queryBuilder -> delete('comments')
            ->where('FK_idUsers = '.$id)
            ->execute();
        $this->db->commit();


        $this->db->beginTransaction();
        $queryBuilder = $this->db->createQueryBuilder();

        $queryBuilder -> delete('posts')
            ->where('FK_idUsers = '.$id)
            ->execute();
        $this->db->commit();

        $this->db->beginTransaction();
        $queryBuilder = $this->db->createQueryBuilder();

        $queryBuilder -> delete('messages')
            ->where('FK_idUsers = '.$id)
            ->execute();
        $this->db->commit();

        $this->db->beginTransaction();
        $queryBuilder = $this->db->createQueryBuilder();

        $queryBuilder -> delete('participants')
            ->where('FK_idUsers = '.$id)
            ->execute();
        $this->db->commit();

        $this->db->beginTransaction();
        $queryBuilder = $this->db->createQueryBuilder();

        $queryBuilder -> delete('users')
            ->where('PK_idUsers = '.$id)
            ->execute();
        $this->db->commit();
    }

    /**
     * Change user access
     *
     * @param User $id   Id
     * @param User $role Role
     *
     * @return nothing
     */
    public function changeAccess($id, $role)
    {
        $this->db->beginTransaction();
        $queryBuilder = $this->db->createQueryBuilder();

        $queryBuilder -> update('users', 'u')
            ->set('u.role_id', $role)
            ->where('PK_idUsers = :id')
            ->setParameter(':id', $id)
            ->execute();
    }

    /**
     * Find all non-friends
     *
     * @param Repository $friendsRepository Friends
     * @param User       $userId            Id
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    protected function findStrangers($friendsRepository, $userId)
    {

        $queryBuilder = $this->db->createQueryBuilder();
        $friends = $friendsRepository->getFriendsIds($userId)->execute();

        return $queryBuilder->select(
            'k.PK_idUsers',
            'k.name',
            'k.surname',
            'k.photo',
            'k.role_id',
            'k.birthDate'
        )
            ->from('users', 'k')
            ->where($queryBuilder -> expr()->notIn('k.PK_idUsers', $friends))
            ->andWhere('k.PK_idUsers <> :userId')
            ->setParameters(array(':userId' => $userId, ':friendId' => 1));
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
            'u.PK_idUsers',
            'u.name',
            'u.surname',
            'u.email',
            'u.photo',
            'u.role_id',
            'u.birthDate'
        )
            ->from('users', 'u');
    }
}
