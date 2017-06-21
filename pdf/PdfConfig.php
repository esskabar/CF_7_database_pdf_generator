<?php

class PdfConfig
{
    /**
     * @param $fid
     * @return array
     * => [ 'blackList' => [ 'g-recaptcha-response', 'some-column-name-to-hide' ] ]
     */
    public static function call($fid){
        $result = static::configForFidDefault();

        /**
         * White list for PDF TABLES
         * Missing $fid will NOT exports to PDF!
         */
        $tablesConfig = array(
            '131' => static::configForFidDefault(),
            '156' => static::configForFidDefault(),
            '133' => static::configForFidDefault(),
            '124' => static::configForFidDefault(),
            '132' => static::configForFidDefault(),
            '470' => static::configForFidDefault(),
            '128' => static::configForFid128()
        );

        if (empty($tablesConfig[$fid])){
            return null;
        }

        $selected = $tablesConfig[$fid];
        $result['blackList'] = array_merge($result['blackList'], $selected['blackList']);

        return $result;
    }

    /**
     * Define columns config
     * @return array
     */
    protected static function configForFidDefault(){
        return array(
            'blackList' => array('g-recaptcha-response')
        );
    }

    protected static function configForFid52(){
        return array(
            'blackList' => array('e_id'),
        );
    }
    protected static function configForFid128(){
        return array(
            'blackList' => array('Special-skills'),
        );
    }

}