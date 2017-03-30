<?php

namespace Fabrication;

use Fabrication\Model;

/**
 * Fabrication Framework User Model
 *
 * @role = rbac
 *
 */
class User extends Model
{
   
    /**
     * The Service ID.
     *
     * @var integer
     * @size 15
     * @flags NOT NULL AUTO_INCREMENT
     */
    public $id = 0;
    
    /**
     * Username.
     *
     * @var string
     * @size 20
     */
    public $username= '';
    
    /**
     * Password.
     *
     * @var string
     * @size 50
     * @role password
     */
    public $password;

    /**
     * First name of the user.
     *
     * @var string
     * @size 50
     */
    public $firstname;
    
    /**
     * Last name of the user.
     *
     * @var string
     * @size 50
     */
    public $lastname;
    
    /**
     * Email of the user.
     *
     * @var string
     * @size 60
     */
    public $email;

    /**
     * Facebook account.
     *
     * @var string
     * @size 60
     */
    public $facebook;

    /**
     * YouTube account.
     *
     * @var string
     * @size 60
     */
    public $youtube;

    /**
     * Twitter account.
     *
     * @var string
     * @size 60
     */
    public $twitter;

    /**
     * Instagram account.
     *
     * @var string
     * @size 60
     */
    public $instagram;
    
    /**
     * Role of the user.
     *
     * @var string
     * @size 50
     * @role system
     */
    public $role;
    
    /**
     * Activation flag.
     * No Null !!
     *
     * @var integer
     * @size 1
     * @default 0
     * @flags NOT NULL
     * @role system
     */
    public $active;
    
    public static $roleSystem = 'root';
    
    public function constraints()
    {
//        $this->addConstraint();
        return '
            UNIQUE (username),
            UNIQUE (email)
        ';
        
        // Examples
//        return 'UNIQUE (username,email)';
//        return 'CONSTRAINT usernameEmail UNIQUE (username,email)';
    }
    
    public function setPassword($password)
    {
        
        $this->password = md5($password);
    }
    
    public function setRole($role)
    {
        
        if ($role == self::$roleSystem) {
            $this->role = self::$roleSystem;
            
            return true;
        }
        
        return;
    }
    
    /**
     * Fixtures for the Users Model.
     *
     */
    public static function fixture()
    {
    }
}
