<?php
require '../../database/db.php';
require '../../time-date.php';

function error422($message)
{
    $data = [
        'status' => 422,
        'message' => $message,
    ];
    header("HTTP/1.0 422 Unprocessable Entity");
    return json_encode($data);
}

function login($email, $password)
{
    global $conn;
    global $currentDate;
    global $currentTime;

    if ($email && $password == NULL) {
        return error422('Enter email and password');
    } else {
        $sql = "SELECT * FROM employee WHERE EMAIL = '$email' OR USERNAME = '$email'";
        $result =  $conn->query($sql);
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['PASSWORD'])) {
                if ($user['EMP_STATUS'] === 'active' && $user['EMP_TYPE'] === 'Admin' || $user['EMP_TYPE'] === 'Rider') {
                    $emp_id = $user['EMP_ID'];
                    $login_log = "INSERT INTO `emp_log`(`EMP_ID`, `LOG_TYPE`, `LOG_DATE`, `LOG_TIME`) 
                                            VALUES ('$emp_id','Login (Rider App)','$currentDate','$currentTime')";
                    if ($conn->query($login_log) === TRUE) {
                        $data = [
                            'status' => 200,
                            'message' => 'Login Success',
                            'emp_type' => $user['EMP_TYPE'],
                            'data' => $emp_id,
                        ];
                        header("HTTP/1.0 200 OK");
                        return json_encode($data);
                    } else {
                        $data = [
                            'status' => 404,
                            'message' => 'Login Failed',
                        ];
                        header("HTTP/1.0 200 Something Wrong");
                        return json_encode($data);
                    }
                    
                } else {
                    $data = [
                        'status' => 404,
                        'message' => 'Login Failed',
                    ];
                    header("HTTP/1.0 404 Not Found");
                    return json_encode($data);
                }
            } else {
                $data = [
                    'status' => 404,
                    'message' => 'Login Failed',
                ];
                header("HTTP/1.0 404 Not Found");
                return json_encode($data);
            }
        } else {
            $data = [
                'status' => 404,
                'message' => 'Login Failed',
            ];
            header("HTTP/1.0 404 Not Found");
            return json_encode($data);
        }
    }
}
