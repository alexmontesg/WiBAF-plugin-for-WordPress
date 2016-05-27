<?php
    class History {

        private $user_data_manager;

        function __construct() {
            $this -> user_data_manager = new UserData();
        }

        /**
         * Gets the most popular pages
         * @param $number (optional) - If specified, returns only n pages
         * @return An array of pages in which the keys are names of WiBAF variables
         * and the values the number of times that all the users accessed it
         */
        public function get_popular_pages($number = NULL) {
            $all_data = $this -> user_data_manager -> get_user_data();
            $pages = array();
            foreach ($all_data as $id => $user_data) {                
                $this -> add_user_pages($pages, $user_data);
            }
            $pages = $this -> sort_and_slice($pages, $number);
            return $pages;
        }

        /**
         * Recommends links to users
         * @param $user_id - The ID of the user that will get the recommendations
         * @param $number (optional) - The number of recommendations to return (default = 5)
         * @param $neighbours (optional) - The closest neighbours that provide recommendations (default = 5)
         * @return An array of pages in which the keys are names of WiBAF variables
         * and the values are the predicted suitability
         */
        public function recommend_links_to_user($user_id, $number = 5, $neighbours = 5) {
            $users = array();
            $pages = array();
            $this -> build_matrix_user_pages($users, $pages);
            $similarity = $this -> calculate_similarity($user_id, $users);
            $similarity = $this -> sort_and_slice($similarity, $neighbours);
            $rated_by_neighbours = array();
            foreach ($similarity as $u_id => $correlation) {
                $this -> add_user_prediction($rated_by_neighbours, $users[$u_id], $neighbours);
            }
            return $this -> calculate_predictions($rated_by_neighbours, $users[$user_id], $number, $pages);
        }

        protected function calculate_predictions($rated_by_neighbours, $user, $number, $pages) {
            $predictions = array_diff_key($rated_by_neighbours, $user);
            $predictions = $this -> sort_and_slice($predictions, $number);
            return $this -> page_id_to_page($pages, $predictions);
        }

        protected function page_id_to_page($pages, $predictions) {
            $new_predictions = array();
            foreach ($predictions as $page_id => $value) {
                $new_predictions[$pages[$page_id]] = $value;
            }
            return $new_predictions;
        }

        protected function add_user_prediction(&$rated_by_neighbours, $user, $neighbours) {
            foreach ($user as $page_id => $visits) {
                if(isset($rated_by_neighbours[$page_id])) {
                    $rated_by_neighbours[$page_id] += $visits / $neighbours;
                } else {
                    $rated_by_neighbours[$page_id] = $visits / $neighbours;
                }
            }
        }

        protected function calculate_similarity($user_id, $users) {
            $user_avg = array_sum($users[$user_id]) / count($users[$user_id]);
            $similarity = array();
            foreach ($users as $u_id => $u_pages) {
                if($u_id !== $user_id) {
                    $similarity[$u_id] = $this -> calculate_user_similarity($users[$user_id], $users[$u_id], $user_avg);
                }
            }
            return $similarity;
        }

        protected function calculate_user_similarity($user_1, $user_2, $user_avg) {
            $a = $b = $c = 0;
            $u_avg = array_sum($user_2) / count($user_2);
            $item_set = array_intersect_key($user_1, $user_2);
            foreach ($item_set as $key => $value) {
                $a += ($user_1[$key] - $user_avg) * ($user_2[$key] - $u_avg);
                $b += pow(($user_1[$key] - $user_avg), 2);
                $c += pow(($user_2[$key] - $u_avg), 2);
            }
            return $a / sqrt($b * $c);
        }

        protected function build_matrix_user_pages(&$users, &$pages) {
            $all_data = $this -> user_data_manager -> get_user_data();
            foreach ($all_data as $id => $user_data) {                
                $this -> add_user_to_matrix($users, $pages, $id, $user_data);
            }
        }

        protected function add_user_to_matrix(&$users, &$pages, $id, $user_data) {
            foreach ($user_data as $name => $obj) {
                if($obj -> domain === "access") {
                    $this -> add_page_to_matrix($users, $id, $name, $pages, $obj -> value);
                }
            }
        }

        protected function add_page_to_matrix(&$users, $id, $name, &$pages, $rating) {
            if(!isset($users[$id])) {
                $users[$id] = array();
            }
            $page_id = array_search($name, $pages);
            if(!($page_id || $page_id === 0)) {
                $page_id = count($pages);
                $pages[$page_id] = $name;
            }
            $users[$id][$page_id] = $rating;
        }

        protected function sort_and_slice($arr, $len = NULL, $start = 0) {
            arsort($arr);
            if(is_int($len) and $len < count($arr) and is_int($start)) {
                $arr = array_slice($arr, $start, $len);
            }
            return $arr;
        }

        protected function add_user_pages(&$pages, $user_data) {
            foreach ($user_data as $name => $obj) {
                if($obj -> domain === "access") {
                    $this -> add_page($pages, $name, $obj -> value);
                }
            }
        }

        protected function add_page(&$pages, $name, $value) {
            if (!array_key_exists($name, $pages)) {
                $pages[$name] = $value;
            }
            $pages[$name] = $pages[$name] + $value;
        }

    }
?>