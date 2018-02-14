<?php

    class Mailer{

        private $recipient;
        private $subject;
        private $sender;
        private $html;

        private $headers = "MIME-Version: 1.0\r\nContent-Type: text/html; charset=UTF-8\r\n";

        //data -> student_id, sender_name, subject, 
        public function __construct($data){
            $this->recipient = User::get( $data['user_id'] );
            $this->subject = $data['subject'];
            
            $test_instance = TestInstance::get( $data['test_instance_id'] );
            $this->sender;
        }

        public function sendMail(){
            $data = TestInstance::getEvaluatedInstances($current_user->id);
            foreach( $data as $d ){
                $test_instance = TestInstance::get($d['test_instance_id']);
                $test = Test::get($test_instance->test_id);
                $tasks = Task::getByTest($test->id);
                $max_points = 0;
                $user_points = 0;
                
                foreach( $tasks as $task ){
                    $result = Task::getResult($task->id, $test_instance->id, $current_user->id);
                    $split = explode('/', $result['result']);
                    $max_points += $split[0];
                    $user_points += $split[1];
                }
            }
        }
    }

?>