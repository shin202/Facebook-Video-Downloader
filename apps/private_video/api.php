<?php

    require 'get_video_info.php';


    // Check File 
    try
    {
        $file = isset($_FILES['file']) ? $_FILES['file'] : '';


        if(empty($file))
        {
            header('HTTP/1.0 404 Not Found');
            throw new Exception("Please Choose File.");
        }
        else
        {
            $tmp = $file['name'];
            if(!preg_match('/^(?:[\w]+)(\.txt)$/', $tmp))
            {
                header('HTTP/1.0 404 Not Found');
                throw new Exception("Invalid File.");
            }
        }
        

        // Return JSON
        header('Content-Type: application/json');


        $tmp = $file['tmp_name'];
        $data = file_get_contents($tmp);
        $get_info = new get_info;
        $response = $get_info->get_response($data);


        echo json_encode($response);
    }

    
    // Throw Error
    catch(Throwable $err)
    {
        $msg['error'] = $err->getMessage();
        echo json_encode($msg);
    }
?>