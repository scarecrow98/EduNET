<?php

/*


*/ 

    class StatX{

        public static function testInstancesCount(){
            $db = Database::getInstance();

            $stmt = $db->prepare(
                "SELECT COUNT(id) AS 'sum' FROM test_instances"                
            );
            $stmt->execute();
            return $stmt->fetch()['sum'];
        }

        public static function taskCount(){
            $db = Database::getInstance();

            $stmt = $db->prepare(
                "SELECT COUNT(id) AS 'sum' FROM tasks"                
            );
            $stmt->execute();
            return $stmt->fetch()['sum']; 
        }

        public static function studentCount(){
            $db = Database::getInstance();

            $stmt = $db->prepare(
                "SELECT COUNT(id) AS 'sum' FROM users WHERE type = ?"                
            );
            $stmt->execute(array(0));
            return $stmt->fetch()['sum']; 
        }

        public static function teacherCount(){
            $db = Database::getInstance();

            $stmt = $db->prepare(
                "SELECT COUNT(id) AS 'sum' FROM users WHERE type = ?"                
            );
            $stmt->execute(array(1));
            return $stmt->fetch()['sum']; 
        }

        public static function groupCount(){
            $db = Database::getInstance();

            $stmt = $db->prepare(
                "SELECT COUNT(id) AS 'sum' FROM groups"                
            );
            $stmt->execute();
            return $stmt->fetch()['sum']; 
        }

        public static function resultsByTestInstance($test_instance_id){
            $db = Database::getInstance();

            $stmt = $db->prepare(
                "SELECT user_id, task_id, result FROM task_results WHERE test_instance_id = ?"
            );
            $stmt->execute(array($test_instance_id));
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $results = array();
            foreach( $data as $d ){
                $r = array(
                    'task_id'   => $d['task_id'],
                    'result'    => $d['result'],
                );

                $results[$d['user_id']][] = $r;
            }

            $points = array();

            foreach( $results as $user_id => $result ){
                $total_points = 0;
                $student_points = 0;

                foreach( $result as $r ){
                    $split = explode('/', $r['result']);
                    $total_points += $split[0];
                    $student_points += $split[1];
                }

                $points[$user_id] = $total_points.'/'.$student_points;
            }
            echo StatX::avgResult($points);
            return $points;
        }

        public static function minResult($arr){
            $first_elem = reset($arr);
            $min = explode('/', $first_elem)[1];

            foreach( $arr as $a ){
                $act = explode('/', $a)[1];

                if( $act < $min ) $min = $act;
            }
            return $min;
        }

        public static function maxResult($arr){
            $first_elem = reset($arr);
            $max = explode('/', $first_elem)[1];

            foreach( $arr as $a ){
                $act = explode('/', $a)[1];

                if( $act > $max ) $max = $act;
            }
            return $max;
        }

        public static function avgResult($arr){
            $sum = 0;

            foreach( $arr as $a ){
                $point = explode('/', $a)[1];
                $sum += $point;
            }

            return ($sum / count($arr));
        }

    }
?>