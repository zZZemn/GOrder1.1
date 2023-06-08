<?php
    if(isset($_POST['regionID'])){
        include('../database/db.php');
        $regionID = $_POST['regionID'];
        
        $provinces_sql = "SELECT * FROM province WHERE REGION_ID = '$regionID' AND PROVINCE_STATUS = 'active'";
        $provinces_result = $conn->query($provinces_sql);

        if($provinces_result->num_rows > 0){
            $provinces = [];
            while($row = $provinces_result->fetch_assoc()){
                $province = [
                    'provinceID' => $row['PROVINCE_ID'],
                    'province' => $row['PROVINCE']
                ];
                $provinces[] = $province;
            }

            echo json_encode($provinces);
        } else {
            $region_sql = "SELECT REGION FROM region WHERE REGION_ID = '$regionID'";
            $region_result = $conn->query($region_sql);
            if($region_result->num_rows > 0){
                $region = $region_result->fetch_assoc();
                $region_name = $region['REGION'];
            }
            $provinces = [
                [
                    'provinceID' => '',
                    'province' => 'No Province Found in '.$region_name
                ]
            ];
            echo json_encode($provinces);
        }
    } else {
        header('Location: ../index.php');
        exit;
    }