<?php
require 'dbcon.php';
header('Content-type: Application/json');

//===================select users=======================//
if(isset($_GET['get_users']))
{
    return getUsers();    
}
//====================get request======================//
// if(isset($_GET['request_id']))
// {
//     getRequest();    
// }
if(isset($_GET['request_id']))
{
    $request_id = mysqli_real_escape_string($con, $_GET['request_id']);

    $request_result = mysqli_query($con, "SELECT * FROM requests WHERE id = '$request_id' LIMIT 1");
    $request = $request_result->fetch_assoc();
    
    if(is_null($request)){        
        $res = [
            'status' => 404,
            'message' => 'Request Id Not Found'
        ];
        echo json_encode($res);
        return;
    }
    

    $user_ids_result = mysqli_query($con, "SELECT user_id FROM request_user WHERE request_id = '$request_id' ");
    $user_ids_array = [];
    while($row = $user_ids_result->fetch_assoc()) {
        array_push($user_ids_array, $row['user_id']);
    }
    $user_ids_text = implode(",", $user_ids_array);
    

    $dep_ids_result = mysqli_query($con, "SELECT departments.id FROM users INNER JOIN departments ON departments.id = users.department_id WHERE users.id IN ($user_ids_text) GROUP BY departments.id");    
    $dep_ids_array = [];
    while($row = $dep_ids_result->fetch_assoc()) {        
        array_push($dep_ids_array, $row['id']);
    }
    $dep_ids_text = implode(",", $dep_ids_array);


    $dep_users = getUsers($dep_ids_array);

    $request['users'] = $user_ids_array;    
    $request['dep_users'] = $dep_users;
    $request['dep_ids'] = $dep_ids_array;

    $res = [
        'status' => 200,
        'message' => 'Request Fetch Successfully by id',
        'data' => $request,
    ];
     echo json_encode($res);
    return;

}
//=====================insert request==================//
if(isset($_POST['save_request']))
{
  return  saveRequest();
}
//=====================update request==================//
if(isset($_POST['update_request']))
{
  return  updateRequest();
}


function updateRequest(){
    require 'dbcon.php';
    $request_id = mysqli_real_escape_string($con, $_POST['request_id']);
    
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $day = mysqli_real_escape_string($con, $_POST['day']);
    $hour = mysqli_real_escape_string($con, $_POST['hour']);
    $priority = mysqli_real_escape_string($con, $_POST['priority']);
    $users_ids = $_POST['users_ids'];

    if($name == NULL || $priority == NULL)
    {
        $res = [
            'status' => 422,
            'message' => 'All fields are mandatory'
        ];
        echo json_encode($res);
        return;
    }

    $query = "UPDATE requests SET name='$name', day='$day', hour='$hour', priority='$priority' WHERE id='$request_id'";
    $query_run = mysqli_query($con, $query);

    if($query_run)
    {
        $query_run = mysqli_query($con, "DELETE FROM request_user WHERE request_id ='$request_id'");
        for ($i = 0; $i < count($users_ids); $i++) {
            $query = "INSERT INTO request_user (user_id,request_id) VALUES ('$users_ids[$i]','$request_id')";
            $query_run = mysqli_query($con, $query);
        }

        $res = [
            'status' => 200,
            'message' => 'Request Updated Successfully'
        ];
        echo json_encode($res);
        return;
    }
    else
    {
        $res = [
            'status' => 500,
            'message' => 'Request Not Updated'
        ];
        echo json_encode($res);
        return;
    }
}

function getRequest()
{

}





function saveRequest(){
    require 'dbcon.php';
    
    if(!isset($_POST['name'])|| !isset($_POST['priority']))    
    {
        $res = [
            'status' => 422,
            'message' => 'All fields are mandatory'
        ];
        echo json_encode($res);
        return;
    }
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $day = mysqli_real_escape_string($con, $_POST['day']);
    $hour = mysqli_real_escape_string($con, $_POST['hour']);
    $priority = mysqli_real_escape_string($con, $_POST['priority']);
    
    $users_ids = $_POST['users_ids'];
    if($name == NULL || $priority == NULL)
    {
        $res = [
            'status' => 422,
            'message' => 'All fields are mandatory'
        ];
        echo json_encode($res);
        return;
    }

    $query = "INSERT INTO requests (name,day,hour,priority,status) VALUES ('$name','$day','$hour','$priority',1)";

    $query_run1 = mysqli_query($con, $query);

    $last_id = mysqli_insert_id($con);

    for ($i = 0; $i < count($users_ids); $i++) {
        $query = "INSERT INTO request_user (user_id,request_id) VALUES ('$users_ids[$i]','$last_id')";
        $query_run = mysqli_query($con, $query);
    }

    if($query_run1)
    {
        $result = mysqli_query($con, "SELECT * FROM requests ORDER BY id DESC LIMIT 1");
        $request = $result->fetch_assoc();
        echo json_encode($request);    
        return;
    }else{
        $res = [
            'status' => 500,
            'message' => 'Request Not Created'
        ];
        echo json_encode($res);
        return;
    }
}


function getUsers($dep_ids = null)
{
    require 'dbcon.php';
    $department_ids =  isset($_GET['dep_ids']) ?  $_GET['dep_ids'] : $dep_ids;
    
    $array = implode("','",$department_ids);
    //echo $array. "xx";
    $myArray = array();
    $result = mysqli_query($con, "SELECT id,name FROM users WHERE department_id IN ('".$array."')");
    while($row = $result->fetch_assoc()) {
        $myArray[] = $row;
    }
    if(isset($_GET['dep_ids'])){
        echo json_encode($myArray);
    }
    return $myArray;    

}



// //-------------------update--------------------//

// if(isset($_POST['update_request']))
// {
//     $request_id = mysqli_real_escape_string($con, $_POST['request_id']);

//     $name = mysqli_real_escape_string($con, $_POST['name']);
//     $day = mysqli_real_escape_string($con, $_POST['day']);
//     $hour = mysqli_real_escape_string($con, $_POST['hour']);
//     $priority = mysqli_real_escape_string($con, $_POST['priority']);

//     if($name == NULL || $priority == NULL)
//     {
//         $res = [
//             'status' => 422,
//             'message' => 'All fields are mandatory'
//         ];
//         echo json_encode($res);
//         return;
//     }

//     $query = "UPDATE support SET name='$name', day='$day', hour='$hour', priority='$priority' 
//               WHERE id='$request_id'";
//     $query_run = mysqli_query($con, $query);

//     if($query_run)
//     {
//         $res = [
//             'status' => 200,
//             'message' => 'Request Updated Successfully'
//         ];
//         echo json_encode($res);
//         return;
//     }
//     else
//     {
//         $res = [
//             'status' => 500,
//             'message' => 'Request Not Updated'
//         ];
//         echo json_encode($res);
//         return;
//     }
// }

// //-------------------to get request id-----------------------------//

// if(isset($_GET['request_id']))
// {
//     $request_id = mysqli_real_escape_string($con, $_GET['request_id']);

//     $query = "SELECT * FROM support WHERE id='$request_id'";
//     $query_run = mysqli_query($con, $query);
  

//     if(mysqli_num_rows($query_run) == 1)
//     {
//         $request = mysqli_fetch_array($query_run);

//         $res = [
//             'status' => 200,
//             'message' => 'Request Fetch Successfully by id',
//             'data' => $request,
//         ];
//         echo json_encode($res);
//         return;
//     }
//     else
//     {
//         $res = [
//             'status' => 404,
//             'message' => 'Request Id Not Found'
//         ];
//         echo json_encode($res);
//         return;
//     }
// }

// //-------------------to change request status--------------------//

// $request_id = mysqli_real_escape_string($con, $_GET['id']);
// $request_status = mysqli_real_escape_string($con, $_GET['request_status']);
// $updatequery1 = "UPDATE support SET request_status=$request_status WHERE id=$request_id";
// mysqli_query($con,$updatequery1);
// header('location:support.php');

// //==========================================//

?>


