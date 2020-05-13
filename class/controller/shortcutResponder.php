<?php
/**
 * Created by PhpStorm.
 * User: lex
 * Date: 3/7/20
 * Time: 14:41
 * This class create rest api endpoint for judge register form
 */

class shortcutResponder{
    // Here initialize our namespace and resource name.
    public function __construct() {
        $this->namespace     = 'wp/v2/champion-helper';
        $this->resource_name = '/apply';
    }

    // Register our routes.
    public function register_routes() {
        register_rest_route(
            $this->namespace,
            $this->resource_name,
            array(
                // POST search string
                array(
                    'methods'  => 'POST',
                    'callback' => array($this, 'apply'),
                    'args'     => array(
                        'fpu' => array(
                            'type'     => 'int',
                            'required' => true,
                        ),
                        'comment' => array(
                            'type'     => 'string',
                            'required' => true,
                        ),
                        'to' => array(
                            'type'     => 'string',
                            'required' => true,
                        ),
                        'from' => array(
                            'type'     => 'string',
                            'required' => true,
                        ),
                        'age_weight_gender_category' => array(
                            'type'     => 'array',
                            'required' => true,
                        ),
//                        'weight_category' => array(
//                            'type'     => 'string',
//                            'required' => true,
//                        ),
                        'judge_category' => array(
                            'type'     => 'string',
                            'required' => true,
                        ),
                        'city' => array(
                            'type'     => 'string',
                            'required' => true,
                        ),
                        'state' => array(
                            'type'     => 'string',
                            'required' => true,
                        ),
                        'middle_name' => array(
                            'type'     => 'string',
                            'required' => true,
                        ),
                        'last_name' => array(
                            'type'     => 'string',
                            'required' => true,
                        ),
                        'first_name' => array(
                            'type'     => 'string',
                            'required' => true,
                        ),
                    ),

                ),
            )
        );
    }

    /**
     * Wrapper for Rest_API response
     * @param mixed $data what you want return
     * @param bool $status Status of current request.
     * @param string $message Any message
     * @param int $code http code
     * @return mixed|WP_REST_Response
     */
    private function response($data, $status = true, $message="Success", $code=200){
        $response = rest_ensure_response($data);
        $response->status = $code;
        $response->data=array(
            "code"=> $status,
            "message"=>$message,
            "data"=>array(
                "status"=>$code,
                "result"=>$data,
            ),
        );
        return $response;
    }

    /**
     * Search by string
     * @param $request
     * @return mixed|WP_REST_Response
     */
    public function apply($request){
    	function _unic($arr=array()){
    		$temp=array();
    		foreach ($arr as $key=>$value){
    			if (empty($temp))
    				$temp[]=$value;
    			else {
    				$break_cond=false;
    				foreach ($temp as $t_key=>$t_value)
				    {
				    	if (md5(serialize($value))==md5(serialize($t_value)))
					    {
					    	$break_cond = true;
					    	break;
					    }
				    }
				    if (!$break_cond)
				    	$temp[]=$value;

			    }
		    }
		    return $temp;
	    }

        $user_data = $request->get_params();

        $filter_args=array(
            'fpu' => FILTER_VALIDATE_INT,
            'comment' => FILTER_SANITIZE_STRING,
            'to' => FILTER_SANITIZE_STRING,
            'from' => FILTER_SANITIZE_STRING,
            'age_weight_gender_category' => array(
            	'filter'    => FILTER_SANITIZE_STRING,
	            'flags'     => FILTER_FORCE_ARRAY
            ),
	        #'age_category' => FILTER_VALIDATE_INT,
            #'weight_category' => FILTER_VALIDATE_INT,
            'judge_category' => FILTER_VALIDATE_INT,
            #'gender_category' => FILTER_SANITIZE_STRING,
            'city' => FILTER_SANITIZE_STRING,
            'state' => FILTER_SANITIZE_STRING,
            'middle_name' => FILTER_SANITIZE_STRING,
            'last_name' => FILTER_SANITIZE_STRING,
            'first_name' => FILTER_SANITIZE_STRING,
        );
        $user_data=filter_var_array($user_data,$filter_args,true);
        $user_data["age_weight_gender_category"]=_unic($user_data["age_weight_gender_category"]);
        if (!validateArr($user_data))
            return $this->response('', false, 'Meow Meow', 400);
        try {
            $jC = new judgeClass($user_data['fpu']);
            $jC->registerNewJudge($user_data);
        }
        catch (Exception $e){
            return $this->response($e, false, 'Bad request', 400);
        }

        return $this->response(true);

    }

}