<?php

require 'dbcon.php';


//===================insert=======================//

if(isset($_POST['save_request']))
{

    $name = mysqli_real_escape_string($con, $_POST['name']);

    //department and (group or user)

    $day = mysqli_real_escape_string($con, $_POST['day']);
    $hour = mysqli_real_escape_string($con, $_POST['hour']);
    $priority = mysqli_real_escape_string($con, $_POST['priority']);

    if($name == NULL || $priority == NULL)
    {
        $res = [
            'status' => 422,
            'message' => 'All fields are mandatory'
        ];
        echo json_encode($res);
        return;
    }

    $query = "INSERT INTO requests (name,day,hour,priority) VALUES ('$name','$day','$hour','$priority')";
    $query_run = mysqli_query($con, $query);


    if($query_run)
    {
        $res = [
            'status' => 200,
            'message' => 'Request Created Successfully'
        ];
        echo json_encode($res);
        return;
    }
    else
    {
        $res = [
            'status' => 500,
            'message' => 'Request Not Created'
        ];
        echo json_encode($res);
        return;
    }
}

//-------------------update--------------------//

if(isset($_POST['update_request']))
{
    $request_id = mysqli_real_escape_string($con, $_POST['request_id']);

    $name = mysqli_real_escape_string($con, $_POST['name']);
    $day = mysqli_real_escape_string($con, $_POST['day']);
    $hour = mysqli_real_escape_string($con, $_POST['hour']);
    $priority = mysqli_real_escape_string($con, $_POST['priority']);

    if($name == NULL || $priority == NULL)
    {
        $res = [
            'status' => 422,
            'message' => 'All fields are mandatory'
        ];
        echo json_encode($res);
        return;
    }

    $query = "UPDATE requests SET name='$name', day='$day', hour='$hour', priority='$priority' 
              WHERE id='$request_id'";
    $query_run = mysqli_query($con, $query);

    if($query_run)
    {
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

//-------------------to get request id-----------------------------//

if(isset($_GET['request_id']))
{
    $request_id = mysqli_real_escape_string($con, $_GET['request_id']);

   // $query = "SELECT * FROM requests WHERE id='$request_id'";
  //  $query_run = mysqli_query($con, $query);
  

    $result = mysqli_query($con, "SELECT * FROM requests WHERE id = '$request_id' LIMIT 1");
    $request = $result->fetch_assoc();
    
    
    $result = mysqli_query($con, "SELECT user_id FROM request_user WHERE request_id = '$request_id' ");
    $usersArray[] =[];
    while($row = $result->fetch_assoc()) {
        $usersArray[] = $row['user_id'];
    }

    $request['users'] = $usersArray;
    $request['dep_users'] = $dep_users;
    //if(mysqli_num_rows($query_run) == 1)
   // {
       // $request = mysqli_fetch_array($query_run);

        $res = [
            'status' => 200,
            'message' => 'Request Fetch Successfully by id',
            'data' => $request,
        ];
        echo json_encode($res);
        return;
  //  }
    // else
    // {
    //     $res = [
    //         'status' => 404,
    //         'message' => 'Request Id Not Found'
    //     ];
    //     echo json_encode($res);
    //     return;
    // }
}

//-------------------to change request status--------------------//

$request_id = mysqli_real_escape_string($con, $_GET['id']);
$request_status = mysqli_real_escape_string($con, $_GET['status']);
$updatequery1 = "UPDATE requests SET status=$request_status WHERE id=$request_id";
mysqli_query($con,$updatequery1);
header('location:support.php');

//==========================================//


?>


