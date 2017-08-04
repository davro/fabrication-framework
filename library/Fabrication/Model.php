<?php
namespace Fabrication;

use Fabrication\Model;
use Fabrication\Database;

/**
 * Fabrication Model Object.
 *
 * TODO Convert to mysqli or PDO (very soon!)
 * TODO SQL FOREIGN KEY constraint
 *
 * @author  David Stevens <davro@davro.net> <mail.davro@gmail.com>
 */
class Model
{
    /**
     * Owner.
     *
     * The owner of the model object.
     *
     * @var integer
     * @size 15
     * @flags NOT NULL
     */
    public $owner;
    
    /**
     * Created at timestamp.
     *
     * This timestamp will be generated on creation.
     *
     * @var integer
     * @size 20
     * @flags
     */
    public $createdAt;
    
    /**
     * Updated at timestamp.
     *
     * This timestamp will be generated and updated.
     *
     * @var integer
     * @size 20
     * @flags
     */
    public $updatedAt;
   
    // Static props
    public static $fields;
    
    public static $sql;
    public static $object;
    public static $objectId = false;
    
    public static $count = array();
    public static $order = array();
    public static $distinct = array();
    public static $operators = array();
    public static $clauses = array();
    
    /**
     *
     */
    public function __construct()
    {
    }
    
    /**
     * Model Object Actions.
     *
     * @var text
     */
    public function __get($name)
    {
        if (isset($this->$name)) {
            return $this->$name;
        }
        
        return false;
    }
    
    /**
     * Setter magic method.
     *
     * @param type $name
     * @param type $value
     * @return boolean
     */
    public function __set($name, $value)
    {
        // Register the key value pair.
        $this->$name = $value;
        
        return true;
    }
    
    /**
     * Retrive the object properties.
     *
     * @return  array
     */
    public function properties()
    {
        return get_object_vars(self::$object);
    }
    
    /**
     * Object methods.
     *
     * @return  array
     */
    public function methods()
    {
        return get_class_methods(self::$object);
    }
        
    /**
     * Create all the model object table from class paths
     *
     * This should iterate over the classpaths looking for model objects.
     * calling createObjectTable on each of the models.
     *
     *
     */
    public static function createTables()
    {
        // Fetch the cached class paths.
        $classPaths = Bootstrap::classPaths();

        // Sort in natural order.
        natsort($classPaths);

        foreach ($classPaths as $namespace => $path) {
            if (! get_parent_class($namespace)) {
                continue;
            }
            
            // exclude component packages for the moment.
            if (preg_match('/.phar/', $namespace)) {
                continue;
            }

            // exclude library caching for the moment.
            if (preg_match('/^Library/', $namespace)) {
                continue;
            }
            
            // classes that inherit the Library Model.
            if (get_parent_class($namespace) == 'Fabrication\Model') {
                $namespace::createTable();
            }
        }
    }
    
    /**
     *
     * @param type $param
     * @return type
     */
    public function listFields($table)
    {
        
        $link = Fabrication::getInstance('database')->connect('mysql');

        self::$fields = mysql_list_fields(PROJECT_DATABASE, $table, $link);
        return self::$fields;
    }
    
    /**
     *
     * @param type $param
     * @return type
     */
    public function numFields()
    {
        
        $link = Fabrication::getInstance('database')->connect('mysql');

        $numFields = mysql_num_fields(self::$fields);
        
        return $numFields;
    }

    /**
     * Create a database table from the native object variables.
     *
     * What is the difference between MYISAM and INNODB?
     *
     * InnoDB has row-level locking,
     * MyISAM can only do full table-level locking.
     *
     * InnoDB has better crash recovery.
     * MyISAM has FULLTEXT search indexes, InnoDB did not until MySQL 5.6 (Feb 2013).
     *
     * InnoDB implements transactions, foreign keys and relationship constraints,
     * MyISAM does not.
     *
     */
    public static function createTable()
    {
//        $engine = 'MyISAM';
        $engine = 'InnoDB';
        $link   = Fabrication::getInstance('database')->connect('mysql');
        
        $object         = self::create();
        $namespace      = get_class($object);
        $namespaceTable = str_replace(
            'applications-',
            '',
            strtolower(str_replace('\\', '-', $namespace))
        );
        
        Fabrication::log(__METHOD__, "Model :: namespace : $namespace ");
                        
        $propertiesReflections = array();
        $reflectionProperties  = Fabrication::mirror($object)->getProperties();
        foreach ($reflectionProperties as $property) {
            $propertyName    = $property->getName();
            $propertyComment = $property->getDocComment();
            if ($propertyComment) {
                $propertiesReflections[$propertyName] = $property;
            }
        }
        
        $columns='';
        $properties = $object->properties();
        foreach ($properties as $property => $type) {
//			if ($property == 'id') { continue; }
            $propertySize = false;
            
            if (isset($propertiesReflections[$property])) {
                if ($type == null) {
                    $propertyDocCommentVar = Fabrication::getDocCommentTag(
                        $propertiesReflections[$property]->getDocComment(),
                        'var'
                    );
                    if ($propertyDocCommentVar) {
                        $propertyType = $propertyDocCommentVar;
                    }
                }
                $propertySize  = Fabrication::getDocCommentTag(
                    $propertiesReflections[$property]->getDocComment(),
                    'size'
                );
                $propertyRole  = Fabrication::getDocCommentTag(
                    $propertiesReflections[$property]->getDocComment(),
                    'role'
                );
                $propertyFlags = Fabrication::getDocCommentTag(
                    $propertiesReflections[$property]->getDocComment(),
                    'flags'
                );
            }
                        
            switch ($propertyType) {
                case 'NULL':
                    $columns.="`{$property}` TEXT NOT NULL, ";
                    break;
                case 'array':
                    $columns.="`{$property}` TEXT NOT NULL, ";
                    break;
                case 'integer':
                    if ($propertySize) {
                        $columns.="`{$property}` int({$propertySize}) $propertyFlags, ";
                    } else {
                        $columns.="`{$property}` int(10) NOT NULL, ";
                    }
                    break;
                case 'boolean':
                    $columns.="`{$property}` TEXT NOT NULL, ";
                    break;
                case 'string':
                    if ($propertySize && $propertySize <= 101) {
                        $columns.="`{$property}` VARCHAR($propertySize) NOT NULL, ";
                    } elseif ($propertySize && $propertySize >= 101 && $propertySize <= 255) {
                        $columns.="`{$property}` CHAR($propertySize) NOT NULL, ";
                    } else {
                        $columns.="`{$property}` TEXT NOT NULL, ";
                    }
                    break;
                case 'object':
                    $columns.="`{$property}` TEXT NOT NULL, ";
                    break;
            }
        }
        
        if (method_exists($object, 'constraints')) {
            self::$sql = '
                CREATE TABLE IF NOT EXISTS `'.$namespaceTable.'` (
                    ' . $columns . '
                    PRIMARY KEY (`id`),
                    ' . $object->constraints() . '
                ) ENGINE=' . $engine . ' DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
            ';
        } else {
            self::$sql = '
                CREATE TABLE IF NOT EXISTS `'.$namespaceTable.'` (
                    '.$columns.'
                    PRIMARY KEY (`id`)
                ) ENGINE=' . $engine . ' DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
            ';
        }
        
        Fabrication::log(__METHOD__, "Model :: $property -> type:'$propertyType' size:'$propertySize' ");
        Fabrication::log(__METHOD__, "Model :: SQL : " . self::$sql);
        $resultModel = mysql_query(self::$sql, $link);
        
        if (!$resultModel) {
            Fabrication::log(__METHOD__, ' Model :: mysql error' . var_export(mysql_error(), true));
        }
        
        //
        // Fixtures check project data/$namespace
        //
        // Fabrication\User == data/fabrication/user
        //
        $namespaceFixturePath = PROJECT_ROOT_DIR . '/data/' . strtolower(str_replace('\\', '/', $namespace));
        if (is_dir($namespaceFixturePath)) {
            Fabrication::log(__METHOD__, "Data :: fixture : $namespace");
            $fixtures = scandir($namespaceFixturePath);
            
            foreach ($fixtures as $fixture) {
                if (in_array($fixture, array('.', '..'))) {
                    continue;
                }
                
                // SAVAGE (do some tests first, but for now)
                include $namespaceFixturePath . '/' . $fixture;
            }
        }
        
        // Fixtures run namespace fixture method.
        if (method_exists($namespace, 'fixture')) {
            Fabrication::log(__METHOD__, "Model :: fixture start : $namespace");
            $fixture = $namespace::fixture();
            Fabrication::log(__METHOD__, "Model :: fixture end : $namespace");
        }
    }
    
    /**
     * Model create
     *
     * @var text
     */
    public static function create($id = null)
    {
        $child = get_called_class();

        // Create the new model object.
        self::$object    = new $child;
        
        // reset
        self::$distinct  = array();
        self::$order     = array();
        self::$distinct  = array();
        self::$operators = array();
        self::$clauses   = array();
        
        // Set the insertion flag.
        if ($id != null) {
            self::$objectId   = (int ) $id;
            self::$object->id = self::$objectId;
        }

        return self::$object;
    }
    
    /**
     * Model Search
     *
     * @param   object  $model  // The object to save in the caching systems.
     */
    public static function search($query = '')
    {
        return self::create($query);
    }
    
    /**
     * Search operator.
     *
     * http://dev.mysql.com/doc/refman/5.0/en/operator-precedence.html
     * http://dev.mysql.com/doc/refman/5.0/en/non-typed-operators.html
     * http://dev.mysql.com/doc/refman/5.0/en/comparison-operators.html
     * http://dev.mysql.com/doc/refman/5.0/en/logical-operators.html
     * http://dev.mysql.com/doc/refman/5.0/en/assignment-operators.html
     *
     *
     * @param   string  $type       .
     * @param   string  $left       .
     * @param   string  $operator   .
     * @param   string  $right      .
     * @return  object
     */
    public static function operator($type, $left, $operator, $right)
    {
        self::$operators[] = array(
            'type'     => $type,
            'left'     => $left,
            'operator' => $operator,
            'right'    => $right
        );
        
        return self::$object;
    }
    
    /**
     * Search operator.
     *
     * http://dev.mysql.com/doc/refman/5.0/en/operator-precedence.html
     * http://dev.mysql.com/doc/refman/5.0/en/non-typed-operators.html
     * http://dev.mysql.com/doc/refman/5.0/en/comparison-operators.html
     * http://dev.mysql.com/doc/refman/5.0/en/logical-operators.html
     * http://dev.mysql.com/doc/refman/5.0/en/assignment-operators.html
     *
     *
     * @param   string  $type   .
     * @param   string  $name   .
     * @param   string  $value  .
     * @return  object
     */
    public static function clause($type, $name, $value = '')
    {
        self::$clauses[] = array(
            'type'  => $type,
            'name'  => $name,
            'value' => $value
        );
        
        return self::$object;
    }
    
    /**
     * Order operator.
     *
     *
     * @param   string  $key
     * @param   string  $value
     * @return  object
     */
    public static function order($key, $value)
    {
        return self::clause('ORDER BY', $key, $value);
    }
    
    /**
     * Search count.
     *
     * @param   mixed   $container
     * @return  boolean
     */
    public static function count($container)
    {
        self::$count[] = $container;
    }
    
    /**
     * Search distinct.
     *
     * @param   mixed   $container
     * @return  boolean
     */
    public static function distinct($container)
    {
        if (is_string($container)) {
            self::$distinct = array($container);
            
            return self::$object;
        }
        
        if (is_array($container)) {
            self::$distinct = $container;
            
            return self::$object;
        }
        
        return false;
    }
    
    /**
     * Save object to cache.
     *
     * @param   object  $model  // The object to save in the caching systems.
     */
    public static function save()
    {
        $link = Database::getResource();
        mysql_select_db(PROJECT_DATABASE, $link);
        
        $object = self::$object;
      
//		if (! self::$objectId) { // broken
        if ($object->id == 0) {
            $objectNamespace  = get_class($object);
            $objectProperties = $object->properties();
            $columnsNames     ='';
            $columnsValues    ='';
            if (count($objectProperties) > 0) {
                $user = \Fabrication\User::search()->
                    operator('WHERE', 'username', '=', \Fabrication\Authentication::getUsername())->
                object();
                
                foreach ($objectProperties as $key => $value) {
                    // remove class model props, this need to get from class dynamically really ...
//					if (in_array($key, array('owner', 'createdAt', 'updatedAt'))) {
//						continue;
//					}

                    $columnsNames.= "`$key` ,";
                    
                    // TODO check docblock for auto increment annotation.
                    if ($key == 'id') {
                        $columnsValues.= "NULL ,";
                    } elseif ($key == 'owner') {
                        if (is_object($user)) {
                            $value = $user->id;
                        }
                        $columnsValues.= "'$value' ,";
                    } elseif ($key == 'createdAt') {
                        $value = time();
                        $columnsValues.= "'$value' ,";
                    } else {
                        $columnsValues.= "'$value' ,";
                    }
                }
            }
            
            $columnsNamesInsert  = trim($columnsNames, ',');
            $columnsValuesInsert = trim($columnsValues, ',');
            
            $namespaceTable = str_replace('applications-', '', strtolower(str_replace('\\', '-', $objectNamespace)));
            
            $sql="
				INSERT INTO `" . PROJECT_DATABASE . "`.`{$namespaceTable}` 
				( $columnsNamesInsert )
				VALUES ($columnsValuesInsert);
			";
            
            $result = mysql_query($sql, $link);
            if (mysql_error()) {
                Fabrication::log(__METHOD__, 'Model :: INSERT : ' . var_export(mysql_error(), true));
                Fabrication::log(__METHOD__, 'Model :: sql : '    . var_export($sql, true));
            }
        } elseif (is_int(self::$objectId)) {
            $objectNamespace = get_class($object);
            $objectNamespaceTable = str_replace(
                'applications-',
                '',
                strtolower(str_replace('\\', '-', $objectNamespace))
            );
            $objectProperties = $object->properties();

            $columnsNameValue='';
            if (count($objectProperties) > 0) {
                foreach ($objectProperties as $key => $value) {
                    if (in_array($key, array('id', 'owner', 'createdAt'))) {
                        continue;
                    }
                    
                    if ($key == 'updatedAt') {
                        $value = time();
                        $columnsNameValue.= "`$key` = '$value',";
                    } else {
                        $columnsNameValue.= "`$key` = '$value',";
                    }
                }
            }
            $columnsNameValueUpdate = trim($columnsNameValue, ',');
            
            $sql="
				UPDATE `" . PROJECT_DATABASE . "`.`{$objectNamespaceTable}` 
				SET $columnsNameValueUpdate
				WHERE `$objectNamespaceTable`.`id` = '{$object->id}';
			";

            $result = mysql_query($sql, $link);
            if (mysql_error()) {
                Fabrication::log(__METHOD__, 'Model :: INSERT : ' . var_export(mysql_error(), true));
                Fabrication::log(__METHOD__, 'Model :: sql : '    . var_export($sql, true));
            }
            
        } else {
            Fabrication::log(__METHOD__, 'Model :: save : failed ');
        }
        
        return self::cache();
    }
    
    /**
     * Retrive objects from database.
     *
     * http://dev.mysql.com/doc/refman/5.5/en/select.html
     *
     * @return boolean
     */
    public function objects()
    {
        // Register fluent interface operators, clauses.
        Fabrication::register('model', self::$operators);
        Fabrication::register('model', self::$clauses);
        
        $query=' ';
        $selectColumns = '';
        
        foreach (self::$operators as $operator) {
            $query.= $operator['type'] . ' `' .
                $operator['left'] . '` ' .
                $operator['operator'] . ' \'' .
                $operator['right'] . '\' ';
        }
        
        foreach (self::$clauses as $clause) {
            $query.= $clause['type'] . ' ' . $clause['name'] . ' ' . $clause['value'] . ' ';
        }
        $query.= ';';
        
        $namespaceTableName = strtolower(str_replace('\\', '-', get_class(self::$object)));
        $namespaceTable = str_replace('applications-', '', $namespaceTableName);
        
        // Distinct example
        // SELECT DISTINCT `id`, `name` FROM `editor-model-data` LIMIT 0 , 30
        if (count(self::$count) > 0) {
            $selectColumns = 'COUNT(*) as count';
        } elseif (count(self::$distinct) == 0) {
            $selectColumns = '*';
        } else {
            $selectColumns.= 'DISTINCT ';
            foreach (self::$distinct as $column) {
                $selectColumns.= "`{$column}`, ";
            }
            $selectColumns = trim($selectColumns, ', ');
        }
        
        self::$sql = 'SELECT ' . $selectColumns . ' FROM `' . $namespaceTable . '` ' . $query;

        $link = Database::getResource();
        mysql_select_db(PROJECT_DATABASE, $link);
        $result = mysql_query(self::$sql, $link);
        if ($result) {
            $dataset = array();
            while ($row = mysql_fetch_assoc($result)) {
                // Create an object of the model.
                if (!isset($row['id'])) {
                    $model = self::create();
                } else {
                    $model = self::create($row['id']);
                }
                
                // Add the data values to the objects properties.
                foreach ($row as $key => $value) {
                    $model->$key = $value;
                }
                $dataset[] = $model;
            }
            return $dataset;
        }
        return false;
    }
    
    /**
     * Retrive an object model from the database.
     *
     * @return boolean
     */
    public function object()
    {
        $objects = $this->objects();
        
        if (count($objects) > 0) {
            return $objects[0];
        }
        
        return false;
    }
    
    /**
     * Query directly using native SQL.
     *
     * @param string $sql
     * @return boolean
     */
    public static function query($sql)
    {
        self::$sql = $sql;

        $link = Database::getResource();
        
        mysql_select_db(PROJECT_DATABASE, $link);
        $result = mysql_query(self::$sql, $link);
        
        if ($result) {
            $dataset = array();
            while ($row = mysql_fetch_assoc($result)) {
                // Create an object of the model.
                if (!isset($row['id'])) {
                    $model = self::create();
                } else {
                    $model = self::create($row['id']);
                }
                
                // Add the data values to the objects properties.
                foreach ($row as $key => $value) {
                    $model->$key = $value;
                }
                $dataset[] = $model;
            }
            return $dataset;
        }
        return false;
    }
    
    /**
     * Truncate a table name within the project database.
     *
     * @param string $table
     * @return resource
     */
    public static function truncate($table)
    {
        self::$sql = 'TRUNCATE  `' . $table . '`';
        
        $link = Database::getResource();
        
        mysql_select_db(PROJECT_DATABASE, $link);
        $result = mysql_query(self::$sql, $link);
        
        return $result;
    }
    
    /**
     * Drop a database table.
     *
     * @param   string  $table  The name of the table to drop.
     * @return  resource
     */
    public static function drop($table)
    {
        self::$sql = 'DROP TABLE `' . $table . '`';
        
        $link = Database::getResource();
        mysql_select_db(PROJECT_DATABASE, $link);
        
        $result = mysql_query(self::$sql, $link);
        return $result;
    }
    
    /**
     * The sql that was last run on the model object database.
     *
     * @return  string
     */
    public static function sql()
    {
        return self::$sql;
    }
    
    /**
     * Delete from database and object cache.
     *
     */
    public static function delete()
    {
        // TODO
    }
        
    /**
     * Cache object.
     *
     * @param   object  $model  // The object to save in the caching systems.
     */
    public static function cache()
    {
        if (!self::$object instanceof Model) {
            throw new Exception(
                'One does not simply cache anything. '.
                'It must have the electrons of "Library/Model" '.
                'running in though it\'s object properties.'
            );
        }
        
        //
        // Public session access this mean we cannot directly write out to
        // disk unless suexec is installed or the output directory has public
        // read/write premissions for all. The framework takes care of this
        // problem by providing a builtin processing queue where object can
        // be serialized json encoded and processed by one of the processing
        // threads, default configuration once per minute crontab.
        //
        // Where the projects queue will be dealt with by one of the
        // fabrication workers, and the observing caching systems will be
        // notified with the current data.
        //
        //
        // $to   = base64_encode(serialize($data));   // Save to database
        // $from = unserialize(base64_decode($data)); //Getting Save Format
        //
        if (! isset($_SESSION['username'])) {
//			throw new Exception('Authentication credentials missing username.');
        } else {
            // Database payload...
//			var_dump(__METHOD__);
//			var_dump(self::$object);
        }
    }
    
    /**
     * TODO
     */
    public function join()
    {
    }
}
