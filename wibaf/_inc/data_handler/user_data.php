<?php
    class UserData {

        /**
         * Gets user metadata
         * @param $user_id (optional) - If specified, returns only data of the specified user.
         * @param $variable (optional) - If specified, returns only the desired variable.
         */
        public function get_user_data($user_id = NULL, $variable = NULL) {
            if (is_null($user_id)) return $this -> get_all_variables_users();
            if (!is_int($user_id)) throw new Exception("Invalid parameter $user_id, integer expected");
            if (is_null($variable)) return $this -> get_all_variables($user_id);
            else return $this -> get_user_variable($user_id, $variable);
        }

        /**
         * Gets all the values for all the different users on the system
         * @param $variable - Name of the variable to be returned
         * @return An array in which the keys are user IDs and the values are value - domain pairs
         */
        public function get_variable_data($variable) {
            $users = $this -> get_users_with_variable();
            $data = array();
            foreach ($users as $user) {
                $id = $user -> ID;
                $data[$id] = $this -> get_user_variable($id, $variable);
                $data[$id] = $data[$id][$variable];
            }
            return $data;
        }

        /**
         * Gets the IDs of all the users existing in the system
         */
        public function get_user_ids() {
            $users = get_users();
            $ids = array();
            foreach ($users as $user) {
                array_push($ids, $user -> ID);
            }
            return $ids;
        }

        /**
         * Gets users that have shared a specific variable
         * @param $variable - The specific variable to search
         */
        protected function get_users_with_variable($variable) {
            return get_users(array(
                'meta_key' => $variable
            ));
        }

        /**
         * Adds a property to an object that exists on an array
         * @param $data - The array in which the property will be added
         * @param $key - The key in which the property will be added
         * @param $value - The value of the property to be added
         * @param $type - The name of the property to be added
         */
        protected function add_variable_to_array(&$data, $key, $value, $type) {
            if (!array_key_exists($key, $data)) {
                $data[$key] = new stdClass;
            }
            $data[$key] -> {$type} = $value;
        }

        /**
         * Gets all users and the variables associated to them
         * @return A matrix with user IDs as first index, variable names as second index and pairs value - domain as cell values
         */
        protected function get_all_variables_users() {
            $ids = $this -> get_user_ids();
            $data = array();
            foreach ($ids as $id) {
                $data[$id] = $this -> get_all_variables($id);
            }
            return $data;
        }

        /**
         * Adds a variable to an array only if this is WiBAF related
         * @param $data - The array in which the data will be added
         * @param $key - The name of the variable in the WordPress database
         * @param $value - The value of the WiBAF variable
         */
        protected function add_wibaf_variable_to_array(&$data, $key, $value) {
            if (startsWith($key, "wibaf_")) {
                $this -> add_variable_to_array($data, substr($key, strlen("wibaf_")), $value, "value");
            } else if (startsWith($key, "domain_wibaf_")) {
                $this -> add_variable_to_array($data, substr($key, strlen("domain_wibaf_")), $value, "domain");
            }
        }

        /**
         * Gets all the variables associated to a single user
         * @param $user_id - The ID of the user that the data is associated with
         * @return An array with variable names as keys and pairs value - domain as values
         */
        protected function get_all_variables($user_id) {
            $user_profile = get_user_meta($user_id);
            $data = array();
            foreach ($user_profile as $key => $value) {
                $this -> add_wibaf_variable_to_array($data, $key, $value[0]);
            }
            return $data;
        }

        /**
         * Gets the value of a variable associated to a user
         * @param $user_id - The ID of the user
         * @param $key - The name of the variable
         * @return An array with a single value, with $key as key and a pair value - domain as value
         */
        protected function get_user_variable($user_id, $key) {
            $data = new stdClass;
            $data -> value = get_user_meta($user_id, "wibaf_" . $key);
            $data -> domain = get_user_meta($user_id, "domain_wibaf_" . $key);
            $data_arr = array();
            $data_arr[$key] = $data;
            return $data_arr;
        }
    }
?>