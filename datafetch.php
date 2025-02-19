<?php

        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRue);


        include('../controller/database/database_interface.php');
        $Insert_db_object=new database_interface();

      

        // nuber of uploading student
        $counter=0;
        //first row of data series
    
        require_once 'Classes/PHPExcel.php';

        
        /** Load $inputFileName to a Spreadsheet Object  **/
        $excel = PHPExcel_IOFactory::load($_FILES['excel']['tmp_name']);
        $i = 4;

        //load excel file using PHPExcel's IOFactory
        //change filename to tmp_name of uploaded file

        //set active sheet to first sheet
        $excel->setActiveSheetIndex(0);
        
                
     
        //loop until the end of data series(cell contains empty string)
        while( $excel->getActiveSheet()->getCell('A'.$i)->getValue() != ""){
            //get cells value
            $fullname =		$excel->getActiveSheet()->getCell('A'.$i)->getValue();
            $sex =	$excel->getActiveSheet()->getCell('B'.$i)->getValue();
            $program_type = $excel->getActiveSheet()->getCell('C'.$i)->getValue();
            $accept_year =	$_POST['current_year'];
            $current_year = $_POST['current_year'];
            $university_number = uniqid();
            $college_id =  $_POST['college_id'];
            $department =  $_POST['department'];
            $smister_id =   $_POST['current_simster'];
            $school	= " ";                                               
        
            $vouture_mount = 0;


            $SQL_statement="INSERT INTO `student`(`fullname`, `university_number`, `sex`,`program_id`, `dep_id`, `college_id`,`accept_year`, `DeleteMark`, `graduation_flag`)
                VALUES ('$fullname','$university_number','$sex','$program_type','$department','$college_id','$accept_year',0,0)";
                echo "<br>".$SQL_statement;
            if( $Insert_db_object->insert_operation($SQL_statement)){

                    $Get_std_id="SELECT `std_id` FROM `student` WHERE `fullname`='$fullname' and `university_number`='$university_number' and `sex`='$sex'
                    and `program_id`='$program_type' and `dep_id`='$department' and `college_id`='$college_id'";
                    $Get_std_id_db_object=$Insert_db_object->select_operation($Get_std_id);
                    $Get_std_id_data=$Get_std_id_db_object->fetch_assoc();
                    $std_id=$Get_std_id_data['std_id'];

                    $batch_address_record="INSERT INTO `address`(`std_id`, `cellPhone`, `CurrentLacation`, `state`, `originCity`, `Email`) VALUES ($std_id,'','','','','')";

                    $batch_family_record="INSERT INTO `family`(`std_id`, `fatherCondition`, `motherCondition`, `motherName`, `number_breathers`, `motherJop`, `motherEducation`
                        , `numberSisters`,`familySupportName`, `familySupportJop`, `familySupportSalary`, `no_univerityStudent`, `no_basicStudent`, `no_underAge`, `mother_salary`)
                        VALUES ($std_id,'','','',0,'','',0,'','',0,0,0,0,0)";
                
                $batch_healthcare_record="INSERT INTO `healthcare`(`std_id`, `medicailCondition`, `specialCare`, `seriseDesise`, `comment`) VALUES ($std_id,'','','','')";
                
                $batch_notionality_record="INSERT INTO `notionality` (`std_id`, `natiolity_number`, `Outplace`, `outdate`, `typeNationlity`, `nationityBy`, `birthday`, `socialStatus`, `placeOfBirth`)
                    VALUES ($std_id, '', '', '0000-01-01', '', '', '0000-01-01', '', '')";
                
                $batch_schools_record="INSERT INTO `schools` (`std_id`, `schoolName`, `ceritificateName`, `cerificateType`, `certificatedate`, `certificateSpecialist`, `sittingNumber`)
                    VALUES ('$std_id', '$school', '', '', '0000-01-01', '', '')";
                
                $batch_student_smister__record="INSERT INTO `student_smister_record`(`std_id`, `year`, `smister_id`
                    ,`current_smister_flag`, `paid_mount`, `unpaid_mount`, `record_comment`, `payment_type`
                    , `payment_type_mount`, `registration_flag`, `active_simster_flag`)
                    VALUES ('$std_id', '$current_year', '$smister_id', 1, 0, 0, '', 1,$vouture_mount,0,1)";

                    echo "<br>".$batch_student_smister__record;
                    
                    


                    if($Insert_db_object->insert_operation($batch_address_record) and $Insert_db_object->insert_operation($batch_family_record)	and
                    $Insert_db_object->insert_operation($batch_healthcare_record)	and $Insert_db_object->insert_operation($batch_notionality_record)
                    and $Insert_db_object->insert_operation($batch_schools_record)	and $Insert_db_object->insert_operation($batch_student_smister__record) 
                        ){

                                $counter++;                                                         

                    }
                    else{

                        $roll_student_record="DELETE FROM `student` WHERE `std_id`=$std_id";
                        $roll_student_smister_record="DELETE FROM `student_smister_record` WHERE `std_id`=$std_id";
                        $roll_schools_record="DELETE FROM `schools` WHERE `std_id`=$std_id";
                        $roll_healthcare_record="DELETE FROM `healthcare` WHERE `std_id`=$std_id";
                        $roll_family_record="DELETE FROM `family` WHERE `std_id`=$std_id";
                        $roll_notionality_record="DELETE FROM `notionality` WHERE `std_id`=$std_id";
                        $roll_address_record="DELETE FROM `address` WHERE `std_id`=$std_id";
                        


                        $Insert_db_object->delete_operation($roll_student_record);
                        $Insert_db_object->delete_operation($roll_student_smister_record);
                        $Insert_db_object->delete_operation($roll_schools_record);
                        $Insert_db_object->delete_operation($roll_healthcare_record);
                        $Insert_db_object->delete_operation($roll_family_record);
                        $Insert_db_object->delete_operation($roll_notionality_record);
                        $Insert_db_object->delete_operation($roll_address_record);
                        

                    }
                        
            }

            $i++;
        }

        $_SESSION['LOCATION']="dashboard.php?Flag=Login";
        header('Location:../dashboard.php?Flag=Login&Messege=true');
?>
