<?php
    if(isset($_POST['provinceID'])){
        include('../database/db.php');
        $provinceID = $_POST['provinceID'];
        
        $municipality_sql = "SELECT * FROM municipality WHERE PROVINCE_ID = '$provinceID' AND MUNICIPALITY_STATUS = 'active' ORDER BY MUNICIPALITY";
        $municipality_result = $conn->query($municipality_sql);

        if($municipality_result->num_rows > 0){
            $municipalities = [];
            while($row = $municipality_result->fetch_assoc()){
                $municipality = [
                    'municipalityID' => $row['MUNICIPALITY_ID'],
                    'municipality' => $row['MUNICIPALITY']
                ];
                $municipalities[] = $municipality;
            }

            echo json_encode($municipalities);
        } else {
            $province_sql = "SELECT PROVINCE FROM province WHERE PROVINCE_ID = '$provinceID'";
            $province_result = $conn->query($province_sql);
            if($province_result->num_rows > 0){
                $province = $province_result->fetch_assoc();
                $province_name = $province['PROVINCE'];
            }
            $municipalities = [
                [
                    'municipalityID' => '',
                    'municipality' => 'No Municipality Found in '.$province_name
                ]
            ];
            echo json_encode($municipalities);
        }
    } else {
        header('Location: ../index.php');
        exit;
    }