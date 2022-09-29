<?php

namespace Phplite\Database;

use PDO;
use Exception;
use PDOException;
use Phplite\File\File;
use Phplite\Http\Request;
use Phplite\Url\Url;

class Database
{

    protected static $instance;
    protected static $connection;
    protected static $query;
    protected static $setter;
    protected static $select;
    protected static $table;
    protected static $join;
    protected static $where;
    protected static $group_by;
    protected static $order_by;
    protected static $limit;
    protected static $offset;
    protected static $having;
    protected static $where_binding = [];
    protected static $having_binding = [];
    protected static $binding = [];


    private static function connect()
    {
        if (!static::$connection) {
            $database_data = File::require_file('config/database.php');
            extract($database_data);

            $dsn = 'mysql:dbname=' . $database . ';host=' . $host . '';

            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                PDO::ATTR_PERSISTENT => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "set NAMES " . $charset . " COLLATE " . $collation
            ];
            //connection
            try {
                static::$connection = new PDO($dsn, $username, $password, $options);
            } catch (PDOException $e) {
                throw new Exception($e->getMessage());
            }
        }
    }

    private static function instance()
    {
        static::connect();
        if (!self::$instance) {
            self::$instance = new Database;
        }
        return self::$instance;
    }

    public static function query($query = null)
    {
        static::instance();
        if ($query == null) {

            if (!static::$table) {
                throw new Exception("Unkown table");
            }
            //SELECT * FROM user JOIN roles roles.id =user.role_id where id>1 Having id>1 limit 1 offset 2
            $query = "SELECT ";
            $query .= static::$select ?: "*";
            $query .= " FROM " . static::$table . " ";
            $query .= static::$join . " ";
            $query .= static::$where . " ";
            $query .= static::$group_by . " ";
            $query .= static::$having . " ";
            $query .= static::$order_by . " ";
            $query .= static::$limit . " ";
            $query .= static::$offset . " ";
        }

        static::$query = $query;
        static::$binding = array_merge(static::$where_binding, static::$having_binding);

        return static::instance();
    }

    //select data from tabel
    public static function select()
    {
        $select = func_get_args();
        $select = implode(',', $select);
        static::$select = $select;

        return static::instance();
    }

    public static function table($table)
    {
        static::$table = $table;

        return static::instance();
    }

    public static function join($table, $first, $operator, $second, $type = "INNER")
    {
        static::$join .= " " . $type . " JOIN " . $table . " ON " . $first . $operator . $second . " ";
        return static::instance();
    }

    public static function rightJoin($table, $first, $operator, $second)
    {
        static::join($table, $first, $operator, $second, "RIGHT");
        return static::instance();
    }

    public static function leftJoin($table, $first, $operator, $second)
    {
        static::join($table, $first, $operator, $second, "LEFT");
        return static::instance();
    }

    public static function where($column, $operator, $value, $type = null)
    {
        //where `username` = "ali" AND id > 4
        $where = '`' . $column . '`' . $operator . ' ? ';
        if (!static::$where) {
            $statement = " WHERE " . $where;
        } else {
            if ($type == null) {
                $statement = " AND " . $where;
            } else {
                $statement = " " . $type . " " . $where;
            }
        }
        static::$where .= $statement;
        static::$where_binding[] = htmlspecialchars($value);

        return  static::instance();
    }

    public static function orWhere($column, $operator, $value)
    {
        static::where($column, $operator, $value, "OR");
        return  static::instance();
    }

    public static function groupBy()
    {
        $group_by = func_get_args();
        $group_by = " GROUP BY "  . implode(',', $group_by) . " ";
        static::$group_by = $group_by;

        return static::instance();
    }

    public static function having($column, $operator, $value)
    {
        //having `username` = "ali" AND id > 4
        $having = '`' . $column . '`' . $operator . ' ? ';
        if (!static::$having) {
            $statement = " HAVING " . $having;
        } else {
            $statement = " AND " . $having;
        }
        static::$having .= $statement;
        static::$having_binding[] = htmlspecialchars($value);

        return  static::instance();
    }

    public static function orderBy($column, $type = null)
    {
        //ORDER BY Country DESC;
        $sep = static::$order_by ? " , " : " ORDER BY ";
        $type = strtoupper($type);
        $type = ($type != null && in_array($type, ['ASC', 'DESC'])) ? $type : "ASC";
        $statement = $sep . $column . " " . $type . " ";
        static::$order_by = $statement;

        return  static::instance();
    }

    public static function limit($limit)
    {
        static::$limit = " LIMIT " . $limit . " ";

        return  static::instance();
    }

    public static function offset($offset)
    {
        static::$offset = " OFFSET " . $offset . " ";

        return  static::instance();
    }

    private static function fetchExecute()
    {
        static::query(static::$query);
        $query = trim(static::$query, ' ');
        $data = static::$connection->prepare($query);
        $data->execute(static::$binding);

        static::clear();
        return $data;
    }

    public static function get()
    {
        $data = static::fetchExecute();
        $result = $data->fetchAll();
        return $result;
    }


    public static function first()
    {
        $data = static::fetchExecute();
        $result = $data->fetch();
        return $result;
    }

    private static function Execute(array $data, $query, $where = null)
    {
        static::instance();
        if (!static::$table) {
            throw new Exception("Unkown table");
        }

        foreach ($data as $key => $value) {
            static::$setter .= '`' . $key . '` = ? , ';
            static::$binding[] = filter_var($value, FILTER_SANITIZE_STRING);
        }

        static::$setter = trim(static::$setter, ', ');
        $query .= static::$setter;
        $query .= $where != null ? static::$where . " " : '';

        static::$binding = $where != null ? array_merge(static::$binding, static::$where_binding) : static::$binding;

        $data = static::$connection->prepare($query);
        $data->execute(static::$binding);

        static::clear();
    }

    //insert
    public static function insert($data)
    {
        $query = "INSERT INTO " . static::$table . " SET ";
        static::Execute($data, $query);
        $object_id = static::$connection->lastInsertId();
        $object = static::table(static::$table)->where('id', '=', $object_id)->first();
        return $object;
    }

    public static function update($data)
    {
        $query = "UPDATE " .  static::$table . " SET ";
        static::Execute($data, $query, true);

        return True;
    }

    public static function delete()
    {
        $query = "DELETE FROM " .  static::$table;
        static::Execute([], $query, true);

        return True;
    }

    public  static function paginate($items_per_page = 15)
    {
        static::query(static::$query);
        $query = trim(static::$query, '  ');
        $data = static::$connection->prepare($query);
        $data->execute();
        $pages = ceil($data->rowcount() / $items_per_page);

        //www.google.com/products?page=1
        $page = Request::get('page');
        $current_page = (!is_numeric($page) || $page < 1) ? "1" :  $page;

        //counts the number of items in each page
        $offset = ($current_page - 1) * $items_per_page;

        //"SELECT * FROM users LIMIT 2   OFFSET 16"
        static::limit($items_per_page);
        static::offset($offset);
        static::query();

        $data = static::fetchExecute();
        $result = $data->fetchAll();

        $response = [
            'data' =>  $result,
            'items_per_page' => $items_per_page,
            'pages' => $pages,
            'current_page' => $current_page
        ];

        return $response;
    }

    public static function links($current_page, $pages)
    {
        $links = '';
        $from = $current_page - 2;
        $to = $current_page + 2;
        if ($from < 2) {
            $from = 2;
            $to = $from + 4;
        }
        if ($to >= $pages) {
            $diff = $to - $pages + 1;
            $from = ($from > 2) ? $from - $diff : 2;
            $to = $pages - 1;
        }
        if ($from < 2) {
            $from = 1;
        }
        if ($to >= $pages) {
            $fto = $pages - 1;
        }

        if ($pages > 1) {
            $links .= "<ul class ='pagination'>";

            $full_link = Url::path(Request::full_url());
            $full_link = preg_replace('/\?page=(.*)/', '', $full_link);
            $full_link = preg_replace('/\&page=(.*)/', '', $full_link);

            $current_page_active = $current_page == 1 ? 'active' : '';
            $href = strpos($full_link, '?') ? ($full_link . '&page=1') : ($full_link . '?page=1');
            $links .= "<li class='link' $current_page_active>  <a href ='$href'>First </a> </li> ";

            for ($i = $from; $i <= $to; $i++) {
                $current_page_active = $current_page == $i ? 'active' : '';
                $href = strpos($full_link, '?') ? ($full_link . '&page=' . $i) : ($full_link . '?page=' . $i);
                $links .= "<li class='link' $current_page_active>  <a href ='$href'> $i</a> </li> ";
            }
            if ($pages > 1) {
                $current_page_active = $current_page == $pages ? 'active' : '';
                $href = strpos($full_link, '?') ? ($full_link . '&page=' . $pages) : ($full_link . '?page=' . $pages);
                $links .= "<li class='link' $current_page_active>  <a href ='$href'>Last </a> </li> ";
            }

            print_r($links);
        }
    }
    private static function clear()
    {
        static::$instance;
        static::$query;
        static::$select;
        // static::$table;
        static::$join;
        static::$where;
        static::$group_by;
        static::$order_by;
        static::$limit;
        static::$offset;
        static::$having;
        static::$where_binding = [];
        static::$having_binding = [];
        $binding = [];
    }
}