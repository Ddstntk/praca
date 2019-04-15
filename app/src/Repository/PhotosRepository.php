<?php
/**
 * PHP Version 5.6
 * PhotosRepository
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
 * Class PhotosRepository
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
class PhotosRepository
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
     * Save record
     *
     * @param Photo $photo  object
     * @param User  $userId Id
     *
     * @return int
     */
    public function save($photo, $userId)
    {
            unset($photo['id']);
            var_dump($photo);

            return $this->db->update('users', $photo, ['PK_idUsers' => $userId]);
    }
}
