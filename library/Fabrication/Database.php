<?php

namespace Fabrication;

/*
 * This file is part of the fabrication framework.
 * 
 * David Stevens <mail.davro@gmail.com> <davro@davro.net>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this version of the source code.
 */

/**
 * Database Abstraction Layer
 *
 * Object, event, about which data is stored.
 *
 * The database layer for the FabricationEngine is Sqlite
 * But you can easily setup with Mysql.
 *
 * sqlite-vs-mysql
 * MySQL is doing on writes and reads on multiple files with very optimized algorithms.
 * SQLite you are the process, and because by default SQLite is much faster
 * (being almost the same as the native read/write to file)
 * if you are smart and implement only what you need (using multiple files),
 * you will get better performance and in the end better app.
 * MySQL is an easy way and it has all the features, but 70% of them you don't need, and they slow it down.
 * However, it has all what enterprise needs, and you know it will be always up to the task.
 *
 *
 * Reads = Memcache(memory) - DataEntity(filesystem)
 *
 * Write = Framework takes care or a few steps after writing
 *
 *  1)  Database() insert, update.
 *  2)  Framework create/load DataEntity
 *  3)  Framework updates Memcache with new/updated entity.
 *
 * This process abstracts the database away from the userspace and should
 * maintain an efficient read/write balance, the only hit to the user is going
 * to be updating the DataEntity in memory, but this could be managed.
 *
 * READ
 * DataEntity -> Load               // Data will be taken from Entity object.
 * DataEntity -> Memcache <- Load   // Memcache running Data taken from memory.
 *
 * WRITE
 * Database -> DataEntity           // Data create/load written to Entity object
 * DataEntity -> Memcache           // Entity object should write out to Memory
 *
 *
 * @package         Library
 * @subpackage  Database
 * @author      David Stevens <mail.davro@gmail.com>
 *
 */
class Database
{
   
    public static $link;
    
    public static $username = 'root';
    
    public static $password = '';
    
    public static $database = 'project-davro.net';
    
    public static $selectType = 'mysql';
    
    public static $selectedDatabase = '';
    
    /**
     * Connection builder.
     *
     * @param string $type The connection type.
     * @return mixed
     */
    public static function connect($type = 'sqlite')
    {
        self::$selectType = $type;
        
        //
        // Eventually change for Database Adaptor classes, sqlite, mysql, postgre ... 
        //
        switch (self::$selectType) {
            case 'sqlite':
                $path = '/tmp/fabrication.' . PROJECT_HOSTNAME . '.sqlite';
                
                if (!file_exists($path) && !Fabrication::isCli()) {
                    return false;
                }
                
                self::$link = new \SQLite3($path);
                
                return self::$link;
            break;
        
            case 'mysql':
                $configuration = Configuration::get('database');
                $hostname = !empty($configuration['hostname']) ? 
                    $configuration['hostname'] : self::$password;
                
                $username = !empty($configuration['username']) ? 
                    $configuration['username'] : self::$password;
                
                $password = !empty($configuration['password']) ? 
                    $configuration['password'] : self::$password;
                
                $database = !empty($configuration['database']) ? 
                    $configuration['database'] : self::$database;
                
                if (!function_exists('\mysql_connect')) {
                    die('Framework Database missing driver!');
                    // @TODO trigger an installation database event.
                } else {
                    // suppress error deprected, TODO must replace with PDO as default!!
                    self::$link = @\mysql_connect($hostname, $username, $password);
                    
                    if (! self::$link) {
                        die('Framework Database missing connection!');
                        // @TODO trigger an installation database connection event.
                    }
                }

                if (PROJECT_DATABASE) {
                    self::selectDB(PROJECT_DATABASE);
                }
                
                if (!self::$link) {
                    Fabrication::log(__METHOD__, 'Database MySQL Error ' . var_export(mysql_error(), true));
                }
                
                return self::$link;
                
            break;
        }
        
        return false;
    }
    
    public static function getResource()
    {
        if (is_resource(self::$link)) {
            return self::$link;
        }
        
        return self::connect(self::$selectType);
    }
    
    public static function query($sql)
    {
        $result = mysql_query($sql, self::$link);
        
        return $result;
    }
    
    public static function selectDB($database = '')
    {
        if (! $database) {
            self::$selectedDatabase = mysql_select_db(self::$database, self::$link);
        } else {
            self::$selectedDatabase = mysql_select_db($database, self::$link);
        }
        
        return self::$selectedDatabase;
    }
    
    public static function numRows($result)
    {
        $numRows = mysql_num_rows($result);
        
        return $numRows;
    }
    
    public static function fetch($result, $type = 'assoc')
    {
        
        $dataset='';
        switch ($type) {
            case 'assoc':
                $dataset = mysql_fetch_assoc($result);
                break;
        }
        
        return $dataset;
    }
}
