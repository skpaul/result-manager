
<?php 
    ini_set('max_execution_time', 0);
    require_once("Required.php");
   
    Required::SwiftLogger()
    ->ZeroSQL()
    ->SwiftDatetime();
   
    $logger = new SwiftLogger(ROOT_DIRECTORY);
    $db = new ZeroSQL();
    $db->Server(DATABASE_SERVER)->Password(DATABASE_PASSWORD)->Database(DATABASE_NAME)->User(DATABASE_USER_NAME);
    $db->connect();


   

    function executeQuery($conn, $sql){
        // $logger = new SwiftLogger(ROOT_DIRECTORY);
        // $logger->createLog($sql);
        $result = $conn->query($sql);
        if(!$result){
            trigger_error($conn->error);
        }
        else{
            return $result;
        }
    }

    function executeSelectQuery($conn, $sql){
        $result = $conn->query($sql);
        return $result;
    }


    function createChoiceRequirementTable($conn){
        $sql = "SHOW TABLES LIKE 'choice_requirement'";
        $result= executeQuery($conn, $sql);
        $numRows = $result->num_rows;
        if ($numRows == 0){
            $sql = "CREATE TABLE `choice_requirement` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `user_id` varchar(20) NOT NULL,
                `choice_required` int(11) NOT NULL,
                `found` int(11) NOT NULL, 
                PRIMARY KEY (`id`) )";

            executeQuery($conn, $sql);    
        }
    }

    function truncateChoiceRequirement($conn){
        executeQuery($conn,"truncate table choice_requirement" );
    }

    function createChoicesTable($conn){
        $sql = "SHOW TABLES LIKE 'choices'";
        $result= executeQuery($conn, $sql);
        $numRows = $result->num_rows;
        if ($numRows == 0){
            $sql = "CREATE TABLE `choices`(
                    `id` int(11) NOT NULL  AUTO_INCREMENT,
                    `user_id` varchar(20) NOT NULL,
                    `requisition_id` int(11) NOT NULL,
                    `is_selected` BOOLEAN NOT NULL DEFAULT FALSE,
                    `post_location` VARCHAR(50) NOT NULL,
                     PRIMARY KEY (`id`))";
                    executeQuery($conn, $sql);    
        }
    }

    function truncateChoices($conn){
        executeQuery($conn,"truncate table choices" );
    }

    function truncatePostCalculations($conn){
        executeQuery($conn,"truncate table choices" );
    }

    function createDivStatTable($conn){
        $sql = "SHOW TABLES LIKE 'divstat'";
        $result= executeQuery($conn, $sql);
        $numRows = $result->num_rows;
        if ($numRows > 0){
            $sql = "drop table `divstat`";
            executeQuery($conn, $sql);    
        }

        $sql = "create TABLE divstat SELECT DISTINCT app_div_code,app_div_name,app_dist_code,app_dist_name,app_thana_code, app_thana_name, post_id_u,post_name,sub_id_u, sub_name from applicants";
        executeQuery($conn, $sql);    
    }

     /**
     * returnRow()
     * 
     * It returns single table row using fetch_object().
     * 
     * fetch_assoc() is 3X faster than other methods.
     * 
     */
    function returnRow($conn, $sql){
        $result = executeQuery($conn, $sql);
        $row = $result->fetch_object();
        $result->free_result();
        return $row;
    }

    /**
     * returnRows()
     * 
     * It returns array of table rows using fetch_assoc().
     * 
     * fetch_assoc() is 3X faster than other methods.
     * 
     */
    function returnRows($conn, $sql){
        $rows= [];
        $result = executeQuery($conn, $sql);
        while ($row = $result->fetch_object()) {
            $rows[] = $row;
        }
        $result->free_result();
        return $rows;
    }

    function getDivisions($conn){
        // $sql = "select distinct app_div_code as divCode, app_div_name as divName from applicants where app_div_code='2' order by divName";
        $sql = "select distinct app_div_code as divCode, app_div_name as divName from applicants order by divName";

        return returnRows($conn, $sql);
    }

    function getDistricts($conn,$divCode){
        //$sql = "select distinct app_dist_code as distCode, app_dist_name as distName from applicants where app_div_code = '$divCode' and app_dist_code='406' order by distName";
        $sql = "select distinct app_dist_code as distCode, app_dist_name as distName from applicants where app_div_code = '$divCode'  order by distName";
        return returnRows($conn, $sql);
    }

    //distinctThanaFromApplicants
    function getThanas($conn,$divCode, $distCode){
        // $sql = "select distinct app_thana_code as thanaCode, app_thana_name as thanaName from applicants where app_div_code='$divCode' and app_dist_code = '$distCode' and app_thana_code='40616' order by thanaName";
        $sql = "select distinct app_thana_code as thanaCode, app_thana_name as thanaName from applicants where app_div_code='$divCode' and app_dist_code = '$distCode' order by thanaName";
        return returnRows($conn, $sql);
    }

    //distinct Post From Applicant Thana
    function getPosts($conn,$divCode, $distCode, $thanaCode){
        // $sql = "select distinct post_id_u as postId, post_name as postName from applicants where app_div_code='$divCode' and app_dist_code = '$distCode' and app_thana_code= '$thanaCode' and post_id_u='30' order by postName";
        $sql = "select distinct post_id_u as postId, post_name as postName from applicants where app_div_code='$divCode' and app_dist_code = '$distCode' and app_thana_code= '$thanaCode' order by postName";
        return returnRows($conn, $sql);
    }

    function getSubjects($conn,$divCode, $distCode, $thanaCode, $postId){
       // $sql = "select distinct sub_id_u as subId, sub_name as subName from applicants where app_div_code='$divCode' and app_dist_code = '$distCode' and app_thana_code= '$thanaCode' and post_id_u='$postId' and sub_id_u='319' order by subName";
        $sql = "select distinct sub_id_u as subId, sub_name as subName from applicants where app_div_code='$divCode' and app_dist_code = '$distCode' and app_thana_code= '$thanaCode' and post_id_u='$postId' order by subName";
        $rows= [];
        $result = $conn->query($sql);
        while ($row = $result->fetch_object()) {
            $rows[] = $row;
        }
        return $rows;
    }

    /**
     * getApplicants()
     * 
     * @return array of objects containing user_id and gender
     */
    function getApplicants($conn,$divCode, $distCode, $thanaCode, $postId, $subId, $gender){
        $sql = "select user_id, gender from applicants where app_div_code='$divCode' and app_dist_code = '$distCode' and app_thana_code= '$thanaCode' and post_id_u='$postId' and sub_id_u='$subId' and gender = $gender and chosen_quantity < 5 order by marks, ranks, dob";
       
        return returnRows($conn, $sql);
    }

    function suggestPost_OLD($conn,$divCode, $distCode, $thanaCode, $postId, $subId){
        $sql = "select id from `posts` where div_code='$divCode' and dist_code = '$distCode' and thana_code= '$thanaCode' and post_id='$postId' and sub_id='$subId' and mpo=1 and has_chosen = 0 order by eiin limit 1";
       
        return returnRow($conn, $sql);
    }

    function suggestPost($conn,$divCode, $distCode, $thanaCode, $postId, $subId, $gender){
        $whereClause = " div_code='$divCode' and dist_code = '$distCode' and thana_code= '$thanaCode' and post_id='$postId' and sub_id='$subId' and mpo='1' and has_chosen = 0";
     
        if($gender == 1){
            $whereClause .= " and post_sex= 0 ";
            $postSql =  "select id from `posts` where  $whereClause order by eiin limit 1";
            $result = $conn->query($postSql);
            $row = $result->fetch_object();
            return $row;
        }

        if($gender == 2){
            $tempSql = $whereClause . " and post_sex= 2 ";
            $postSql =  "select id from `posts` where $tempSql order by eiin limit 1";
            $result = $conn->query($postSql);
            $row = $result->fetch_object();

            if ($row == NULL) {
                //try without female quota. If not found then try with female quota.
                $tempSql = $whereClause . " and post_sex= 0 ";
                $postSql =  "select id from `posts` where $tempSql order by eiin limit 1";
                $result = $conn->query($postSql);
                $row = $result->fetch_object();
            }

            return $row;
        }
       
    }

    function choosePost($conn, $userId, $postId, $postLocation){
        $sql = "select user_id from choices where user_id == '$userId'";
        $result = $conn->query($sql);
        $quantity = $result->num_rows;
        if($quantity<5){
            $sql = "insert into choices(user_id, requisition_id, post_location) values('$userId', '$postId', '$postLocation');";
            executeQuery($conn,$sql);
            $sql = "update applicants set chosen_quantity = chosen_quantity + 1 where user_id = '$userId';";
            executeQuery($conn,$sql);
            $sql = "update posts set has_chosen = 1 where id = '$postId';";
            executeQuery($conn,$sql);
        }
    }

    function configureColumns($con){
        // $result = mysql_query("SHOW COLUMNS FROM `table` LIKE 'fieldname'");
        // $exists = (mysql_num_rows($result))?TRUE:FALSE;
        // if($exists) {
        // // do your stuff
        // }

        $sql = "SHOW COLUMNS FROM `applicants` LIKE 'chosen_quantity'";
        $result = executeQuery($con, $sql);
        if($result->num_rows == 0){
            $sql = "ALTER TABLE `applicants` ADD `chosen_quantity` INT NOT NULL DEFAULT '0' FIRST;";
            executeQuery($con, $sql);
        }
        else{
            $sql = "update `applicants` set `chosen_quantity` = 0";
            executeQuery($con, $sql);
        }
       
        $sql = "SHOW COLUMNS FROM `posts` LIKE 'has_chosen'";
        $result = executeQuery($con, $sql);
        if($result->num_rows == 0){
            $sql = "ALTER TABLE `posts` ADD `has_chosen` BOOLEAN NOT NULL DEFAULT FALSE FIRST;";
            executeQuery($con, $sql);
        }
        else{
            $sql = "update `posts` set `has_chosen` = 0";
            executeQuery($con, $sql);
        }
    }

    //eiin can not be duplicate
    function hasApplicantAndPost_duplicate_eiin($conn,$divCode, $distCode, $thanaCode, $postId, $subId, $gender){
        $isFound = false;
        $sql = "select user_id from applicants where  app_div_code='$divCode' and app_dist_code = '$distCode' and app_thana_code= '$thanaCode' and post_id_u='$postId' and sub_id_u='$subId' and gender = $gender and chosen_quantity < 5 order by marks, ranks, dob ";

        $result = executeQuery($conn, $sql);
        $applicantsQuantity = $result->num_rows;
        if ($applicantsQuantity == 0) {
            return false;
        }

        $postQuantity = 0;
        $whereClause = " div_code='$divCode' and dist_code = '$distCode' and thana_code= '$thanaCode' and post_id='$postId' and sub_id='$subId' and mpo='1' and has_chosen = 0";
     
        if($gender == 1){
            $whereClause .= " and post_sex= 0 ";
            $postSql =  "select id from `posts` where  $whereClause";

            $result = executeQuery($conn, $postSql);
            $postQuantity = $result->num_rows;
        }

        if($gender == 2){
            $tempSql = $whereClause . " and post_sex= 2 ";
            $postSql =  "select id from `posts` where $tempSql";
            $result = executeQuery($conn, $postSql);
            $postQuantity = $result->num_rows;
            if ($postQuantity == 0) {
                //try without female quota. If not found then try with female quota.
                $tempSql = $whereClause . " and post_sex= 0 ";
                $postSql =  "select id from `posts` where  $tempSql";
                $result = executeQuery($conn, $postSql);
                $postQuantity = $result->num_rows;
            }
        }
       

        if($applicantsQuantity>0 && $postQuantity>0){
            $isFound = true;
        }

        return $isFound;
    }

    function hasApplicantAndPost($conn,$divCode, $distCode, $thanaCode, $postId, $subId, $gender){
        //$isFound = false;
        $sql = "select user_id from applicants where  app_div_code='$divCode' and app_dist_code = '$distCode' and app_thana_code= '$thanaCode' and post_id_u='$postId' and sub_id_u='$subId' and gender = $gender and chosen_quantity < 5 order by marks, ranks, dob LIMIT 1";

        $result = executeQuery($conn, $sql);
        $applicantsQuantity = $result->num_rows;
        if ($applicantsQuantity == 0) {
            return NULL;
        }

        $row = $result->fetch_object();
        $userId = $row->user_id;
        $notIn = " and eiin NOT IN (select p.eiin from choices c INNER JOIN applicants a on c.user_id=a.user_id INNER JOIN posts p on c.requisition_id = p.id WHERE c.user_id ='$userId') ";

        $orderBy = " order by eiin ";

        $postQuantity = 0;
        $whereClause = " div_code='$divCode' and dist_code = '$distCode' and thana_code= '$thanaCode' and post_id='$postId' and sub_id='$subId' and mpo='1' and has_chosen = 0";
     
        if($gender == 1){
            $whereClause .= " and post_sex= 0 ";
            //select id from `posts` where  div_code='1' and dist_code = '506' and thana_code= '50602' and post_id='120' and sub_id='429' and mpo='1' and has_chosen = 0 and post_sex= 0

            //select id from `posts` where  div_code='1' and dist_code = '506' and thana_code= '50602' and post_id='120' and sub_id='429' and mpo='1' and has_chosen = 0 and post_sex= 0 and eiin NOT IN (select p.eiin from choices c INNER JOIN applicants a on c.user_id=a.user_id INNER JOIN posts p on c.requisition_id = p.id WHERE c.user_id ='A1013687D7')

            

            $postSql =  "select id from `posts` where  $whereClause $notIn $orderBy LIMIT 1";

            $result = executeQuery($conn, $postSql);
            $postQuantity = $result->num_rows;
            if ($postQuantity == 0) {
                return NULL;
            }

            if ($postQuantity == 1) {
                $post = $result->fetch_object();
                return array($userId, $post->id);
            }          
        }

        if($gender == 2){
            $tempSql = $whereClause . " and post_sex= 2 ";
            $postSql =  "select id from `posts` where $tempSql $notIn $orderBy LIMIT 1";
            $result = executeQuery($conn, $postSql);
            $postQuantity = $result->num_rows;
            if ($postQuantity == 1) {
                $post = $result->fetch_object();
                return array($userId, $post->id);
            }
            if ($postQuantity == 0) {
                //try without female quota. If not found then try with female quota.
                $tempSql = $whereClause . " and post_sex= 0 ";
                $postSql =  "select id from `posts` where  $tempSql $notIn $orderBy LIMIT 1";
                $result = executeQuery($conn, $postSql);
                $postQuantity = $result->num_rows;
                if ($postQuantity == 1) {
                    $post = $result->fetch_object();
                    return array($userId, $post->id);
                }
                if ($postQuantity == 0) {
                   return NULL;
                }
            }
        }
       
        return NULL;
    }

    function recursive($con, $division, $district, $thana, $post, $subject){
        $FemaleFound = hasApplicantAndPost($con, $division->divCode, $district->distCode, $thana->thanaCode,$post->postId, $subject->subId, 2);
        if($FemaleFound != NULL){
            $userId = $FemaleFound[0];
            $postId = $FemaleFound[1];
            choosePost($con, $userId, $postId, "thana");
        }
       
        $MaleFound = hasApplicantAndPost($con, $division->divCode, $district->distCode, $thana->thanaCode,$post->postId, $subject->subId, 1);
        if($MaleFound != NULL){
            $userId = $MaleFound[0];
            $postId = $MaleFound[1];
            choosePost($con, $userId, $postId, "thana");
        }

        if ($FemaleFound == NULL && $MaleFound == NULL) {
            //No need to do anything.
        }
        else {
            recursive($con, $division, $district, $thana, $post, $subject);
        }
    }

    function recursive_duplicate_eiin($con, $division, $district, $thana, $post, $subject){
        $isFemaleFound = hasApplicantAndPost($con, $division->divCode, $district->distCode, $thana->thanaCode,$post->postId, $subject->subId, 2);
        if($isFemaleFound){
           //get female applicants 
           $applicants = getApplicants($con, $division->divCode, $district->distCode, $thana->thanaCode, $post->postId, $subject->subId,2);
           foreach ($applicants as $applicant) {
               $suggestedPost = suggestPost($con, $division->divCode, $district->distCode, $thana->thanaCode,$post->postId, $subject->subId, 2);
               if ($suggestedPost != NULL) {
                   choosePost($con, $applicant->user_id, $suggestedPost->id, "thana");
               }
           }
        }
       
        $isMaleFound = hasApplicantAndPost($con, $division->divCode, $district->distCode, $thana->thanaCode,$post->postId, $subject->subId, 1);
        if($isMaleFound){
            //get male applicants 
            $applicants = getApplicants($con, $division->divCode, $district->distCode, $thana->thanaCode,$post->postId, $subject->subId,1);
            foreach ($applicants as $applicant) {
                $suggestedPost = suggestPost($con, $division->divCode, $district->distCode, $thana->thanaCode,$post->postId, $subject->subId, 1);
                if ($suggestedPost != NULL) {
                    choosePost($con, $applicant->user_id, $suggestedPost->id, "thana");
                }
            }
        }

        if ($isFemaleFound || $isMaleFound) {
            recursive($con, $division, $district, $thana, $post, $subject);
        }
    }



    function suggestFromOwnThana($con){

        $divisions = getDivisions($con); // get distinct divisions from applicants table.

        //division loop starts
        foreach ($divisions as $division) {
            $districts = getDistricts($con, $division->divCode);
            //district loop starts
            foreach ($districts as $district) {
                $thanas = getThanas($con, $division->divCode, $district->distCode);
                //thana loop starts
                foreach ($thanas as $thana) {
                    $posts = getPosts($con, $division->divCode, $district->distCode, $thana->thanaCode);
                    //post loop starts
                    foreach ($posts as $post) {
                        $subjects = getSubjects($con, $division->divCode, $district->distCode, $thana->thanaCode,$post->postId);
                        //subject loop starts
                        foreach ($subjects as $subject) {
                            recursive($con, $division, $district, $thana, $post, $subject);
                        }
                        //subject loop ends

                    }
                    //post loop starts

                }
                //thana loop starts
                

                
            }
            //district loop ends
            
        }
        //division loop ends
    }


    //first try $gender = 2 (female), then $gender
    function suggestFromAdjacentThanas($counter, $gender, $conn){
        $sql = "SELECT user_id FROM `applicants` where chosen_quantity = $counter and gender=$gender order by marks, ranks, dob"; //gender in applicant = 1 or 2
        $result = $conn->query($sql);
        
        $applicants = [];
        while ($row = $result->fetch_object()) {
            $applicants[] = $row;
        }

        if(count($applicants)>0){
            foreach ($applicants as $applicantDetails) {
                $sql = "select * from `applicants` where user_id = '$applicantDetails->user_id'";
                $applicant = returnRow($conn, $sql);

                if($gender == 1){
                    $sqlPosts = "select id from `posts` where post_sex= 0 and div_code='$applicant->app_div_code' and dist_code ='$applicant->app_dist_code' and thana_code <>  '$applicant->app_thana_code' and post_id='$applicant->post_id_u' and sub_id='$applicant->sub_id_u' and mpo='1' and has_chosen = 0 order by eiin LIMIT 1";
                    $posts = returnRows($conn, $sqlPosts);
                }

                if($gender == 2){
                    //first, try without female quota. If not found then try with female quota.
                    $sqlPosts = "select id from `posts` where post_sex= 0 and div_code='$applicant->app_div_code' and dist_code ='$applicant->app_dist_code' and thana_code <> '$applicant->app_thana_code' and post_id='$applicant->post_id_u' and sub_id='$applicant->sub_id_u' and mpo='1' and has_chosen = 0 order by eiin LIMIT 1";
                                                        
                    $posts = returnRows($conn, $sqlPosts);

                    if(count($posts) == 0){
                        $sqlPosts = "select id from `posts` where post_sex= 2 and div_code='$applicant->app_div_code' and dist_code ='$applicant->app_dist_code' and thana_code <> '$applicant->app_thana_code' and post_id='$applicant->post_id_u' and sub_id='$applicant->sub_id_u' and mpo='1' and has_chosen = 0 order by eiin LIMIT 1";

                        $posts = returnRows($conn, $sqlPosts);
                    }
                }
              
                if(count($posts) > 0){
                    $post= $posts[0];
                    choosePost($conn, $applicant->user_id, $post->id, "district");
                }
                else{
                    suggestFromAdjacentDistricts($applicant, $gender, $conn);
                }
            }
            
        }//female candidates
    }

    function checkFromAdjacentThanas($counter, $conn){
        //first check female
        suggestFromAdjacentThanas($counter, 2, $conn);
        suggestFromAdjacentThanas($counter, 1, $conn);

        $counter++;
        if($counter < 5){
            checkFromAdjacentThanas($counter, $conn);
        }
    }


    function suggestFromAdjacentDistricts($applicant, $gender, $conn){
       // $sql = "select * from `applicants` where user_id = '$user_id'";
       // $applicant = returnRow($conn, $sql);

        if($gender == 1){
            $sqlPosts = "select id from `posts` where post_sex= 0 and div_code='$applicant->app_div_code' and dist_code <>'$applicant->app_dist_code'  and post_id='$applicant->post_id_u' and sub_id='$applicant->sub_id_u' and mpo='1' and has_chosen = 0 order by eiin LIMIT 1";
            $posts = returnRows($conn, $sqlPosts);
        }

        if($gender == 2){
            //first, try without female quota. If not found then try with female quota.
            $sqlPosts = "select id from `posts` where post_sex= 0 and div_code='$applicant->app_div_code' and dist_code <> '$applicant->app_dist_code' and post_id='$applicant->post_id_u' and sub_id='$applicant->sub_id_u' and mpo='1' and has_chosen = 0 order by eiin LIMIT 1";
                                                
            $posts = returnRows($conn, $sqlPosts);

            if(count($posts) == 0){
                $sqlPosts = "select id from `posts` where post_sex= 2 and div_code='$applicant->app_div_code' and dist_code <>'$applicant->app_dist_code' and post_id='$applicant->post_id_u' and sub_id='$applicant->sub_id_u' and mpo='1' and has_chosen = 0 order by eiin LIMIT 1";

                $posts = returnRows($conn, $sqlPosts);
            }
        }
    
        if(count($posts) > 0){
            $post= $posts[0];
            choosePost($conn, $applicant->user_id, $post->id, "division");
        }
        else{
            suggestFromAdjacentDivisions($applicant, $gender, $conn);
        }
    }

    function suggestFromAdjacentDivisions($applicant, $gender, $conn){
        // $sql = "select * from `applicants` where user_id = '$user_id'";
        // $applicant = returnRow($conn, $sql);

        if($gender == 1){
            $sqlPosts = "select id from `posts` where post_sex= 0 and div_code<>'$applicant->app_div_code'  and post_id='$applicant->post_id_u' and sub_id='$applicant->sub_id_u' and mpo='1' and has_chosen = 0 order by eiin LIMIT 1";
            $posts = returnRows($conn, $sqlPosts);
        }

        if($gender == 2){
            //first, try without female quota. If not found then try with female quota.
            $sqlPosts = "select id from `posts` where post_sex= 0 and div_code<>'$applicant->app_div_code' and post_id='$applicant->post_id_u' and sub_id='$applicant->sub_id_u' and mpo='1' and has_chosen = 0 order by eiin LIMIT 1";
                                                
            $posts = returnRows($conn, $sqlPosts);

            if(count($posts) == 0){
                $sqlPosts = "select id from `posts` where post_sex= 2 and div_code<>'$applicant->app_div_code' and post_id='$applicant->post_id_u' and sub_id='$applicant->sub_id_u' and mpo='1' and has_chosen = 0 order by eiin LIMIT 1";

                $posts = returnRows($conn, $sqlPosts);
            }
        }
    
        if(count($posts) > 0){
            $post= $posts[0];
            choosePost($conn, $applicant->user_id, $post->id, "all");
        }

    }

    function post(){
        //select 1 post where district quota > district quota found
        
        //-->if district quota found
            //select applicant who has claimed for district quota
            
        //if district quota found<-----
    }

    try {

        $db->truncate("post_calculation");

        //select all posts 
        $posts = $db->select()->from("posts")->toList();

        foreach ($posts as $post) {
            $vacancies = $post->vacancies;
            $distPercentage = $post->districtQuota;
            $femalePercentage = $post->femaleQuota;
            $freedomFighterPercentage = $post->freedomFighterQuota;
            $tribalPercentage = $post->tribalQuota;
            
            echo $vacancies;
        }

        // $con = mysqli_connect("localhost", "root", "", "ntrca");
        // createChoicesTable($con);
        // truncateChoices($con);
        // configureColumns($con); 

        // createDivStatTable($con);

        // suggestFromOwnThana($con);

        $db->close();
        echo "<br>done";


    } catch (\Exception $exp) {
        $logger->createLog($exp->getMessage());
        echo "Problem while showing data. A log has been created.";
    }


?>
