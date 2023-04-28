<?php 

require '../database/db.php';
require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

function error422($message) {
    $data = [ 
        'status' => 422,
        'message' => $message,
    ];
    header("HTTP/1.0 422 Unprocessable Entity");
    return json_encode($data);
}

function login($email, $password){

    global $conn;

    if($email && $password == NULL)
    {
        return error422('Enter email and password');
    }
    else
    {
        $sql = "SELECT * FROM customer_user WHERE EMAIL = '$email' OR USERNAME = '$email'";
        $result =  $conn->query($sql);

        if($result->num_rows > 0)
        {
            $user = $result->fetch_assoc();
            if(password_verify($password, $user['PASSWORD']))
            {
                if($user['STATUS'] === 'Active')
                {
                    $data = [
                        'status' => 200,
                        'message' => 'Login Success',
                        'data' => $user['CUST_ID'],
                    ];
                    header("HTTP/1.0 200 OK");
                    return json_encode($data);
                }
                else
                {
                    $data = [
                        'status' => 404,
                        'message' => 'Login Failed',
                    ];
                    header("HTTP/1.0 404 Not Found");
                    return json_encode($data);
                }
            }
            else
            {
                $data = [
                    'status' => 404,
                    'message' => 'Login Failed',
                ];
                header("HTTP/1.0 404 Not Found");
                return json_encode($data);
            }
        }
        else
        {
            $data = [
                'status' => 404,
                'message' => 'Login Failed',
            ];
            header("HTTP/1.0 404 Not Found");
            return json_encode($data);
        }
    }
}

function signup($fname, $lname, $mi, $suffix, $sex, $email, $username, $password, $contact_no, $unit_street, $barangay, $municipality, $province, $region, $birthday) {

    global $conn;

    if($fname == null || $lname == null)
    {
        return error422($fname." ".$lname);
    }

    elseif($email == null)
    {
        return error422('Please Enter your Email');
    }
    
    elseif($username == null)
    {
        return error422('Please Enter Username');
    }

    elseif($password == null)
    {
        return error422('Please Enter Password');
    }

    elseif($contact_no == null)
    {
        return error422('Please Enter you Contact number');
    }
    
    elseif($contact_no == null)
    {
        return error422('Please Enter you Contact number');
    }

    elseif($unit_street == null || $barangay == null || $municipality == null || $province == null || $region == null)
    {
        return error422('Please Enter your full address');
    }

    elseif($birthday == null)
    {
        return error422('Please Enter your Birthdate');
    }

    else
    {
        $check_email = "SELECT * FROM customer_user WHERE EMAIL = '$email'";
        $check_email_result =  $conn->query($check_email);

        $check_username = "SELECT * FROM customer_user WHERE USERNAME = '$username'";
        $check_username_result = $conn->query($check_username);

        if($check_email_result->num_rows > 0)
        {
            $data = [
                'status' => 404,
                'message' => 'Existing email',
            ];
            header("HTTP/1.0 404 Existing email");
            return json_encode($data);
        }

        elseif($check_username_result->num_rows > 0)
        {
            $data = [
                'status' => 404,
                'message' => 'Existing username',
            ];
            header("HTTP/1.0 404 Existing username");
            return json_encode($data);
        }

        else
        {
            $verification_code = rand(100000, 999999);

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $cust_id = rand(100000, 999999);
            $cust_id_result = $conn->query("SELECT * FROM customer_user WHERE CUST_ID = $cust_id");
        
            while($cust_id_result->num_rows > 0)
            {
                $cust_id = rand(100000, 999999);
                $cust_id_result = $conn->query("SELECT * FROM customer_user WHERE CUST_ID = $cust_id");
            }

            $insert_customer = "INSERT INTO `customer_user`(`CUST_ID`, `FIRST_NAME`, `LAST_NAME`, `MIDDLE_INITIAL`, `SUFFIX`, `SEX`, `EMAIL`, `USERNAME`, `PASSWORD`, `CONTACT_NO`, `UNIT_STREET`, `BARANGAY`, `MUNICIPALITY`, `PROVINCE`, `REGION`, `BIRTHDAY`, `CUSTOMER_TYPE`, `STATUS`) 
                                VALUES ('$cust_id','$fname','$lname','$mi','$suffix','$sex','$email','$username','$hashed_password','$contact_no','$unit_street','$barangay','$municipality','$province','$region','$birthday','Regular','Active')";

            if($conn->query($insert_customer))
            {
                $data = [
                    'status' => 200,
                    'message' => 'Registered!'
                ];
                header("HTTP/1.0 200 OK");
                return json_encode($data);
            }            
            else
            {
                $data = [
                    'status' => 200,
                    'message' => 'Registration failed!'
                ];
                header("HTTP/1.0 200 OK");
                return json_encode($data);
            }
        }
    }
  }

  function products($cust_id_search){
    global $conn;

    if($cust_id_search['id'] == null)
    {
        return error422('Enter Customer ID');
    }
    else
    {
        $id = $cust_id_search['id'];

        $cheking_cust_id = "SELECT * FROM customer_user WHERE CUST_ID = $id";
        $cheking_cust_id_result = $conn->query($cheking_cust_id);

        if($cheking_cust_id_result->num_rows > 0)
        {
            if(isset($cust_id_search['pro_search']))
            {
                if($cust_id_search['pro_search'] != null)
                {
                    //search here
                    $pro_search = $cust_id_search['pro_search'];
                    $product_search = "SELECT * FROM products WHERE PRODUCT_NAME LIKE '%$pro_search%'";
                    $product_search_result = $conn->query($product_search);

                    $product_search_data = [];

                    if($product_search_result->num_rows > 0)
                    {
                        while($row = $product_search_result->fetch_assoc())
                        {
                            $product_search_data[] = [
                                    'product_id' => $row['PRODUCT_ID'],
                                    'product_code' => $row['PRODUCT_CODE'],
                                    'product_name' => $row['PRODUCT_NAME'],
                                    'unit_measurement' => $row['UNIT_MEASUREMENT'],
                                    'selling_price' => $row['SELLING_PRICE'],
                                    'cat_id' => $row['CAT_ID'],
                                    'subcat_id' => $row['SUB_CAT_ID'],
                                    'description' => $row['DESCRIPTION'],
                                    'critical_level' => $row['CRITICAL_LEVEL'],
                                    'product_img' => $row['PRODUCT_IMG'],
                                    'prescribe' => $row['PRESCRIBE'],
                                    'vatable' => $row['VATABLE']
                            ];
                        }
                        $data = [
                            'status' => 200,
                            'message' => 'Product Fetch Success',
                            'data' => $product_search_data
                        ];
                        header("HTTP/1.0 200 OK");
                        return json_encode($data);
                    }
                    else
                    {
                        $data = [
                            'status' => 404,
                            'message' => 'No product found',
                        ];
                        header("HTTP/1.0 404 No product found");
                        return json_encode($data);
                    }
                }
                else
                {
                $products = "SELECT * FROM products";
                $products_result = $conn->query($products);

                $products_data = [];

                    if($products_result->num_rows > 0)
                    {
                        while($row = $products_result->fetch_assoc())
                        {
                            $products_data[] = [
                                    'product_id' => $row['PRODUCT_ID'],
                                    'product_code' => $row['PRODUCT_CODE'],
                                    'product_name' => $row['PRODUCT_NAME'],
                                    'unit_measurement' => $row['UNIT_MEASUREMENT'],
                                    'selling_price' => $row['SELLING_PRICE'],
                                    'cat_id' => $row['CAT_ID'],
                                    'subcat_id' => $row['SUB_CAT_ID'],
                                    'description' => $row['DESCRIPTION'],
                                    'critical_level' => $row['CRITICAL_LEVEL'],
                                    'product_img' => $row['PRODUCT_IMG'],
                                    'prescribe' => $row['PRESCRIBE'],
                                    'vatable' => $row['VATABLE']
                            ];
                        }

                        $data = [
                            'status' => 200,
                            'message' => 'Product Fetch Success',
                            'data' => $products_data
                        ];
                        header("HTTP/1.0 200 OK");
                        return json_encode($data);

                    }
                    else
                    {
                        $data = [
                            'status' => 404,
                            'message' => 'No product found',
                        ];
                        header("HTTP/1.0 404 No product found");
                        return json_encode($data);
                    }
                }
            }

            else
            {
                $products = "SELECT * FROM products";
                $products_result = $conn->query($products);

                $products_data = [];

                if($products_result->num_rows > 0)
                {
                    while($row = $products_result->fetch_assoc())
                    {
                        $products_data[] = [
                                'product_id' => $row['PRODUCT_ID'],
                                'product_code' => $row['PRODUCT_CODE'],
                                'product_name' => $row['PRODUCT_NAME'],
                                'unit_measurement' => $row['UNIT_MEASUREMENT'],
                                'selling_price' => $row['SELLING_PRICE'],
                                'cat_id' => $row['CAT_ID'],
                                'subcat_id' => $row['SUB_CAT_ID'],
                                'description' => $row['DESCRIPTION'],
                                'critical_level' => $row['CRITICAL_LEVEL'],
                                'product_img' => $row['PRODUCT_IMG'],
                                'prescribe' => $row['PRESCRIBE'],
                                'vatable' => $row['VATABLE']
                        ];
                    }

                    $data = [
                        'status' => 200,
                        'message' => 'Product Fetch Success',
                        'data' => $products_data
                    ];
                    header("HTTP/1.0 200 OK");
                    return json_encode($data);

                }
                else
                {
                    $data = [
                        'status' => 404,
                        'message' => 'No product found',
                    ];
                    header("HTTP/1.0 404 No product found");
                    return json_encode($data);
                }
            }
        }
        else
        {
                $data = [
                    'status' => 405,
                    'message' => 'Access Deny',
                ];
                header("HTTP/1.0 405 Access Deny");
                echo json_encode($data);
        }
    }
  }
?>