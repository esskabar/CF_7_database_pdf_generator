<?php

class PdfHeader
{
    public static function call($fid, $columns){
        $result = [];
        foreach ($columns as $columnName){
            if (static::isValid($fid, $columnName)){
                $result[] = $columnName;
            }
        }

        return $result;
    }

    protected static function isValid($fid, $columnName){
        $config = PdfConfig::call($fid);

        return !in_array($columnName ,$config['blackList']);
    }
}