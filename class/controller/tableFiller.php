<?php
/**
 * Created by PhpStorm.
 * User: lex
 * Date: 3/7/20
 * Time: 14:41
 * This class create rest api endpoint for judge selection
 */

class tableFiller{
	// Here initialize our namespace and resource name.
	public function __construct() {
		$this->namespace     = 'wp/v2/champion-helper';
		$this->resource_name = '/set';
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
						'ID' => array(
							'type'     => 'int',
							'required' => true,
						),
						'judgeID' => array(
							'type'     => 'int',
							'required' => true,
						),
						'dateID' => array(
							'type'     => 'string',
							'required' => true,
						),
						'sectionID' => array(
							'type'     => 'int',
							'required' => true,
						),
						'positionID' => array(
							'type'     => 'string',
							'required' => true,
						),

					),
					'permission_callback' => 'is_user_logged_in',
				),
                // PUT search string
                array(
                    'methods'  => 'PUT',
                    'callback' => array($this, 'apply_age'),
                    'args'     => array(
                        'ID' => array(
                            'type'     => 'int',
                            'required' => true,
                        ),
                        'dateID' => array(
                            'type'     => 'string',
                            'required' => true,
                        ),
                        'age_categoryID' => array(
                            'type'     => 'int',
                            'required' => true,
                        ),
                        'sectionID' => array(
                            'type'     => 'int',
                            'required' => true,
                        ),

                    ),
                    'permission_callback' => 'is_user_logged_in',
                ),
                // PATCH search string
                array(
                    'methods'  => 'PATCH',
                    'callback' => array($this, 'apply_weight'),
                    'args'     => array(
                        'ID' => array(
                            'type'     => 'int',
                            'required' => true,
                        ),
                        'dateID' => array(
                            'type'     => 'string',
                            'required' => true,
                        ),
                        'weight_categoryID' => array(
                            'type'     => 'int',
                            'required' => true,
                        ),
                        'sectionID' => array(
                            'type'     => 'int',
                            'required' => true,
                        ),

                    ),
                    'permission_callback' => 'is_user_logged_in',
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
		$user_data = $request->get_params();

		$filter_args=array(
			'ID' => FILTER_VALIDATE_INT,
			'judgeID' => FILTER_VALIDATE_INT,
			'sectionID' => FILTER_VALIDATE_INT,
			'positionID' => FILTER_VALIDATE_INT,
			'dateID' => FILTER_SANITIZE_STRING,

		);
		$user_data=filter_var_array($user_data,$filter_args,false);

		// validate wrong date mark
		if (!$user_data['dateID'])
			return $this->response('', false, 'Wrong date', 400);



		try {
			$judge = new judgeClass($user_data['ID']);
			if (!$judge->isJudgeExist($user_data['judgeID']))
				return $this->response('', false, 'Bad request', 400);

			// ok judge exist
			$result =$judge->setJudgeSchedule($user_data['judgeID'],$user_data);
			if ($result)
				return $this->response($result);
			else
				return $this->response('', false, 'Bad request', 400);
		}
		catch (Exception $e){
			return $this->response($e, false, 'Bad request', 400);
		}

		return $this->response(true);

	}

    public function apply_age($request){
        $user_data = $request->get_params();

        var_dump($user_data);
        die();

        $filter_args=array(
            'ID' => FILTER_VALIDATE_INT,
            'judgeID' => FILTER_VALIDATE_INT,
            'sectionID' => FILTER_VALIDATE_INT,
            'positionID' => FILTER_VALIDATE_INT,
            'dateID' => FILTER_SANITIZE_STRING,

        );
        $user_data=filter_var_array($user_data,$filter_args,false);

        // validate wrong date mark
        if (!$user_data['dateID'])
            return $this->response('', false, 'Wrong date', 400);



        try {
            $judge = new judgeClass($user_data['ID']);
            if (!$judge->isJudgeExist($user_data['judgeID']))
                return $this->response('', false, 'Bad request', 400);

            // ok judge exist
            $result =$judge->setJudgeSchedule($user_data['judgeID'],$user_data);
            if ($result)
                return $this->response($result);
            else
                return $this->response('', false, 'Bad request', 400);
        }
        catch (Exception $e){
            return $this->response($e, false, 'Bad request', 400);
        }

        return $this->response(true);

    }

    public function apply_weight($request){
        $user_data = $request->get_params();

        var_dump($user_data);
        die();

        $filter_args=array(
            'ID' => FILTER_VALIDATE_INT,
            'judgeID' => FILTER_VALIDATE_INT,
            'sectionID' => FILTER_VALIDATE_INT,
            'positionID' => FILTER_VALIDATE_INT,
            'dateID' => FILTER_SANITIZE_STRING,

        );
        $user_data=filter_var_array($user_data,$filter_args,false);

        // validate wrong date mark
        if (!$user_data['dateID'])
            return $this->response('', false, 'Wrong date', 400);



        try {
            $judge = new judgeClass($user_data['ID']);
            if (!$judge->isJudgeExist($user_data['judgeID']))
                return $this->response('', false, 'Bad request', 400);

            // ok judge exist
            $result =$judge->setJudgeSchedule($user_data['judgeID'],$user_data);
            if ($result)
                return $this->response($result);
            else
                return $this->response('', false, 'Bad request', 400);
        }
        catch (Exception $e){
            return $this->response($e, false, 'Bad request', 400);
        }

        return $this->response(true);

    }
}