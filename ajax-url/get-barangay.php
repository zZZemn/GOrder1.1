<?php
    if(isset($_POST['municipalityID'])){
        include('../database/db.php');
        $municipalityID = $_POST['municipalityID'];
        
        $barangay_sql = "SELECT * FROM barangay WHERE MUNICIPALITY_ID = '$municipalityID' AND BARANGAY_STATUS = 'active'";
        $barangay_result = $conn->query($barangay_sql);

        if($barangay_result->num_rows > 0){
            $barangays = [];
            while($row = $barangay_result->fetch_assoc()){
                $barangay = [
                    'barangayID' => $row['BARANGAY_ID'],
                    'barangay' => $row['BARANGAY']
                ];
                $barangays[] = $barangay;
            }

            echo json_encode($barangays);
        } else {
            $municipality_sql = "SELECT MUNICIPALITY FROM municipality WHERE MUNICIPALITY_ID = '$municipalityID'";
            $municipality_result = $conn->query($municipality_sql);
            if($municipality_result->num_rows > 0){
                $municipality = $municipality_result->fetch_assoc();
                $municipality_name = $municipality['MUNICIPALITY'];
            }
            $barangays = [
                [
                    'barangayID' => '',
                    'barangay' => 'No Barangay Found in '.$municipality_name
                ]
            ];
            echo json_encode($barangays);
        }
    } else {
        header('Location: ../index.php');
        exit;
    }