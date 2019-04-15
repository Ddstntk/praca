<?php

/**
 * PHP Version 5.6
 * Custom User Implementation
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

namespace Provider;

use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

/**
 * Class CustomUser
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
final class CustomUser implements AdvancedUserInterface
{

    /**
     * Logged user id
     *
     * @var
     */
    private $id;
    /**
     * Logged user username
     *
     * @var
     */
    private $username;
    /**
     * Logged user password
     *
     * @var
     */
    private $password;
    /**
     * User status
     *
     * @var bool
     */
    private $enabled;
    /**
     * Account status
     *
     * @var bool
     */
    private $accountNonExpired;
    /**
     * Credentials status
     *
     * @var bool
     */
    private $credentialsNonExpired;
    /**
     * Account lock status
     *
     * @var bool
     */
    private $accountNonLocked;
    /**
     * Logged user roles
     *
     * @var array
     */
    private $roles;

    /**
     * CustomUser constructor.
     *
     * @param User  $id                    Id
     * @param User  $username              Username
     * @param User  $password              Password
     * @param array $roles                 Role
     * @param bool  $enabled               Is enabled
     * @param bool  $userNonExpired        Haven't expired
     * @param bool  $credentialsNonExpired Credentials not expired
     * @param bool  $userNonLocked         User non locekd
     */
    public function __construct($id, $username, $password, array $roles = array(), $enabled = true, $userNonExpired = true, $credentialsNonExpired = true, $userNonLocked = true)
    {
        if ('' === $username || null === $username) {
            throw new \InvalidArgumentException('The username cannot be empty.');
        }

        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->enabled = $enabled;
        $this->accountNonExpired = $userNonExpired;
        $this->credentialsNonExpired = $credentialsNonExpired;
        $this->accountNonLocked = $userNonLocked;
        $this->roles = $roles;
    }

    /**
     * To string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getUsername();
    }

    /**
     * Get id
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get roles
     *
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Get salt
     *
     * @return null|string|void
     */
    public function getSalt()
    {
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Check if account not expired
     *
     * @return bool
     */
    public function isAccountNonExpired()
    {
        return $this->accountNonExpired;
    }

    /**
     * Check if account not locked
     *
     * @return bool
     */
    public function isAccountNonLocked()
    {
        return $this->accountNonLocked;
    }

    /**
     * Check if credentials not expired
     *
     * @return bool
     */
    public function isCredentialsNonExpired()
    {
        return $this->credentialsNonExpired;
    }

    /**
     * Check if user is enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * Erase credentials
     * {@inheritdoc}
     *
     * @return nothing
     */
    public function eraseCredentials()
    {
    }
}
