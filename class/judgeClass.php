<?php
/**
 * Created by PhpStorm.
 * User: lex
 * Date: 3/7/20
 * Time: 15:37
 */

class judgeClass
{
    private $pluginPost;
    public static $category_age = array(
        'male'=>array(
            '1' => 'Юнаки',
            '2' => 'Юнаки 1в.к',
            '3' => 'Юнаки 2в.к',
            '4' => 'Юнаки 3в.к',
            '5' => 'Юніори',
            '6' => 'Чоловіки'),
        'female'=>array(
            '1' => 'Дівчата',
            '2' => 'Дівчата 1в.к',
            '3' => 'Дівчата 2в.к',
            '4' => 'Дівчата 3в.к',
            '5' => 'Юніорки',
            '6' => 'Жінки')
    );

    public static $category_weight = array(
        'male'=>array(
            '1' => '46',
            '2' => '49',
            '3' => '53',
            '4' => '59',
            '5' => '66',
            '6' => '74',
            '7' => '83',
            '8' => '93',
            '9' => '105',
            '10' => '120',
            '11' => '120+'),
        'female'=>array(
            '1' => '40',
            '2' => '43',
            '3' => '47',
            '4' => '52',
            '5' => '57',
            '6' => '63',
            '7' => '72',
            '8' => '84',
            '9' => '84+')
    );

    public static $category_judge = array(
        '1' => 'II',
        '2' => 'I',
        '3' => 'HK',
        '4' => 'MK1',
        '5' => 'MK2'
    );

    public static $position_judge = array('СтС','БС','СУ','Секр','ПС','КС','ГЖ','Ж');

    public function getAge($key=false){
        if($key)
            return self::$category_age[$key];
        else return self::$category_age;
    }

    public function getWeight($key=false){
        if($key)
            return self::$category_weight[$key];
        else return self::$category_weight;
    }

    public function getJudgeCategories($key=false){
        return self::$category_judge;
    }

    function __construct($id = false)
    {
        if (gettype($id)!='integer')
            throw new Exception('Invalid id');

        $post = get_post($id);
        // post not exist
        if ((!$post) || ($post->post_type!="champion"))
            throw new Exception('Invalid id');

        $this->pluginPost = $post;

    }

    public function registerNewJudge($data, $return_bool=true){
        $query_args=array(

            'comment' => $data['comment'],
            'to' => date('Y-m-d',strtotime($data['to'])),
            'from' => date('Y-m-d',strtotime($data['from'])),
            'age_category' => $data['age_category'],
//            'weight_category' => $data['weight_category'],
            'judge_category' => $data['judge_category'],
//            'gender_category' => $data['gender_category'],
	        'age_weight_gender_category' => $data['age_weight_gender_category'],
            'name' => '',
            'location' => $data['city'].','.$data['state']
        );
        // assembly name to one string
        foreach ($data as $key=>$value){
            if ($this->_validateEntry($key,'name')){
                $query_args['name'].=' '.$value;
            }
        }
        return add_post_meta( $this->pluginPost->ID, 'judge_'.time(), json_encode($query_args,JSON_UNESCAPED_UNICODE));

    }

    private function _validateEntry($key,$needle){
        if (strpos($key, $needle) !== false) {
            return true;
        }
        return false;
    }

    public static function calculateJudges($postID){
        global $wpdb;
        $query =
            "select count(*) as 'total' from `".$wpdb->prefix."postmeta` where `meta_key` like 'judge%' and `post_id` = '".intval($postID)."';";
        $result = $wpdb->get_row($query,ARRAY_A);
        return $result['total'];
    }

    public static function getJudgesData($postID){
        global $wpdb;
        $query =
            "select `meta_id` as 'id',`meta_value` as 'value' from `".$wpdb->prefix."postmeta` where `meta_key` like 'judge%' and `post_id` = '".intval($postID)."';";
        $query_result = $wpdb->get_results($query,ARRAY_A);
        $result = array();
        foreach ($query_result as $key=>$value){
            $result[$value['id']]=json_decode($value['value']);
        }
        return $result;

    }

    public static function getJudgesAssignee($postID=0){
        global $wpdb;
        $query = "
        SELECT 
          A.meta_id as id,
          SUBSTRING_INDEX(A.meta_key,'judge_',-1) as judge_id, 
          A.meta_value as register_info, 
          B.meta_value as assigned_info,
          B.meta_id 
        FROM `".$wpdb->prefix."postmeta` A
        left join `".$wpdb->prefix."postmeta` B on
          SUBSTRING_INDEX(A.meta_key,'judge_',-1) = SUBSTRING_INDEX(B.meta_key,'jstatus_',-1)
        where A.meta_key like 'judge_%' and A.post_id=".intval($postID);
        $query_result = $wpdb -> get_results($query,ARRAY_A);
        $result = array();
        foreach ($query_result as $key=>$value){
            $result[$value['id']] = (object) array_merge(
                    json_decode($value['register_info'],true),
                    array(
                        'assigned' => array(
                            'judge_id'      => $value['judge_id'],
                            'meta_id'       => $value['meta_id'],
                            'assigned_info' => json_decode($value['assigned_info'],true)
                        )
                    )
            );

        }
        return $result;
    }

    public static function getCompetitions(){
        global $wpdb;
        $query ="
                    SELECT * from (
                        SELECT ID, post_title as name,(
                            select meta_value from `".$wpdb->prefix."postmeta` 
                            where meta_key = 'date_start' and
                            post_id=ID and
                            date(meta_value) > now()+ interval 40 day) as date_start 
                        FROM `".$wpdb->prefix."posts` where post_type = 'champion' and post_status = 'publish') Z 
                    where Z.`date_start` is not null;
        ";
        return $wpdb->get_results($query,ARRAY_A);
    }

    public function getJudge($id){
    	return get_post_meta( $this->pluginPost->ID, 'judge_'.$id, true );
    }

	public function isJudgeExist($id){
		if (empty($this->getJudge($id)))
			return false;
		return true;
	}

    public function getJudgeSchedule($id){
	    $result = get_post_meta( $this->pluginPost->ID, 'jstatus_'.$id, true );
	    return json_decode($result,true);
    }

    public function setJudgeSchedule($id,$data){
    	$schedule_meta = $this->getJudgeSchedule($id);
    	// meta exist
		if ($schedule_meta){
			if ($data['positionID']===false)
				unset($schedule_meta[$data['dateID']][$data['sectionID']]);
			else
				$schedule_meta[$data['dateID']][$data['sectionID']]=$data['positionID'];
			$data_to_send = $schedule_meta;
		}
		// no meta found
		else{
			$data_to_send = array($data['dateID'] => array($data['sectionID'] => $data['positionID']));
		}
	    $status = update_post_meta(
		    $this->pluginPost->ID,
		    'jstatus_'.$data['judgeID'],
		    json_encode($data_to_send));

		if ($status)
		    return $this->getJudgeSchedule($id);
		return false;
    }
}