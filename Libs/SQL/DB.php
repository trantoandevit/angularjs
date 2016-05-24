<?php

namespace Libs\SQL;

require_once __DIR__ . '/adodb5/adodb.inc.php';

class DB
{

    /** @var static */
    static protected $_instance;

    /** @var ADOConnection */
    protected $_adoConnection;
    protected $cachedDate;

    static function config($type, $host, $user, $pass, $db, $debug)
    {
        static::$_instance = new static($type, $host, $user, $pass, $db, $debug);
    }

    /** @return \ADOConnection */
    static function getInstance()
    {
        return static::$_instance;
    }

    function __construct($type, $host, $user, $pass, $db, $debug)
    {
        $this->_adoConnection = NewADOConnection($type);
        $this->_adoConnection->debug = $debug;
        $status = $this->_adoConnection->Connect($host, $user, $pass, $db);
        if (!$status)
        {
            throw new Exception("Không kết nối được CSDL");
        }

        $this->_adoConnection->SetCharSet('utf8');
        $this->_adoConnection->SetFetchMode(ADODB_FETCH_ASSOC);
    }

    function __call($name, $arguments)
    {
        $callback = array($this->_adoConnection, $name);
        if (!is_callable($callback))
        {
            throw new Exception('Không có DB::' . $name);
        }
        return call_user_func_array($callback, $arguments);
    }

    function __get($name)
    {
        if (isset($this->_adoConnection->{$name}))
        {
            return $this->_adoConnection->{$name};
        }
    }

    function __set($name, $value)
    {
        $this->_adoConnection->{$name} = $value;
    }

    function getDate($cache = true)
    {
        if (!$this->cachedDate || !$cache)
        {
            $this->cachedDate = $this->GetOne("SELECT NOW()");
        }
        return $this->cachedDate;
    }

    /**
     * 
     * @param string $table Tên bảng
     * @param string $where Điều kiện
     * @param array $params Tham số
     * @return int Số bản ghi xóa hoặc FALSE
     */
    public function delete($table, $where, $params = array())
    {
        $sql = "DELETE FROM $table WHERE $where";
        $result = $this->Execute($sql, $params);
        if ($result == false)
        {
            return false;
        }
        else
        {
            return $this->Affected_Rows();
        }
    }

    /**
     * Insert vào CSDL
     * @param string $table Tên bảng
     * @param array $arr_data Mảng dữ liệu
     * @param string $pk Tên trường khóa chính
     * @return int ID vừa tạo hoặc FALSE nếu thất bại 
     * @throws InvalidArgumentException
     */
    public function insert($table, $arr_data, $pk = null)
    {
        if (!is_array($arr_data))
        {
            throw new InvalidArgumentException('$arr_data Phải là array $k=>$v');
        }
        if (empty($arr_data))
        {
            throw new InvalidArgumentException('$arr_data Không được empty()');
        }
        $sql = "INSERT INTO $table(" . implode(',', array_keys($arr_data)) . ") VALUES(?" . str_repeat(',?', count($arr_data) - 1) . ")";
        $this->Execute($sql, array_values($arr_data));
        return $this->Insert_ID($table, $pk);
    }

    /**
     * Insert nhiều row bằng một lệnh SQL
     * @param string $table
     * @param array $arr_data Mảng 2 chiều
     * @throws InvalidArgumentException
     */
    function insertMany($table, $arr_data)
    {
        if (!is_array($arr_data))
        {
            throw new InvalidArgumentException('$arr_data Phải là array $k=>$v');
        }
        if (empty($arr_data))
        {
            throw new InvalidArgumentException('$arr_data Không được empty()');
        }
        $first_row = $arr_data[0];
        $fields = implode(",", array_keys($first_row));
        $params = array();
        $count_fields = count($first_row);
        $sql = "INSERT INTO $table($fields) VALUES(?" . str_repeat(",?", $count_fields - 1) . ")";
        $sql .= str_repeat(",(?, " . str_repeat(",?", $count_fields - 1) . ")", count($arr_data) - 1);
        foreach ($arr_data as $row)
        {
            $params = array_merge($params, array_values($row));
        }
        $this->Execute($sql, $params);
    }

    /**
     * Update CSDL
     * @param string $table Tên bảng
     * @param array $arr_data Mảng dữ liệu
     * @param string $where Điều kiện. VD: '1=1'
     * @param array $params Mảng tham số
     * @return int Số bản ghi Update hoặc FALSE
     * @throws Exception
     */
    public function update($table, $arr_data, $where, $where_params = array())
    {
        if (!is_array($arr_data))
        {
            throw new InvalidArgumentException('$arr_data Phải là array $k=>$v');
        }
        if (empty($arr_data))
        {
            throw new InvalidArgumentException('$arr_data Không được empty()');
        }
        $sql = '';
        $params = array();
        foreach ($arr_data as $k => $v)
        {
            $sql .= strlen($sql) > 0 ? ",$k=?" : "UPDATE $table SET $k=?";
            array_push($params, $v);
        }
        $sql .= " WHERE $where";
        $result = $this->Execute($sql, array_merge($params, $where_params));
        if ($result == false)
        {
            return false;
        }
        else
        {
            return $this->Affected_Rows();
        }
    }

}
