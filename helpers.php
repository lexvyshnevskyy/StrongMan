<?php
/**
 * Created by PhpStorm.
 * User: test
 * Date: 17.12.19
 * Time: 20:23
 */

function plugin_activation(){
    return;
}

function plugin_deactivation(){
    return;
}

/**
 * Get all metadata for post and return only predefined array that Verified by filter
 * Check filter_var_array() in php manual
 * @param string $post post object
 * @param array $filter_args array
 * @return mixed
 */
function plugin_get_post_meta($post=object, $filter_args=array()){
    $metadata = get_metadata('post', $post->ID);
    $temp = array();
    foreach ($metadata as $key=>$value){
        $temp[$key]=$value[0];
    }
    return filter_var_array($temp, $filter_args);
}

/**
 * Return days difference
 * @param $metadata
 * @return int
 */
function plugin_get_date_diff($metadata){
     if (isset($metadata['date_start']) && isset($metadata['date_start'])){
         $date_f = date_create_from_format('Y-m-d', $metadata['date_start']);
         $date_t = date_create_from_format('Y-m-d', $metadata['date_end']);
         return date_diff(
             $date_f ? $date_f : new DateTime(),
             $date_t ? $date_t: new DateTime(),
             true
         )->days+1;
     }
     else
         return 0;
}

/**
 * Get array of dates from metadata
 * with fist and last date
 * @param $metadata
 * @return array of dates
 */
function plugin_get_date_period($metadata){
    if (isset($metadata['date_start']) && isset($metadata['date_start'])){
        try
        {
            $date_f = date_create_from_format('Y-m-d', $metadata['date_start']);
            $date_t = date_create_from_format('Y-m-d', $metadata['date_end']);
            $period = new DatePeriod(
                    $date_f ? $date_f : new DateTime(),
                    new DateInterval('P1D'),
                    $date_t ? $date_t->modify( '+1 day' ): new DateTime(),
                    0
                );
            $result = array();
            foreach ($period as $key=>$obj)
                $result[]=$obj->format('Y-m-d');
            return $result;
        }
        catch (Exception $e){
            return array();
        }
    }
    else
        return array();
}

/**
 * Wrapper for json_decode
 * @param $var
 * @return array|mixed|object
 */
function plugin_json_decode($var){
    return json_decode($var,true);
}

function validateArr($data = ARRAY_A, $debug=false){
    foreach ($data as $key=>$value) {
    	if ($debug)
    		var_dump($key,$value);
	    if ( empty( $value ) ) {
		    return false;
	    }
    }
    return true;
}


class helperPluginClass{
    private $assigned_info = array();
    private $judge_info = array();


    function __construct($obj = OBJECT)
    {   $assigned =$obj->assigned;
        $this->assigned_info = $assigned["assigned_info"];
        unset ($assigned["assigned_info"]);
        $this->judge_info = $assigned;
    }

    function getJudgeID($key){
        return $this->judge_info["judge_id"];
    }

    function getJudgeInfo(){
        return $this->assigned_info;
    }

    function getMetaID(){
        return $this->judge_info["meta_id"];
    }

    function is_selected($key,$idx,$isint){
        if ($this->assigned_info[$key][$idx]===false) return '';
        if (!array_key_exists($idx,$this->assigned_info[$key])) return '';
        if ((int)$this->assigned_info[$key][$idx]===$isint)
            return 'selected';
        return '';
    }

    function isPreviousAvailable($key,$idx){
//        if ($idx === 0) return true;
//        foreach ($this->assigned_info[$key] as $index=>$value){
//            if ($index === $idx-1)
//                if (is_int($this->assigned_info[$key][$index]))
//                    return false;
//        }
        return true;
    }

    function getCurrentClass($key,$idx){
        if ($this->assigned_info[$key][$idx] === false)
            return 'disabled'; // red
        if (!$this->isPreviousAvailable($key,$idx))
            return 'disabled';
        if ((!$this->assigned_info[$key])||(!array_key_exists($idx,$this->assigned_info[$key]))) return '';
        if (is_int($this->assigned_info[$key][$idx]))
            return'choosen'; // blue

        #TODO
        //return 'not-active'; //recommended

    }

    function getExcelColor($key,$idx){
        switch ($this->getCurrentClass($key,$idx)){
            case 'disabled':return 'FF0000';break;
            case 'choosen': return '0000FF';break;
            default: return 'FFFFFF';
        }
    }
}