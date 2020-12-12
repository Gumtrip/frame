<?php


namespace sf\db;
use PDO;

class Model implements ModelInterface
{
    public static $pdo;
    public static function getDb()
    {
        if (empty(static::$pdo)) {
            $host = '127.0.0.1';
            $database = 'sf';
            $username = 'homestead';
            $password = 'secret';
            static::$pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
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
    public static function findOne($condition)
    {
        $sql = 'select * from ' . static::tableName() . ' where ';
        // 取出condition中value作为参数
        $params = array_values($condition);
        $keys = [];
        foreach ($condition as $key => $value) {
            array_push($keys, "$key = ?");
        }
        // 拼接sql完成
        $sql .= implode(' and ', $keys);
        $stmt = static::getDb()->prepare($sql);
        $rs = $stmt->execute($params);

        if ($rs) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!empty($row)) {
                // 创建相应model的实例
                $model = new static();
                foreach ($row as $rowKey => $rowValue) {
                    // 给model的属性赋值
                    $model->$rowKey = $rowValue;
                }
                return $model;
            }
        }
        // 默认返回null
        return null;
    }
    public static function findAll($condition)
    {

    }
    public static function updateAll($condition, $attributes)
    {

    }
    public static function deleteAll($condition)
    {

    }
    public function insert()
    {

    }
    public function update()
    {

    }
    public function delete()
    {

    }

}