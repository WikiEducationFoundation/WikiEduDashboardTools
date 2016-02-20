<?php

require_once 'common.php';

$query = "
SELECT user_id
FROM user WHERE user_name = $user_name
";

echo_query_results($query);
