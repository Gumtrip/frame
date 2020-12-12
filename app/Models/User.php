<?php


namespace app\Models;
use sf\db\Model;

class User extends Model
{
    public static function tableName()
    {
        return 'user';
    }
}