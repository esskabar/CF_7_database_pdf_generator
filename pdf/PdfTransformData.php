<?php

class PdfTransformData
{
    public static function call($fid, $columns, $data){
        //echo json_encode($data);die;

        $result = [];
        foreach($data as $_id => $row){
            $pdfRow = [];
            foreach ($columns as $columnName => $_){
                if (static::isValid($fid, $columnName)){
                    $pdfRow[] = $row[$columnName];
                }
            }
            $result[] = $pdfRow;
        }

        return $result;
    }

    protected static function isValid($fid, $columnName){
        $config = PdfConfig::call($fid);

        return !in_array($columnName ,$config['blackList']);
    }
}