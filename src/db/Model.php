<?php


namespace sf\db;
use PDO;
use sf\Sf;
class Model implements ModelInterface
{
    public static $pdo;
    public static function getDb()
    {
        if (empty(static::$pdo)) {

            static::$pdo = Sf::createObject('db')->getDb();
            static::$pdo->exec("set names 'utf8'");
        }

        return static::$pdo;
    }

    public static function tableName()
    {
        return get_called_class();
    }
    public static function primaryKey()
    {
        return ['id'];
    }
    public static function findOne($condition=null)
    {
        list($where, $params) = static::buildWhere($condition);
        $sql = 'select * from ' . static::tableName() . $where;
        // 取出condition中value作为参数
        $stmt = static::getDb()->prepare($sql);
        $rs = $stmt->execute($params);
        if(!empty($condition)){
            if ($rs) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!empty($row)) {
                    // 创建相应model的实例
                    return static::arr2Model($row);
                }
            }
        }

        // 默认返回null
        return null;
    }
    public static function findAll($condition =null)
    {
        list($where, $params) = static::buildWhere($condition);
        $sql = 'select * from ' . static::tableName() . $where;

        $stmt = static::getDb()->prepare($sql);
        $rs = $stmt->execute($params);
        $models = [];

        if ($rs) {
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                if (!empty($row)) {
                    $model = static::arr2Model($row);
                    array_push($models, $model);
                }
            }
        }

        return $models;
    }
    public static function updateAll($condition, $attributes)
    {
        $sql = 'update ' . static::tableName();
        $params = [];

        if (!empty($attributes)) {
            $sql .= ' set ';
            $params = array_values($attributes);
            $keys = [];
            foreach ($attributes as $key => $value) {
                array_push($keys, "$key = ?");
            }
            $sql .= implode(' , ', $keys);
        }

        list($where, $params) = static::buildWhere($condition, $params);
        $sql .= $where;

        $stmt = static::getDb()->prepare($sql);
        $execResult = $stmt->execute($params);
        if ($execResult) {
            // 获取更新的行数
            $execResult = $stmt->rowCount();
        }
        return $execResult;
    }
    public static function deleteAll($condition)
    {
        list($where, $params) = static::buildWhere($condition);
        $sql = 'delete from ' . static::tableName() . $where;

        $stmt = static::getDb()->prepare($sql);
        $execResult = $stmt->execute($params);
        if ($execResult) {
            // 获取删除的行数
            $execResult = $stmt->rowCount();
        }
        return $execResult;
    }
    public function insert()
    {
        $sql = 'insert into ' . static::tableName();
        $params = [];
        $keys = [];
        foreach ($this as $key => $value) {
            array_push($keys, $key);
            array_push($params, $value);
        }
        // 构建由？组成的数组，其个数与参数相等数相同
        $holders = array_fill(0, count($keys), '?');
        $sql .= ' (' . implode(' , ', $keys) . ') values ( ' . implode(' , ', $holders) . ')';

        $stmt = static::getDb()->prepare($sql);
        $execResult = $stmt->execute($params);
        // 将一些自增值赋回Model中
        $primaryKeys = static::primaryKey();
        foreach ($primaryKeys as $name) {
            // Get the primary key
            $lastId = static::getDb()->lastInsertId($name);
            $this->$name = (int) $lastId;
        }
        return $execResult;
    }
    public function update()
    {
        $primaryKeys = static::primaryKey();
        $condition = [];
        foreach ($primaryKeys as $name) {
            $condition[$name] = isset($this->$name) ? $this->$name : null;
        }

        $attributes = [];
        foreach ($this as $key => $value) {
            if (!in_array($key, $primaryKeys, true)) {
                $attributes[$key] = $value;
            }
        }

        return static::updateAll($condition, $attributes) !== false;
    }

    public function delete()
    {
        $primaryKeys = static::primaryKey();
        $condition = [];
        foreach ($primaryKeys as $name) {
            $condition[$name] = isset($this->$name) ? $this->$name : null;
        }

        return static::deleteAll($condition) !== false;
    }

    public static function buildWhere($condition, $params = null)
    {
        if (is_null($params)) {
            $params = [];
        }

        $where = '';
        if (!empty($condition)) {
            $where .= ' where ';
            $keys = [];
            foreach ($condition as $key => $value) {
                array_push($keys, "$key = ?");
                array_push($params, $value);
            }
            $where .= implode(' and ', $keys);
        }
        return [$where, $params];
    }

    /** 数据变对象
     * @param $row
     * @return static
     */
    public static function arr2Model($row)
    {
        $model = new static();
        foreach ($row as $rowKey => $rowValue) {
            $model->$rowKey = $rowValue;
        }
        return $model;
    }

}